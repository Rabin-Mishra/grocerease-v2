<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index(Request $request)
    {
        $cart = $this->cartService->getCartWithItems($request);
        $subtotal = $this->cartService->getSubtotal($request);
        return view('cart.index', compact('cart', 'subtotal'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer',
            'quantity' => 'required|integer|min:1|max:99'
        ]);

        try {
            $this->cartService->addItem($request, $request->product_id, $request->quantity);
            return back()->with('success', 'Item added to cart!');
        } catch (\Exception $e) {
            return back()->with('error', 'Unable to add item to cart. It may be out of stock.');
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'cart_item_id' => 'required|integer',
            'quantity' => 'required|integer|min:1|max:99'
        ]);

        $this->cartService->updateQuantity($request, $request->cart_item_id, $request->quantity);
        return back()->with('success', 'Cart updated successfully.');
    }

    public function remove(Request $request, int $id)
    {
        $this->cartService->removeItem($request, $id);
        return back()->with('success', 'Item removed from cart.');
    }
}
