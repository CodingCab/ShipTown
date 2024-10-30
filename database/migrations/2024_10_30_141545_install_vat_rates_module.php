<?php

use App\Modules\VatRates\src\VatRatesModuleServiceProvider;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        VatRatesModuleServiceProvider::installModule();
    }
};
