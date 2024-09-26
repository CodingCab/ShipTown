<?php

namespace Tests\Feature\Api\OrdersStatuses\OrdersStatus;

use App\Models\OrderStatus;
use App\User;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $admin = User::factory()->create()->assignRole('admin');
        $this->actingAs($admin, 'api');
    }

    public function testDestroyCallReturnsOk(): void
    {
        $orderStatus = OrderStatus::create([
            'name' => 'testing',
            'code' => 'testing',
            'order_active' => 0,
            'sync_ecommerce' => 0,
        ]);

        $response = $this->delete(route('api.orders-statuses.destroy', $orderStatus));
        $response->assertOk();
    }

    public function testCannotDeleteOrderActive(): void
    {
        $orderStatus = OrderStatus::create([
            'name' => 'testing',
            'code' => 'testing',
            'order_active' => 1,
            'sync_ecommerce' => 0,
        ]);

        $response = $this->delete(route('api.orders-statuses.destroy', $orderStatus));
        $response->assertStatus(401);
    }

    public function testCannotDeleteSyncEcommerce(): void
    {
        $orderStatus = OrderStatus::create([
            'name' => 'testing',
            'code' => 'testing',
            'order_active' => 0,
            'sync_ecommerce' => 1,
        ]);

        $response = $this->delete(route('api.orders-statuses.destroy', $orderStatus));
        $response->assertStatus(401);
    }
}
