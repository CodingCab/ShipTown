<?php

namespace Tests\Feature\ShippingLabels\ShippingLabel;

use App\Models\Order;
use App\Models\ShippingLabel;
use App\User;
use Tests\TestCase;

class IndexTest extends TestCase
{
    protected string $uri = '/shipping-labels';

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $order = Order::factory()->create();
        $shippingLabel = ShippingLabel::factory()->create([
            'order_id' => $order->getKey(),
            'shipping_number' => 'test',
        ]);
        $this->uri = route('shipping-labels', [$shippingLabel->getKey()]);
        $this->user = User::factory()->create();
    }

    public function testIfUriSet(): void
    {
        $this->assertNotEmpty($this->uri);
    }

    public function testGuestCall(): void
    {
        $response = $this->get($this->uri);

        $response->assertRedirect('/login');
    }

    public function testUserCall(): void
    {
        $this->actingAs($this->user, 'web');

        $response = $this->get($this->uri);

        ray($response);

        $response->assertOk();
    }

    public function testAdminCall(): void
    {
        $this->user->assignRole('admin');

        $this->actingAs($this->user, 'web');

        $response = $this->get($this->uri);

        $response->assertOk();
    }
}
