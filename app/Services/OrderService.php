<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Exception;

class OrderService
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function placeOrder(User $user, Cart $cart, array $checkoutData): Order
    {
        return DB::transaction(function () use ($user, $cart, $checkoutData) {
            $cart->load('items.product');

            if ($cart->items->isEmpty()) {
                throw new Exception('Cart is empty');
            }

            $subtotal = 0;

            foreach ($cart->items as $item) {
                $product = $item->product;
                
                // We use sharedLock or lockForUpdate in a real highly concurrent system, 
                // but checking current stock within transaction is usually enough for simple apps
                if (!$product || $product->stock_quantity < $item->quantity) {
                    $title = $product->title ?? 'Unknown Product';
                    $stock = $product->stock_quantity ?? 0;
                    throw new Exception("'{$title}' has only {$stock} left.");
                }
                
                $subtotal += $product->price * $item->quantity;
            }

            $shippingFee = $subtotal < 2000 ? 150 : 0;
            $total = $subtotal + $shippingFee;

            $order = Order::create([
                'user_id' => $user->id,
                'address_id' => $checkoutData['address_id'],
                'subtotal' => $subtotal,
                'shipping_fee' => $shippingFee,
                'total' => $total,
                'payment_method' => $checkoutData['payment_method'],
                'payment_status' => 'pending',
                'order_status' => 'placed',
            ]);

            foreach ($cart->items as $item) {
                $product = $item->product;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_title' => $product->title,
                    'unit_price' => $product->price,
                    'quantity' => $item->quantity,
                ]);

                $product->decrement('stock_quantity', $item->quantity);
            }

            $this->cartService->clearCart($cart);

            return $order;
        });
    }
}
