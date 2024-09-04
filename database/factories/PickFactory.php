<?php

namespace Database\Factories;

use App\Models\OrderProduct;
use App\Models\Product;
use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PickFactory extends Factory
{
    public function definition(): array
    {
        $product = Product::query()->inRandomOrder()->first() ?? Product::factory()->create();

        $user = User::query()->inRandomOrder()->first() ?? User::factory()->create();

        $orderProducts = OrderProduct::factory()->count(rand(1, 5))->create([
            'product_id' => $product->getKey(),
        ]);

        $quantity = $orderProducts->sum('quantity_ordered');

        $skippingPick = (rand(1, 20) === 1);

        return [
            'product_id' => $product->getKey(),
            'order_product_ids' => $orderProducts->pluck('id')->toArray(),
            'sku_ordered' => $product->sku,
            'name_ordered' => $product->name,
            'user_id' => $user->getKey(),
            'quantity_picked' => $skippingPick ? 0 : $quantity,
            'quantity_skipped_picking' => $skippingPick ? $quantity : 0,
        ];
    }
}
