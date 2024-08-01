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
        $quantityDiscount = QuantityDiscount::factory()
            ->create([
                'name' => 'Buy 2 get 2 half price',
                'job_class' => CalculateSoldPriceForBuyXGetYForZPercentDiscount::class,
                'configuration' => [
                    'quantity_full_price' => 2,
                    'quantity_discounted' => 2,
                    'discount_percent' => 50,
                ],
            ]);

        QuantityDiscountsProduct::factory()
            ->create([
                'quantity_discount_id' => $quantityDiscount->id,
                'product_id' => Product::where(['sku' => '4001'])->first(),
            ]);

        QuantityDiscountsProduct::factory()
            ->create([
                'quantity_discount_id' => $quantityDiscount->id,
                'product_id' => Product::where(['sku' => '4002'])->first(),
            ]);

        QuantityDiscountsProduct::factory()
            ->create([
                'quantity_discount_id' => $quantityDiscount->id,
                'product_id' => Product::where(['sku' => '4005'])->first(),
            ]);
    }
}
