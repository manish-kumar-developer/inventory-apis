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
     
    }


public function assign(Request $request, Product $product)
{

    if (Auth::user()->role !== 'manager') {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    $request->validate([
        'assigned_to' => 'required|exists:users,id'
    ]);

    
    $user = User::find($request->assigned_to);
    if ($user->role !== 'employee') {
        return response()->json(['error' => 'Can only assign to employees'], 400);
    }

    $product->update(['assigned_to' => $request->assigned_to]);
    
    return response()->json([
        'message' => 'Product assigned successfully',
        'product' => $product,
        'assigned_to' => $user->name
    ]);
}
}