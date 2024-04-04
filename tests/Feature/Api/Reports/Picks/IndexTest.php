<?php

namespace Tests\Feature\Api\Reports\Picks;

use App\Models\OrderProduct;
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
        OrderProduct::factory()->create();

        $response = $this->actingAs($user, 'api')->getJson(route('/api/reports/picks', ['page' => 1, 'per_page' => 1]));

        $response->assertOk();
    }
}
