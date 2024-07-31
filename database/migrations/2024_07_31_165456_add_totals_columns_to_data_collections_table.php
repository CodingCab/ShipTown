<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('data_collections', function (Blueprint $table) {
            $table->decimal('total_quantity', 20, 2)->nullable()->after('name');
            $table->decimal('total_cost', 20, 2)->nullable()->after('total_quantity');
            $table->decimal('total_price', 20, 2)->nullable()->after('total_cost');
        });
    }
};
