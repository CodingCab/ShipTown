<?php

namespace Tests\Feature\Orders;

use App\User;
use Tests\TestCase;

class IndexTest extends TestCase
{
    protected string $uri = '/orders';

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
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

    public function testIfUriSet(): void
    {
        $this->assertNotEmpty($this->uri);
    }
}
