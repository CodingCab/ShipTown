<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class SchemaLoadedListener
{
    public function handle($event): void
    {
        DB::transaction(function () {
            Artisan::call('app:install');
        });
    }
}
