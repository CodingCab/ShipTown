<?php

namespace Tests\Feature\Help;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    protected string $uri = '/help';

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

        $response->assertSuccessful();
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
