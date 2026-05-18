<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $total_orders = Order::count();
        $pending_orders = Order::where('order_status', 'placed')->count();
        $total_revenue = Order::where('payment_status', 'paid')->sum('total');
        
        $total_products = Product::count();
        $low_stock_products = Product::where('stock_quantity', '<', 5)->get();
        
        $recent_orders = Order::with('user')->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'total_orders',
            'pending_orders',
            'total_revenue',
            'total_products',
            'low_stock_products',
            'recent_orders'
        ));
    }
}
