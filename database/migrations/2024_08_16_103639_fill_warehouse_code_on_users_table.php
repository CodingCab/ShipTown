<?php

use App\User;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        User::query()
            ->whereNull('warehouse_code')
            ->chunk(100, function ($users) {
                User::query()
                    ->whereIn('id', $users->pluck('id'))
                    ->update(['warehouse_code' =>  DB::raw('(SELECT code FROM warehouses WHERE warehouses.id = warehouse_id)')]);
            });
    }
};
