<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modules_quantity_discounts_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quantity_discount_id');
            $table->foreignId('product_id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modules_quantity_discounts_products');
    }
};
