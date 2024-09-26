<?php

namespace Tests\Feature\Api\Reports\Picks;

use App\Models\OrderProduct;
use App\Models\Warehouse;
use App\User;
use Tests\TestCase;

class IndexTest extends TestCase
{
    public function testPaginationCallReturnsOk(): void
    {
        $user = User::factory()->create();

        Warehouse::factory()->create();
        OrderProduct::factory()->create();

        $response = $this->actingAs($user, 'api')->getJson('/api/reports/picks?per_page=1&page=2');

        $response->assertOk();
    }

    public function testIndexCallReturnsOk(): void
    {
        $user = User::factory()->create();

        Warehouse::factory()->create();
        OrderProduct::factory()->create();

        $response = $this->actingAs($user, 'api')->getJson('/api/reports/picks');

        $response->assertOk();
    }
}
