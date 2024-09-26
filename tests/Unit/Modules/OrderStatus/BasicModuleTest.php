<?php

namespace Tests\Unit\Modules\OrderStatus;

use App\Models\Order;
use App\Models\OrderStatus;
use App\Modules\OrderStatus\src\OrderStatusServiceProvider;
use Tests\TestCase;

class BasicModuleTest extends TestCase
{
    public function testIfOrderIsActiveUpdates(): void
    {
        /** @var OrderStatus $orderStatus */
        $orderStatus = OrderStatus::factory()->create(['order_active' => true]);

        Order::factory()->create(['status_code' => $orderStatus->code]);

        $this->assertDatabaseHas('orders', ['is_active' => true]);

        $orderStatus->update(['order_active' => false]);

        $this->assertDatabaseHas('orders', ['is_active' => false]);
    }

    public function testIfOrderOnHoldUpdates(): void
    {
        OrderStatusServiceProvider::enableModule();

        /** @var OrderStatus $orderStatus */
        $orderStatus = OrderStatus::factory()->create(['order_on_hold' => true]);

        Order::factory()->create(['status_code' => $orderStatus->code, 'is_on_hold' => true]);

        $this->assertDatabaseHas('orders', ['is_on_hold' => true]);

        $orderStatus->update(['order_on_hold' => false]);

        $this->assertDatabaseHas('orders', ['is_on_hold' => false]);
    }

    public function testIfCanEnable(): void
    {
        OrderStatusServiceProvider::enableModule();

        $this->assertTrue(true, 'Most basic test... to be continued');
    }
}
