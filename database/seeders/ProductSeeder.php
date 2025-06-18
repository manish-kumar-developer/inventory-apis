<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::truncate(); // Optional: clears existing records

        for ($i = 1; $i <= 20; $i++) {
            Product::create([
                'name' => "Product $i",
                'description' => "Description for Product $i",
                'image' => 'https://via.placeholder.com/150?text=Product+' . $i, // Placeholder image
                'quantity' => rand(1, 100),
                'created_at' => now()->subDays(rand(0, 30)),
            ]);
        }
    }
}
