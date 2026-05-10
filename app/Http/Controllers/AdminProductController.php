<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Cloudinary\Cloudinary;

class AdminProductController extends Controller
{
    private function checkAdmin(): void
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized.');
        }
    }

    public function index()
    {
        $this->checkAdmin();
        $products = Product::latest()->get();
        return view('admin.products', compact('products'));
    }

    public function store(Request $request)
    {
        $this->checkAdmin();

        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'price'   => 'required|numeric|min:0',
            'stock'   => 'required|numeric|min:0',
            'capital' => 'nullable|numeric|min:0',
            'image'   => 'nullable|file|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data['capital'] = $data['capital'] ?? 0;
        $data['is_available'] = ((float) $data['stock']) > 0;

        if ($request->hasFile('image')) {
            $cloudinary = new Cloudinary(env('CLOUDINARY_URL'));
            $result = $cloudinary->uploadApi()->upload($request->file('image')->getRealPath());
            $data['image'] = $result['secure_url'];
        }

        Product::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully.',
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $this->checkAdmin();

        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'price'   => 'required|numeric|min:0',
            'stock'   => 'required|numeric|min:0',
            'capital' => 'nullable|numeric|min:0',
            'image'   => 'nullable|file|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data['capital'] = $data['capital'] ?? 0;
        $data['is_available'] = ((float) $data['stock']) > 0;

        if ($request->hasFile('image')) {
            $cloudinary = new Cloudinary(env('CLOUDINARY_URL'));
            $result = $cloudinary->uploadApi()->upload($request->file('image')->getRealPath());
            $data['image'] = $result['secure_url'];
        }

        $product->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully.',
        ]);
    }

    public function destroy(Product $product)
    {
        $this->checkAdmin();
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully.',
        ]);
    }
}