<?php

namespace Database\Factories\Modules\QuantityDiscounts\src\Models;

use App\Modules\QuantityDiscounts\src\Models\QuantityDiscount;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuantityDiscountFactory extends Factory
{
    protected $model = QuantityDiscount::class;

    public function definition(): array
    {
        return [
            'name' => 'Test Quantity Discount',
            'job_class' => 'App\Modules\QuantityDiscounts\src\Jobs\CalculateSoldPriceForBuyXForYPercentDiscount',
            'configuration' => []
        ];
    }
}
