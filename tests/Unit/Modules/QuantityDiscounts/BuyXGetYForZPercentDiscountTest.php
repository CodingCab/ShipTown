<?php

namespace Tests\Unit\Modules\QuantityDiscounts;

use App\Models\DataCollection;
use App\Models\DataCollectionRecord;
use App\Models\Product;
use App\Models\Warehouse;
use App\Modules\DataCollector\src\DataCollectorServiceProvider;
use App\Modules\QuantityDiscounts\src\Jobs\CalculateSoldPriceForBuyXGetYForZPercentDiscount;
use App\Modules\QuantityDiscounts\src\Models\QuantityDiscount;
use App\Modules\QuantityDiscounts\src\Models\QuantityDiscountsProduct;
use App\Modules\QuantityDiscounts\src\QuantityDiscountsServiceProvider;
use Tests\TestCase;

class BuyXGetYForZPercentDiscountTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        DataCollectorServiceProvider::enableModule();
        QuantityDiscountsServiceProvider::enableModule();

        $this->warehouse = Warehouse::factory()->create();

        $this->product1 = Product::factory()->create(['sku' => '4001']);
        $this->product2 = Product::factory()->create(['sku' => '4005']);

        $this->product1->prices()
            ->update([
                'price' => 20,
                'sale_price' => '17.99',
                'sale_price_start_date' => now()->subDays(14),
                'sale_price_end_date' => now()->subDays(7)
            ]);

        $this->product2->prices()
            ->update([
                'price' => 30,
                'sale_price' => '17.99',
                'sale_price_start_date' => now()->subDays(14),
                'sale_price_end_date' => now()->subDays(7)
            ]);

        $quantityDiscount = QuantityDiscount::factory()->create([
            'name' => 'Buy 2 get 3rd half price',
            'job_class' => CalculateSoldPriceForBuyXGetYForZPercentDiscount::class,
            'configuration' => [
                'quantity_full_price' => 2,
                'quantity_discounted' => 2,
                'discount_percent' => 50,
            ],
        ]);

        QuantityDiscountsProduct::factory()->create([
            'quantity_discount_id' => $quantityDiscount->id,
            'product_id' => $this->product1->getKey(),
        ]);

        QuantityDiscountsProduct::factory()->create([
            'quantity_discount_id' => $quantityDiscount->id,
            'product_id' => $this->product2->getKey(),
        ]);
    }

    /** @test */
    public function testExample()
    {
        /** @var DataCollection $dataCollection */
        $dataCollection = DataCollection::factory()->create([
            'warehouse_id' => $this->warehouse->getKey(),
            'warehouse_code' => $this->warehouse->code,
        ]);

        DataCollectionRecord::query()->create([
            'data_collection_id' => $dataCollection->getKey(),
            'product_id' => $this->product1->getKey(),
            'inventory_id' => $this->product1->inventory()->first()->id,
            'unit_cost' => 10,
            'unit_full_price' => 20,
            'unit_sold_price' => 20,
            'quantity_scanned' => 2,
            'quantity_requested' => 0,
        ]);

        $product2 = DataCollectionRecord::query()->create([
            'data_collection_id' => $dataCollection->getKey(),
            'product_id' => $this->product2->getKey(),
            'inventory_id' => $this->product2->inventory()->first()->id,
            'unit_cost' => 10,
            'unit_full_price' => 30,
            'unit_sold_price' => 30,
            'quantity_scanned' => 2,
            'quantity_requested' => 0,
        ]);

        $dataCollection = $dataCollection->refresh();

        $this->assertEquals(80, $dataCollection->total_sold_price);
    }
}
