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
            $table->string('name')->change();
            $table->text('description')->change();
        });
    }
};
