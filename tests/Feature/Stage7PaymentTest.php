<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Stage7PaymentTest extends TestCase
{
    use RefreshDatabase;
    protected $seed = true;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::where('email', 'test@grocerease.com')->first();
        $this->actingAs($this->user);
    }

    public function test_esewa_payment_flow_initiates_redirect_form()
    {
        // 1. Create a product with stock = 5
        $product = Product::factory()->create([
            'status' => 'active',
            'stock_quantity' => 5,
            'price' => 500
        ]);

        // 2. Add to cart
        $this->post('/cart/add', [
            'product_id' => $product->id,
            'quantity' => 1
        ]);

        // 3. Place order with eSewa
        $response = $this->post('/checkout/place-order', [
            'payment_method' => 'esewa',
            'address_id' => 'new',
            'address_line1' => 'Test Street',
            'city' => 'Kathmandu',
            'district' => 'Kathmandu'
        ]);

        $order = Order::latest()->first();

        // 4. Assert redirect to esewa initiate route
        $response->assertRedirect(route('payment.esewa.initiate', $order->id));

        // 5. Follow the redirect to the eSewa initiate page
        $initiateResponse = $this->get(route('payment.esewa.initiate', $order->id));
        $initiateResponse->assertStatus(200);

        // 6. Verify the form fields
        $initiateResponse->assertSee('<form id="esewa-form"', false);
        $initiateResponse->assertSee('EPAYTEST'); // product_code
        $initiateResponse->assertSee('total_amount');
        $initiateResponse->assertSee('transaction_uuid');
        $initiateResponse->assertSee('signature');
        $initiateResponse->assertSee('success_url');
        
        // 7. Verify a Payment record was created
        $payment = Payment::where('order_id', $order->id)->first();
        $this->assertNotNull($payment, 'Payment record was not created.');
        $this->assertEquals('esewa', $payment->provider);
        $this->assertEquals('pending', $payment->status);
    }
}
