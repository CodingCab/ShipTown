<?php

use App\User;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $users = User::query()->whereNull('warehouse_code')->with('warehouse')->get();

        $users->each(function (User $user) {
            return $user->update(['warehouse_code' => $user->warehouse->code]);
        });
    }
};
