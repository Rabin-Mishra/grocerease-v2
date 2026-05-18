<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class CheckoutController extends Controller
{
    protected CartService $cartService;
    protected OrderService $orderService;

    public function __construct(CartService $cartService, OrderService $orderService)
    {
        $this->cartService = $cartService;
        $this->orderService = $orderService;
    }

    public function index(Request $request)
    {
        $cart = $this->cartService->getCartWithItems($request);

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $subtotal = $this->cartService->getSubtotal($request);
        $addresses = auth()->user()->addresses;

        return view('checkout.index', compact('cart', 'subtotal', 'addresses'));
    }

    public function placeOrder(Request $request)
    {
        $user = auth()->user();
        $hasAddresses = $user->addresses()->exists();

        $rules = [
            'payment_method' => 'required|in:esewa,khalti,cod',
        ];

        if ($hasAddresses && $request->has('address_id') && $request->address_id !== 'new') {
            $rules['address_id'] = 'required|exists:addresses,id,user_id,' . $user->id;
        } else {
            $rules['address_line1'] = 'required|string|max:255';
            $rules['city'] = 'required|string|max:100';
            $rules['district'] = 'required|string|max:100';
        }

        $validated = $request->validate($rules);

        try {
            DB::beginTransaction();

            $addressId = null;
            if (isset($validated['address_id']) && $validated['address_id'] !== 'new') {
                $addressId = $validated['address_id'];
            } else {
                $address = Address::create([
                    'user_id' => $user->id,
                    'address_line1' => $validated['address_line1'],
                    'city' => $validated['city'],
                    'district' => $validated['district'],
                    'is_default' => !$hasAddresses,
                ]);
                $addressId = $address->id;
            }

            $cart = $this->cartService->getCartWithItems($request);
            
            $checkoutData = [
                'address_id' => $addressId,
                'payment_method' => $validated['payment_method'],
            ];

            $order = $this->orderService->placeOrder($user, $cart, $checkoutData);

            DB::commit();

            if ($order->payment_method === 'cod') {
                return redirect()->route('orders.show', ['order' => $order->id, 'placed' => 1])
                    ->with('success', 'Order placed successfully!');
            }

            if ($order->payment_method === 'esewa') {
                return redirect()->route('payment.esewa.initiate', $order->id);
            }

            if ($order->payment_method === 'khalti') {
                return redirect()->route('payment.khalti.initiate', $order->id);
            }

            return redirect()->route('orders.show', $order->id);

        } catch (Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
}
