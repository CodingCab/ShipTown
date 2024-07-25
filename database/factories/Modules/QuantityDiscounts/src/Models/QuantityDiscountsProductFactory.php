<?php

namespace Database\Factories\Modules\QuantityDiscounts\src\Models;

use App\Modules\QuantityDiscounts\src\Models\QuantityDiscountsProduct;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuantityDiscountsProductFactory extends Factory
{
    protected $model = QuantityDiscountsProduct::class;

    public function definition(): array
    {
        return [
            'quantity_discount_id' => 1,
            'product_id' => 1,
        ];
    }
}