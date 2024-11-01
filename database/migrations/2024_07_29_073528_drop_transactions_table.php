<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('transactions');
    }

    public function down(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->json('raw_data');
            $table->timestamps();
        });
    }
};
