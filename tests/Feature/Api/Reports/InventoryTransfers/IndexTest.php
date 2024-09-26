<?php

namespace Tests\Feature\Api\Reports\InventoryTransfers;

use App\Models\DataCollectionRecord;
use App\Models\Product;
use App\Models\Warehouse;
use App\User;
use Tests\TestCase;

class IndexTest extends TestCase
{
    public function testPaginationCallReturnsOk(): void
    {
        $user = User::factory()->create();

        $warehouse = Warehouse::factory()->create();

        Product::factory()->create();
        DataCollectionRecord::factory()->create([
            'product_id' => Product::first()->id,
            'warehouse_id' => $warehouse->id,
            'warehouse_code' => $warehouse->code,
        ]);

        $response = $this->actingAs($user, 'api')->getJson('/api/reports/inventory-transfers?page=1&per_page=1');

        $response->assertOk();
    }

    public function testIndexCallReturnsOk(): void
    {
        $user = User::factory()->create();

        $warehouse = Warehouse::factory()->create();
        Product::factory()->create();
        DataCollectionRecord::factory()->create([
            'product_id' => Product::first()->id,
            'warehouse_id' => $warehouse->id,
            'warehouse_code' => $warehouse->code,
        ]);

        $response = $this->actingAs($user, 'api')->getJson('/api/reports/inventory-transfers');

        $response->assertOk();
    }
}
