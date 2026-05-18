<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'brand', 'primaryImage'])->latest();

        if ($request->has('search') && $request->search != '') {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $products = $query->paginate(20);
        
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::orderBy('title')->get();
        $brands = Brand::orderBy('title')->get();
        return view('admin.products.create', compact('categories', 'brands'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'keywords' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'images' => 'required|array|min:1|max:4',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $slug = Str::slug($request->title) . '-' . time();

        $product = Product::create([
            'title' => $request->title,
            'slug' => $slug,
            'description' => $request->description,
            'keywords' => $request->keywords,
            'price' => $request->price,
            'stock_quantity' => $request->stock_quantity,
            'status' => $request->status,
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
        ]);

        $manager = new ImageManager(new Driver());

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $file) {
                $filename = Str::random(10) . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = "products/{$slug}/{$filename}";

                // Resize image max 800x800 preserve ratio
                $image = $manager->read($file);
                $image->scaleDown(width: 800, height: 800);
                
                // Store using Storage facade
                Storage::disk(config('filesystems.default'))->put($path, (string) $image->encode());

                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'is_primary' => $index === 0, // First image is primary
                ]);
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('title')->get();
        $brands = Brand::orderBy('title')->get();
        $product->load('images');
        
        return view('admin.products.edit', compact('product', 'categories', 'brands'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'keywords' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'new_images' => 'nullable|array|max:4',
            'new_images.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
            'delete_images' => 'nullable|array',
        ]);

        $product->update([
            'title' => $request->title,
            'description' => $request->description,
            'keywords' => $request->keywords,
            'price' => $request->price,
            'stock_quantity' => $request->stock_quantity,
            'status' => $request->status,
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
        ]);

        // Delete requested images
        if ($request->has('delete_images')) {
            foreach ($request->delete_images as $imageId) {
                $img = ProductImage::where('id', $imageId)->where('product_id', $product->id)->first();
                if ($img) {
                    if (Storage::disk(config('filesystems.default'))->exists($img->image_path)) {
                        Storage::disk(config('filesystems.default'))->delete($img->image_path);
                    }
                    $img->delete();
                }
            }
            
            // Re-assign primary if primary was deleted
            $hasPrimary = ProductImage::where('product_id', $product->id)->where('is_primary', true)->exists();
            if (!$hasPrimary) {
                $firstRemaining = ProductImage::where('product_id', $product->id)->first();
                if ($firstRemaining) {
                    $firstRemaining->update(['is_primary' => true]);
                }
            }
        }

        // Upload new images
        if ($request->hasFile('new_images')) {
            $manager = new ImageManager(new Driver());
            
            $existingCount = ProductImage::where('product_id', $product->id)->count();
            
            foreach ($request->file('new_images') as $file) {
                if ($existingCount >= 4) break; // Limit to 4 images
                
                $filename = Str::random(10) . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = "products/{$product->slug}/{$filename}";

                $image = $manager->read($file);
                $image->scaleDown(width: 800, height: 800);
                
                Storage::disk(config('filesystems.default'))->put($path, (string) $image->encode());

                $isPrimary = !ProductImage::where('product_id', $product->id)->where('is_primary', true)->exists();

                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'is_primary' => $isPrimary,
                ]);
                
                $existingCount++;
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        // Check for order items
        $hasOrders = \App\Models\OrderItem::where('product_id', $product->id)->exists();

        if ($hasOrders) {
            $product->update(['status' => 'inactive']);
            return redirect()->route('admin.products.index')->with('warning', 'Product cannot be deleted as it exists in orders. It has been marked as inactive.');
        }

        // Delete images
        $images = ProductImage::where('product_id', $product->id)->get();
        foreach ($images as $img) {
            if (Storage::disk(config('filesystems.default'))->exists($img->image_path)) {
                Storage::disk(config('filesystems.default'))->delete($img->image_path);
            }
        }
        
        // Delete product directory
        if (Storage::disk(config('filesystems.default'))->exists("products/{$product->slug}")) {
            Storage::disk(config('filesystems.default'))->deleteDirectory("products/{$product->slug}");
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }
}
