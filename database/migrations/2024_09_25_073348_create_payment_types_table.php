<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payment_types', function (Blueprint $table) {
            $table->id();
            $table->string('payment_code')->unique();
            $table->string('payment_name');
            $table->timestamps();
            $table->softDeletes();

            $table->index('payment_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_types');
    }
};
