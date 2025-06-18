<?php


use App\Http\Controllers\ProductController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('products', ProductController::class);
    Route::post('/products/{product}/assign', [AssignmentController::class, 'assign']);
    Route::get('/products/export', [ProductController::class, 'export']);
    
    
});


Route::any('{any}', function ($any) {
    return response()->json([
        'error' => 'Endpoint not found',
        'requested_path' => $any,
        'suggestions' => [
            'Check your route definition',
            'Verify the HTTP method',
            'Ensure authentication token is valid'
        ]
    ], 404);
})->where('any', '.*');
