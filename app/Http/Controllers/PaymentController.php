<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    // ── eSewa ───────────────────────────────────────────────────────────────

    public function initiateEsewa(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $formData = $this->paymentService->initiateEsewa($order);

        Payment::create([
            'order_id' => $order->id,
            'provider' => 'esewa',
            'transaction_id' => $formData['transaction_uuid'],
            'amount' => $order->total,
            'status' => 'pending',
        ]);

        return view('payment.esewa_redirect', compact('formData'));
    }

    public function esewaSuccess(Request $request)
    {
        if (!$request->has('data')) {
            return redirect()->route('orders.index')->with('error', 'Invalid payment response.');
        }

        $decodedData = $this->paymentService->verifyEsewa($request->get('data'));

        if ($decodedData) {
            $transactionUuid = $decodedData['transaction_uuid'];
            $payment = Payment::where('transaction_id', $transactionUuid)->first();

            if ($payment && $payment->status !== 'completed') {
                $payment->update([
                    'status' => 'completed',
                    'provider_response' => $decodedData,
                    'verified_at' => now(),
                ]);

                $payment->order->update([
                    'payment_status' => 'paid',
                ]);

                return redirect()->route('orders.show', ['order' => $payment->order_id, 'paid' => 1])
                    ->with('success', 'Payment successful!');
            }
        }

        return redirect()->route('orders.index')->with('error', 'Payment verification failed.');
    }

    public function esewaFailure()
    {
        return redirect()->route('orders.index')->with('error', 'Payment cancelled or failed.');
    }

    // ── Khalti ──────────────────────────────────────────────────────────────

    public function initiateKhalti(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        try {
            $pidx = $this->paymentService->initiateKhalti($order);

            Payment::create([
                'order_id' => $order->id,
                'provider' => 'khalti',
                'transaction_id' => $pidx,
                'amount' => $order->total,
                'status' => 'pending',
            ]);

            return redirect()->away("https://test-pay.khalti.com/?pidx={$pidx}");
        } catch (\Exception $e) {
            return back()->with('error', 'Could not initiate Khalti payment.');
        }
    }

    public function khaltiSuccess(Request $request)
    {
        $pidx = $request->get('pidx');
        $transactionId = $request->get('transaction_id'); // khalti provided tx id

        if (!$pidx) {
            return redirect()->route('orders.index')->with('error', 'Invalid Khalti response.');
        }

        $payment = Payment::where('transaction_id', $pidx)->first();
        if (!$payment) {
            return redirect()->route('orders.index')->with('error', 'Payment record not found.');
        }

        $isValid = $this->paymentService->verifyKhalti($pidx);

        if ($isValid) {
            if ($payment->status !== 'completed') {
                $payment->update([
                    'status' => 'completed',
                    'provider_response' => $request->all(),
                    'verified_at' => now(),
                ]);

                $payment->order->update([
                    'payment_status' => 'paid',
                ]);
            }

            return redirect()->route('orders.show', ['order' => $payment->order_id, 'paid' => 1])
                ->with('success', 'Payment successful!');
        }

        $payment->update(['status' => 'failed', 'provider_response' => $request->all()]);
        return redirect()->route('orders.show', ['order' => $payment->order_id, 'failed' => 1])
            ->with('error', 'Payment verification failed.');
    }
}
