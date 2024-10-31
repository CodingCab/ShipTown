<?php

namespace Database\Factories\Modules\VatRates\src\Models;

use App\Modules\VatRates\src\Models\VatRate;
use Illuminate\Database\Eloquent\Factories\Factory;

class VatRateFactory extends Factory
{
    protected $model = VatRate::class;

    public function definition(): array
    {
        return [
            'code' => 'VAT_23',
            'rate' => 23,
        ];
    }
}
