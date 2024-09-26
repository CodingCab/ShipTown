<?php

namespace Tests\Feature\Pdf\Orders\OrderNumber\Template;

use App\Models\Order;
use App\User;
use Tests\TestCase;

class IndexTest extends TestCase
{
    protected string $uri = 'this set in setUp() method';

    protected User $user;

    protected Order $order;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->order = Order::factory()->create();

        $this->uri = '/pdf/orders/'.$this->order->order_number.'/address_label';
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

        $response->assertSuccessful();
    }

    public function testAdminCall(): void
    {
        $this->user->assignRole('admin');

        $this->actingAs($this->user, 'web');

        $response = $this->get($this->uri);

        $response->assertSuccessful();
    }
}
