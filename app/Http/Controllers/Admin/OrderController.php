<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('user')->latest();

        if ($request->has('status') && $request->status != '') {
            $query->where('order_status', $request->status);
        }

        if ($request->has('date_from') && $request->date_from != '') {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to != '') {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->paginate(20);
        
        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'address', 'items.product.primaryImage', 'payment']);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:placed,confirmed,processing,dispatched,delivered,cancelled',
        ]);

        $validTransitions = [
            'placed' => ['confirmed', 'cancelled'],
            'confirmed' => ['processing', 'cancelled'],
            'processing' => ['dispatched', 'cancelled'],
            'dispatched' => ['delivered', 'cancelled'],
            'delivered' => [],
            'cancelled' => [],
        ];

        $currentStatus = $order->order_status;
        $newStatus = $request->status;

        if ($newStatus === 'cancelled' || in_array($newStatus, $validTransitions[$currentStatus] ?? [])) {
            $order->update(['order_status' => $newStatus]);
            return response()->json(['success' => true, 'message' => "Order status updated to {$newStatus}."]);
        }

        return response()->json(['success' => false, 'message' => "Invalid status transition from {$currentStatus} to {$newStatus}."], 422);
    }
}
