<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth:sanctum');
    }

    public function index()
    {
        if (Auth::user()->role === 'manager') {
            return Product::all();
        }
        return Auth::user()->assignedProducts;
    }

    public function store(Request $request)
    {
        if (Auth::user()->role !== 'manager') {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'name' => 'required|string',
            'quantity' => 'required|integer',
            'price' => 'required|numeric'
        ]);

        return Product::create($request->all());
    }

    public function show(Product $product)
    {
        if (Auth::user()->role === 'employee' && $product->assigned_to !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
        return $product;
    }

    public function update(Request $request, Product $product)
    {
        if (Auth::user()->role === 'employee') {
            if ($product->assigned_to !== Auth::id()) {
                abort(403, 'Unauthorized');
            }
            $validated = $request->validate(['quantity' => 'required|integer']);
            $product->update($validated);
            return $product;
        }
        
        $product->update($request->all());
        return $product;
    }

    public function destroy(Product $product)
    {
        if (Auth::user()->role !== 'manager') {
            abort(403, 'Unauthorized');
        }
        $product->delete();
        return response()->noContent();
    }
}