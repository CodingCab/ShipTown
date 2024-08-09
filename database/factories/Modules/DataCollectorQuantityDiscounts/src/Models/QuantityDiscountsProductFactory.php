<?php

namespace Database\Factories\Modules\DataCollectorQuantityDiscounts\src\Models;

use App\Models\Product;
use App\Modules\DataCollectorQuantityDiscounts\src\Models\QuantityDiscount;
use App\Modules\DataCollectorQuantityDiscounts\src\Models\QuantityDiscountsProduct;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuantityDiscountsProductFactory extends Factory
{
    protected $model = QuantityDiscountsProduct::class;

    public function definition(): array
    {
        return [
            'quantity_discount_id' => function () {
                return QuantityDiscount::factory()->create()->id;
            },
            'product_id' => function () {
                return Product::factory()->create()->id;
            },
        ];
    }
}
