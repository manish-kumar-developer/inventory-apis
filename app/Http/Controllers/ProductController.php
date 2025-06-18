<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use App\Services\ProductImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

 
    public function index()
    {
        Log::info('Fetching products', ['user_id' => Auth::id()]);

        if (Auth::user()->role === 'manager') {
            $products = Product::with('assignedUser')->get();
            return response()->json($products);
        }

        $products = Auth::user()->assignedProducts()->with('assignedUser')->get();
        return response()->json($products);
    }

   
      public function store(Request $request)
    {
        if (Auth::user()->role !== 'manager') {
            return response()->json(['error' => 'Unauthorized action'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'mainImage' => 'required|image|mimes:jpeg,png,jpg|max:2048', // Changed to image
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0'
        ]);

        $imageService = new ProductImageService();
        $imageName = null;

        if ($request->hasFile('mainImage')) {
            $imageName = $imageService->upload($request->file('mainImage'));
        }

        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'mainImage' => $imageName,
            'price' => $request->price,
            'quantity' => $request->quantity
        ]);

        Log::info('Product created', [
            'product_id' => $product->id,
            'user_id' => Auth::id()
        ]);

        return response()->json([
            'message' => 'Product created successfully',
            'product' => $product
        ], 201);
    }


    public function show(Product $product)
    {
        if (Auth::user()->role === 'employee' && $product->assigned_to !== Auth::id()) {
            Log::warning('Unauthorized product access', [
                'user_id' => Auth::id(),
                'product_id' => $product->id
            ]);
            return response()->json(['error' => 'Unauthorized access to product'], 403);
        }

        return response()->json($product->load('assignedUser'));
    }

 


    public function update(Request $request, Product $product)
    {
        if (Auth::user()->role === 'employee') {
            if ($product->assigned_to !== Auth::id()) {
                Log::warning('Unauthorized product update', [
                    'user_id' => Auth::id(),
                    'product_id' => $product->id
                ]);
                return response()->json(['error' => 'Unauthorized action'], 403);
            }

            $request->validate(['quantity' => 'required|integer|min:0']);
            $product->update($request->only('quantity'));
            return response()->json($product);
        }

     
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'mainImage' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Changed to image
            'price' => 'sometimes|numeric|min:0',
            'quantity' => 'sometimes|integer|min:0'
        ]);

        $updateData = $request->except('mainImage');
        $imageService = new ProductImageService();

        if ($request->hasFile('mainImage')) {
            $updateData['mainImage'] = $imageService->upload($request->file('mainImage'));
        }

        $product->update($updateData);
        return response()->json($product);
    }

    public function destroy(Product $product)
    {
        if (Auth::user()->role !== 'manager') {
            return response()->json(['error' => 'Unauthorized action'], 403);
        }

        $product->delete();

        Log::info('Product deleted', [
            'product_id' => $product->id,
            'user_id' => Auth::id()
        ]);

        return response()->noContent();
    }


    public function assign(Request $request, Product $product)
    {
        if (Auth::user()->role !== 'manager') {
            return response()->json(['error' => 'Unauthorized action'], 403);
        }

        $request->validate([
            'assigned_to' => 'required|exists:users,id'
        ]);

        $user = User::find($request->assigned_to);

        if ($user->role !== 'employee') {
            return response()->json(['error' => 'Can only assign to employees'], 400);
        }

        $product->update(['assigned_to' => $request->assigned_to]);

        Log::info('Product assigned', [
            'product_id' => $product->id,
            'assigned_to' => $user->id,
            'by_user' => Auth::id()
        ]);

        return response()->json([
            'message' => 'Product assigned successfully',
            'product' => $product->load('assignedUser')
        ]);
    }

    public function export()
    {
        if (Auth::user()->role !== 'manager') {
            return response()->json(['error' => 'Unauthorized action'], 403);
        }

        $fileName = 'products_' . date('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        $products = Product::with('assignedUser')->get();

        $callback = function () use ($products) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'ID',
                'Name',
                'Description',
                'Main Image',
                'Price',
                'Quantity',
                'Assigned To',
                'Assigned User Email',
                'Created At'
            ]);

           
            foreach ($products as $product) {
                fputcsv($file, [
                    $product->id,
                    $product->name,
                    $product->description,
                    $product->mainImage,
                    $product->price,
                    $product->quantity,
                    $product->assigned_to,
                    $product->assignedUser ? $product->assignedUser->email : 'N/A',
                    $product->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
