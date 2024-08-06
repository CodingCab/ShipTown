<?php

namespace Tests\Unit\Modules\TransactionProducts;

use App\Models\DataCollection;
use App\Models\DataCollectionRecord;
use App\Models\Product;
use App\Models\Warehouse;
use App\Modules\DataCollector\src\DataCollectorServiceProvider;
use App\Modules\QuantityDiscounts\src\Jobs\CalculateSoldPriceForBuyXGetYForZPercentDiscount;
use App\Modules\QuantityDiscounts\src\Models\QuantityDiscount;
use App\Modules\QuantityDiscounts\src\Models\QuantityDiscountsProduct;
use App\Modules\QuantityDiscounts\src\QuantityDiscountsServiceProvider;
use App\Modules\TransactionProducts\src\TransactionProductsServiceProvider;
use Tests\TestCase;

class GroupSimilarProductsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        ray()->clearAll();

        DataCollectorServiceProvider::enableModule();
        QuantityDiscountsServiceProvider::enableModule();
        TransactionProductsServiceProvider::enableModule();

        $this->warehouse = Warehouse::factory()->create();

        $this->product4001 = Product::factory()->create(['sku' => '4001', 'price' => 10]);
        $this->product4005 = Product::factory()->create(['sku' => '4005', 'price' => 50]);

        $this->product4001->prices()
            ->update([
                'price' => 10,
                'sale_price' => '10',
                'sale_price_start_date' => now()->subDays(14),
                'sale_price_end_date' => now()->subDays(7)
            ]);

        $this->product4005->prices()
            ->update([
                'price' => 50,
                'sale_price' => '50',
                'sale_price_start_date' => now()->subDays(14),
                'sale_price_end_date' => now()->subDays(7)
            ]);

        $quantityDiscount = QuantityDiscount::factory()->create([
            'name' => 'Buy 2 get 2 half price',
            'job_class' => CalculateSoldPriceForBuyXGetYForZPercentDiscount::class,
            'configuration' => [
                'quantity_full_price' => 2,
                'quantity_discounted' => 2,
                'discount_percent' => 50,
            ],
        ]);

        QuantityDiscountsProduct::factory()->create([
            'quantity_discount_id' => $quantityDiscount->id,
            'product_id' => $this->product4001->getKey(),
        ]);

        QuantityDiscountsProduct::factory()->create([
            'quantity_discount_id' => $quantityDiscount->id,
            'product_id' => $this->product4005->getKey(),
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
            'product_id' => $this->product4001->getKey(),
            'inventory_id' => $this->product4001->inventory()->first()->id,
            'unit_cost' => 5,
            'unit_full_price' => 10,
            'unit_sold_price' => 10,
            'quantity_scanned' => 1,
            'quantity_requested' => 0,
        ]);

        DataCollectionRecord::query()->create([
            'data_collection_id' => $dataCollection->getKey(),
            'product_id' => $this->product4005->getKey(),
            'inventory_id' => $this->product4005->inventory()->first()->id,
            'unit_cost' => 20,
            'unit_full_price' => 50,
            'unit_sold_price' => 50,
            'quantity_scanned' => 5,
            'quantity_requested' => 0,
        ]);

        ray($dataCollection->refresh(), $dataCollection->refresh()->records()->get()->toArray());

        $this->assertEquals(4, DataCollectionRecord::query()->where('data_collection_id', $dataCollection->id)->count());
    }
}
