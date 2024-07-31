<?php

namespace Database\Seeders\Demo;

use App\Models\Product;
use App\Modules\QuantityDiscounts\src\Jobs\CalculateSoldPriceForBuyXGetYForZPercentDiscount;
use App\Modules\QuantityDiscounts\src\Models\QuantityDiscount;
use App\Modules\QuantityDiscounts\src\Models\QuantityDiscountsProduct;
use Illuminate\Database\Seeder;

class QuantityDiscountSeeder extends Seeder
{
    public function run()
    {
        $product = Product::where(['sku' => '4001'])->first();

        $product->prices()
            ->update([
                'price' => 20,
                'sale_price' => '17.99',
                'sale_price_start_date' => now()->subDays(14),
                'sale_price_end_date' => now()->subDays(7)
            ]);

        $quantityDiscount = QuantityDiscount::factory()->create([
            'name' => 'Buy 2 get 3rd half price',
            'job_class' => CalculateSoldPriceForBuyXGetYForZPercentDiscount::class,
            'configuration' => [
                'quantity_full_price' => 2,
                'quantity_discounted' => 1,
                'discount_percent' => 50,
            ],
        ]);

        QuantityDiscountsProduct::factory([
                'quantity_discount_id' => $quantityDiscount->id,
                'product_id' => $product->getKey(),
            ])
            ->create();
    }
}
