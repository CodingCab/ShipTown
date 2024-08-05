<?php

use Illuminate\Database\Migrations\Migration;
use App\Modules\TransactionProducts\src\TransactionProductsServiceProvider;

return new class extends Migration
{
    public function up(): void
    {
        TransactionProductsServiceProvider::installModule();
    }
};
