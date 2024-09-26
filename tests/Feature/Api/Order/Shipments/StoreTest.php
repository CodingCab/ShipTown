<?php

namespace Tests\Feature\Api\Order\Shipments;

use App\Models\Order;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use RefreshDatabase;

    public function testStoreCallReturnsOk(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create();

        $response = $this->actingAs($user, 'api')->postJson(route('shipments.store'), [
            'order_id' => $order['id'],
            'shipping_number' => '123',
        ]);

        $response->assertStatus(201);

        $response->assertJsonStructure([
            'data' => [],
        ]);
    }
}
