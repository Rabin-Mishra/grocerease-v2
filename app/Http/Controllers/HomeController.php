<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->get();
        $brands = Brand::withCount('products')->get();
        $featuredProducts = Product::with('primaryImage')->latest()->limit(8)->get();
        
        return view('home', compact('categories', 'brands', 'featuredProducts'));
    }
}
