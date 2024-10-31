<?php

namespace Database\Seeders;

use App\Modules\VatRates\src\Models\VatRate;
use Illuminate\Database\Seeder;

class VatRatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vatRates = [
            [
                'code' => 'VAT_0',
                'rate' => 0,
            ],
            [
                'code' => 'VAT_5',
                'rate' => 5,
            ],
            [
                'code' => 'VAT_8',
                'rate' => 8,
            ],
            [
                'code' => 'VAT_23',
                'rate' => 23,
            ],
        ];

        foreach ($vatRates as $vatRate) {
            VatRate::create($vatRate);
        }
    }
}
