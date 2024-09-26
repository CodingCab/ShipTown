<?php

namespace Tests\Feature\Api\Reports\Inventory;

use App\Models\Product;
use App\Models\Warehouse;
use App\User;
use Tests\TestCase;

class IndexTest extends TestCase
{
    public function testPaginationCallReturnsOk(): void
    {
        $user = User::factory()->create();

        Warehouse::factory()->create();
        Product::factory()->create();

        $response = $this->actingAs($user, 'api')->getJson('/api/reports/inventory?page=1&per_page=2');

        $response->assertOk();
    }

    public function testIndexCallReturnsOk(): void
    {
        $user = User::factory()->create();

        Warehouse::factory()->create();
        Product::factory()->create();

        $response = $this->actingAs($user, 'api')->getJson('/api/reports/inventory');

        $response->assertOk();
    }
}
