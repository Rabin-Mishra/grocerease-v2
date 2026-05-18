<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CartService
{
    public function getOrCreateCart(Request $request): Cart
    {
        if (auth()->check()) {
            return Cart::firstOrCreate(['user_id' => auth()->id()]);
        }

        $sessionCartId = $request->session()->get('cart_id');
        
        if ($sessionCartId) {
            $cart = Cart::where('session_id', $sessionCartId)->whereNull('user_id')->first();
            if ($cart) {
                return $cart;
            }
        }

        $newSessionId = (string) Str::uuid();
        $cart = Cart::create(['session_id' => $newSessionId]);
        $request->session()->put('cart_id', $newSessionId);
        
        return $cart;
    }

    public function addItem(Request $request, int $productId, int $quantity = 1): void
    {
        $product = Product::where('id', $productId)
            ->where('status', 'active')
            ->where('stock_quantity', '>', 0)
            ->firstOrFail();

        $cart = $this->getOrCreateCart($request);

        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $productId)
            ->first();

        if ($cartItem) {
            $newQuantity = min($cartItem->quantity + $quantity, $product->stock_quantity);
            $cartItem->update(['quantity' => $newQuantity]);
        } else {
            $newQuantity = min($quantity, $product->stock_quantity);
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $productId,
                'quantity' => $newQuantity,
                'price' => $product->price,
            ]);
        }
    }

    public function removeItem(Request $request, int $cartItemId): void
    {
        $cart = $this->getOrCreateCart($request);
        CartItem::where('id', $cartItemId)->where('cart_id', $cart->id)->delete();
    }

    public function updateQuantity(Request $request, int $cartItemId, int $qty): void
    {
        $cart = $this->getOrCreateCart($request);
        $cartItem = CartItem::where('id', $cartItemId)->where('cart_id', $cart->id)->firstOrFail();
        
        if ($qty < 1) {
            $qty = 1;
        }

        $maxStock = $cartItem->product->stock_quantity ?? 0;
        $qty = min($qty, $maxStock);

        $cartItem->update(['quantity' => $qty]);
    }

    public function getCartWithItems(Request $request): ?Cart
    {
        $cart = $this->getOrCreateCart($request);
        $cart->load(['items.product.primaryImage']);
        return $cart;
    }

    public function getItemCount(Request $request): int
    {
        $cart = $this->getOrCreateCart($request);
        return CartItem::where('cart_id', $cart->id)->sum('quantity') ?: 0;
    }

    public function getSubtotal(Request $request): float
    {
        $cart = $this->getOrCreateCart($request);
        $subtotal = 0;
        
        foreach ($cart->items as $item) {
            $unitPrice = $item->product->price ?? $item->price;
            $subtotal += $unitPrice * $item->quantity;
        }
        
        return (float) $subtotal;
    }

    public function mergeGuestCartOnLogin(User $user, ?string $guestCartId): void
    {
        if (!$guestCartId) return;

        $guestCart = Cart::where('session_id', $guestCartId)->whereNull('user_id')->first();
        if (!$guestCart) return;

        $userCart = Cart::firstOrCreate(['user_id' => $user->id]);

        foreach ($guestCart->items as $guestItem) {
            $userItem = CartItem::where('cart_id', $userCart->id)
                ->where('product_id', $guestItem->product_id)
                ->first();

            $maxStock = $guestItem->product->stock_quantity ?? 0;

            if ($userItem) {
                $newQuantity = min($userItem->quantity + $guestItem->quantity, $maxStock);
                $userItem->update(['quantity' => $newQuantity]);
            } else {
                CartItem::create([
                    'cart_id' => $userCart->id,
                    'product_id' => $guestItem->product_id,
                    'quantity' => min($guestItem->quantity, $maxStock),
                    'price' => $guestItem->price,
                ]);
            }
        }

        $this->clearCart($guestCart);
        $guestCart->delete();
    }

    public function clearCart(Cart $cart): void
    {
        CartItem::where('cart_id', $cart->id)->delete();
    }
}
