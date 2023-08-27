<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('modules_magento2api_connections', function (Blueprint $table) {
            $table->string('magento_store_code')->nullable()->after('magento_store_id');
        });
    }
};