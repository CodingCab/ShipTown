<?php

namespace Tests\Feature\Api\Reports\Inventory;

use App\Models\Product;
use App\Models\Warehouse;
use App\User;
use Tests\TestCase;

class IndexTest extends TestCase
{
    /** @test */
    public function test_index_call_returns_ok()
    {
        $user = User::factory()->create();

        Warehouse::factory()->create();
        Product::factory()->create();

        $response = $this->actingAs($user, 'api')->getJson('/api/reports/inventory', ['page' => 1, 'per_page' => 1]);

        $response->assertOk();
    }
}
