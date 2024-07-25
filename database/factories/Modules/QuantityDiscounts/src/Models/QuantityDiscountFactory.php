<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class QuantityDiscountFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => 'Test Quantity Discount',
            'type' => 'BUY_X_GET_Y_FOR_Z_PRICE',
            'configuration' => []
        ];
    }
}
