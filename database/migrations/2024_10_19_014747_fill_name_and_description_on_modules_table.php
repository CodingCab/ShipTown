<?php

use App\Models\Module;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // TODO: remove this file if not used anymore
        Module::whereNull('name')->orWhereNull('description')->get()
            ->each(function (Module $module) {
                $module->name = $module->name;
                $module->description = $module->description;
                $module->save();
            });
    }
};
