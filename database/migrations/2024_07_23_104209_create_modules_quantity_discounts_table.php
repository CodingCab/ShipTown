<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('modules_quantity_discounts', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable()->after('id');
            $table->string('type', 15)->nullable()->after('name');
            $table->json('configuration')->nullable()->after('type');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modules_quantity_discounts');
    }
};
