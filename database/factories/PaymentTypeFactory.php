<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentTypeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'payment_code' => $this->faker->unique()->currencyCode,
            'payment_name' => $this->faker->unique()->creditCardType,
        ];
    }
}
