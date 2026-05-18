<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;

class PaymentService
{
    /**
     * eSewa Initiate
     */
    public function initiateEsewa(Order $order): array
    {
        $transactionUuid = $order->id . '-' . time();
        $totalAmount = $order->total;
        $productCode = env('ESEWA_PRODUCT_CODE', 'EPAYTEST');
        $secretKey = env('ESEWA_SECRET_KEY', '8gBm/:&EnhH.1/q');

        $signedFieldNames = 'total_amount,transaction_uuid,product_code';
        $signatureString = "total_amount={$totalAmount},transaction_uuid={$transactionUuid},product_code={$productCode}";
        
        $signature = base64_encode(hash_hmac('sha256', $signatureString, $secretKey, true));

        return [
            'amount' => $order->subtotal,
            'tax_amount' => 0,
            'total_amount' => $totalAmount,
            'transaction_uuid' => $transactionUuid,
            'product_code' => $productCode,
            'product_service_charge' => 0,
            'product_delivery_charge' => $order->shipping_fee,
            'success_url' => route('payment.esewa.success'),
            'failure_url' => route('payment.esewa.failure'),
            'signed_field_names' => $signedFieldNames,
            'signature' => $signature,
            'url' => env('ESEWA_PAYMENT_URL', 'https://rc-epay.esewa.com.np/api/epay/main/v2/form'),
        ];
    }

    /**
     * eSewa Verify
     */
    public function verifyEsewa(string $encodedData)
    {
        $decodedJson = base64_decode($encodedData);
        $data = json_decode($decodedJson, true);

        if (!$data || !isset($data['signature']) || !isset($data['transaction_uuid'])) {
            return false;
        }

        $secretKey = env('ESEWA_SECRET_KEY', '8gBm/:&EnhH.1/q');

        // Recreate signature
        $signedFieldNames = explode(',', $data['signed_field_names']);
        $signatureString = '';
        foreach ($signedFieldNames as $field) {
            $signatureString .= "{$field}={$data[$field]},";
        }
        $signatureString = rtrim($signatureString, ',');

        $expectedSignature = base64_encode(hash_hmac('sha256', $signatureString, $secretKey, true));

        if ($expectedSignature !== $data['signature']) {
            return false;
        }

        // Optional status check endpoint
        // eSewa v2 endpoint: https://rc-epay.esewa.com.np/api/epay/transaction/status/
        $statusCheckUrl = str_replace('/main/v2/form', '/transaction/status/', env('ESEWA_PAYMENT_URL', 'https://rc-epay.esewa.com.np/api/epay/main/v2/form'));
        
        $response = Http::get($statusCheckUrl, [
            'product_code' => $data['product_code'],
            'total_amount' => $data['total_amount'],
            'transaction_uuid' => $data['transaction_uuid'],
        ]);

        if ($response->successful()) {
            $statusData = $response->json();
            if (isset($statusData['status']) && $statusData['status'] === 'COMPLETE') {
                return $data;
            }
        } elseif ($data['status'] === 'COMPLETE') {
            // Fallback if status endpoint fails but signature is valid and status is COMPLETE
            return $data;
        }

        return false;
    }

    /**
     * Khalti Initiate
     */
    public function initiateKhalti(Order $order): string
    {
        $secretKey = env('KHALTI_SECRET_KEY', 'your_khalti_test_secret');
        $initiateUrl = env('KHALTI_INITIATE_URL', 'https://a.khalti.com/api/v2/epayment/initiate/');

        // amount in paisa
        $amountInPaisa = $order->total * 100;

        $response = Http::withHeaders([
            'Authorization' => "key {$secretKey}",
        ])->post($initiateUrl, [
            'return_url' => route('payment.khalti.success'),
            'website_url' => url('/'),
            'amount' => $amountInPaisa,
            'purchase_order_id' => (string) $order->id,
            'purchase_order_name' => "GrocerEase Order #{$order->id}",
            'customer_info' => [
                'name' => auth()->user()->name,
                'email' => auth()->user()->email,
            ]
        ]);

        if ($response->successful()) {
            $data = $response->json();
            return $data['pidx'];
        }

        throw new \Exception('Khalti initiate failed: ' . $response->body());
    }

    /**
     * Khalti Verify
     */
    public function verifyKhalti(string $pidx): bool
    {
        $secretKey = env('KHALTI_SECRET_KEY', 'your_khalti_test_secret');
        $verifyUrl = env('KHALTI_VERIFY_URL', 'https://a.khalti.com/api/v2/epayment/lookup/');

        $response = Http::withHeaders([
            'Authorization' => "key {$secretKey}",
        ])->post($verifyUrl, [
            'pidx' => $pidx,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            return isset($data['status']) && $data['status'] === 'Completed';
        }

        return false;
    }
}
