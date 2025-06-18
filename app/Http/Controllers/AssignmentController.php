<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssignmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('role:manager');
    }

    public function assign(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'user_id' => 'required|exists:users,id'
        ]);

        $product = Product::find($request->product_id);
        $user = User::find($request->user_id);

        if ($user->role !== 'employee') {
            return response()->json(['message' => 'Can only assign to employees'], 400);
        }

        $product->update(['assigned_to' => $user->id]);
        return response()->json(['message' => 'Product assigned successfully']);
    }
}