<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Product;
use App\Models\CartItem;
use App\Models\Cart;

class Stage5CartTest extends TestCase
{
    use RefreshDatabase;
    protected $seed = true;

    public function test_guest_can_add_to_cart_and_view_it()
    {
        // 1. Create a product since the seeder only creates categories/brands
        $category = \App\Models\Category::first() ?? \App\Models\Category::factory()->create();
        $brand = \App\Models\Brand::first() ?? \App\Models\Brand::factory()->create();
        
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'status' => 'active',
            'stock_quantity' => 10,
            'price' => 100
        ]);

        // 2. Add product to cart as guest
        $response = $this->post('/cart/add', [
            'product_id' => $product->id,
            'quantity' => 1
        ]);
        $response->assertSessionHas('success');
        $response->assertRedirect();

        // 3. View cart page
        $cartResponse = $this->get('/cart');
        $cartResponse->assertStatus(200);
        $cartResponse->assertSee($product->title);
        $cartResponse->assertSee('value="1"', false);

        // 4. Add SAME product to cart again
        $this->post('/cart/add', [
            'product_id' => $product->id,
            'quantity' => 1
        ]);

        // 5. Verify quantity incremented rather than row duplicated
        $cartId = session('cart_id');
        $cart = Cart::where('session_id', $cartId)->first();
        $itemsCount = CartItem::where('cart_id', $cart->id)->count();
        $this->assertEquals(1, $itemsCount, 'Row was duplicated instead of incrementing quantity');
        
        $item = CartItem::where('cart_id', $cart->id)->first();
        $this->assertEquals(2, $item->quantity, 'Quantity did not increment to 2');
        
        // Check UI for updated quantity
        $cartResponse2 = $this->get('/cart');
        $cartResponse2->assertStatus(200);
        $cartResponse2->assertSee('value="2"', false);
        $cartResponse2->assertSee('Please login to checkout');
    }
}
