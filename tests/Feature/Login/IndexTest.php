<?php

namespace Tests\Feature\Login;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    protected string $uri = '/login';

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function testGuestCall(): void
    {
        $response = $this->get($this->uri);

        $response->assertSuccessful();
    }

    public function testUserCall(): void
    {
        $this->actingAs($this->user, 'web');

        $response = $this->get($this->uri);

        $response->assertRedirect('/dashboard');
    }

    public function testAdminCall(): void
    {
        $this->actingAs($this->user, 'web');

        $this->user->assignRole('admin');

        $response = $this->get($this->uri);

        $response->assertRedirect('/dashboard');
    }

    public function testIfUriSet(): void
    {
        $this->assertNotEmpty($this->uri);
    }
}
