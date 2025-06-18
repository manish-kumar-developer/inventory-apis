<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Add missing columns
            if (!Schema::hasColumn('products', 'name')) {
                $table->string('name')->after('id');
            }
            
            if (!Schema::hasColumn('products', 'description')) {
                $table->text('description')->nullable()->after('name');
            }
            
            if (!Schema::hasColumn('products', 'quantity')) {
                $table->integer('quantity')->after('description');
            }
            
            if (!Schema::hasColumn('products', 'price')) {
                $table->decimal('price', 8, 2)->after('quantity');
            }
            
            if (!Schema::hasColumn('products', 'assigned_to')) {
                $table->foreignId('assigned_to')->nullable()->constrained('users')->after('price')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        // We don't want to remove columns in rollback to preserve data
    }
};