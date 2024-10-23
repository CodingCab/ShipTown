<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // TODO: remove this file if not used anymore
        Schema::table('modules', function (Blueprint $table) {
            if (!Schema::hasColumn('modules', 'name')) {
                $table->string('name')->nullable();
            }
            if (!Schema::hasColumn('modules', 'description')) {
                $table->text('description')->nullable();
            }
        });
    }
};
