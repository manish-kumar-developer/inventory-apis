<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Services\ProductImageService;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'quantity',
        'price',
        'assigned_to',
        'mainImage'  
    ];

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }


    protected static function booted()
    {
        static::deleted(function ($product) {
            if ($product->mainImage) {
                app(ProductImageService::class)->delete($product->mainImage);
            }
        });

        static::updating(function ($product) {
            $originalImage = $product->getOriginal('mainImage');
            $newImage = $product->mainImage;
            
           
            if ($originalImage && $originalImage !== $newImage) {
                app(ProductImageService::class)->delete($originalImage);
            }
        });
    }
}