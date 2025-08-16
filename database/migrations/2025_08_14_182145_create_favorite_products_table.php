<?php
// database/migrations/2025_08_14_000000_create_favorite_products_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('favorite_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('product_id'); // FK ke products.id_produk
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique('product_id');
            $table->index(['is_active', 'sort_order']);

            // Foreign key constraint (sesuaikan dengan nama tabel produk Anda)
            $table->foreign('product_id')->references('id_produk')->on('produk')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favorite_products');
    }
};