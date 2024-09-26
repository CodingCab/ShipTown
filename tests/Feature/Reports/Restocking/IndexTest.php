<?php

namespace Tests\Feature\Reports\Restocking;

use App\Models\Product;
use App\Models\Warehouse;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    protected string $uri = '/reports/restocking';

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
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

        Warehouse::factory()->create();
        Product::factory()->create();

        $response = $this->get($this->uri);

        $response->assertSuccessful();
    }

    public function testAdminCall(): void
    {
        $this->actingAs($this->user, 'web');

        $this->user->assignRole('admin');

        Warehouse::factory()->create();
        Product::factory()->create();

        $response = $this->get($this->uri);

        $response->assertSuccessful();
    }
}
