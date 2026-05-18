<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class Stage6CheckoutTest extends TestCase
{
    use RefreshDatabase;
    protected $seed = true;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::where('email', 'test@grocerease.com')->first();
        $this->actingAs($this->user);
    }

    public function test_successful_checkout_decrements_stock()
    {
        // 1. Create a product with stock = 2
        $product = Product::factory()->create([
            'status' => 'active',
            'stock_quantity' => 2,
            'price' => 500
        ]);

        // 2. Add to cart (qty 1)
        $this->post('/cart/add', [
            'product_id' => $product->id,
            'quantity' => 1
        ]);

        // 3. Place order via checkout
        $response = $this->post('/checkout/place-order', [
            'payment_method' => 'cod',
            'address_id' => 'new',
            'address_line1' => 'Test Street 1',
            'city' => 'Kathmandu',
            'district' => 'Kathmandu'
        ]);

        // 4. Assert success redirect to order page
        $order = Order::latest()->first();
        $response->assertRedirect(route('orders.show', ['order' => $order->id, 'placed' => 1]));
        $response->assertSessionHas('success');

        // 5. Verify stock dropped to 1
        $product->refresh();
        $this->assertEquals(1, $product->stock_quantity, 'Stock was not decremented correctly.');
        
        // Verify cart is empty
        $cartService = app(CartService::class);
        $this->assertEquals(0, $cartService->getItemCount(request()), 'Cart was not cleared.');
    }

    public function test_checkout_fails_gracefully_when_stock_insufficient()
    {
        // 1. Create product with stock = 2
        $product = Product::factory()->create([
            'status' => 'active',
            'stock_quantity' => 2,
            'price' => 500
        ]);

        // 2. Add to cart (qty 1 to bypass cart add limit if we enforce it, or directly 5 if bypassing UI)
        // Wait, CartService::addItem enforces min($qty, stock_quantity). So we can't add 5 via normal means.
        // Let's add 2 (which is allowed)
        $this->post('/cart/add', [
            'product_id' => $product->id,
            'quantity' => 2
        ]);
        
        // 3. Now simulate someone else buying 1 item concurrently, dropping stock to 1
        $product->update(['stock_quantity' => 1]);

        // 4. Attempt to checkout with 2 in cart, but only 1 in stock
        $response = $this->post('/checkout/place-order', [
            'payment_method' => 'cod',
            'address_id' => 'new',
            'address_line1' => 'Test Street 2',
            'city' => 'Kathmandu',
            'district' => 'Kathmandu'
        ]);

        // 5. Assert fails gracefully
        $response->assertRedirect(); // back
        $response->assertSessionHas('error');
        
        $errorMsg = session('error');
        $this->assertStringContainsString("has only 1 left", $errorMsg);
        
        // 6. Verify order was NOT created (DB transaction rolled back)
        // We might have previous orders from seeded data, but we can check if address was created 
        // to prove rollback. The new address shouldn't exist because of the transaction.
        $this->assertDatabaseMissing('addresses', [
            'address_line1' => 'Test Street 2'
        ]);
    }
}
