<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('modules_reports_inventory_dashboard_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('warehouse_id');
            $table->string('warehouse_code');
            $table->json('data')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['warehouse_id', 'warehouse_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modules_reports_inventory_dashboard_records');
    }
};
