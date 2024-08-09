<?php

namespace Database\Factories\Modules\QuantityDiscounts\src\Models;

use App\Modules\QuantityDiscounts\src\Jobs\CalculateSoldPriceForBuyXGetYForZPercentDiscount;
use App\Modules\QuantityDiscounts\src\Models\QuantityDiscount;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuantityDiscountFactory extends Factory
{
    protected $model = QuantityDiscount::class;

    public function definition(): array
    {
        return [
            'name' => 'Buy 2 get 3rd half price',
            'job_class' => CalculateSoldPriceForBuyXGetYForZPercentDiscount::class,
            'configuration' => [
                'quantity_full_price' => 2,
                'quantity_discounted' => 1,
                'discount_percent' => 50,
            ]
        ];
    }
}
