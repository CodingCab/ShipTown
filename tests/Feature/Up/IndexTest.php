<?php

namespace Tests\Feature\Up;

use App\User;
use Tests\TestCase;

class IndexTest extends TestCase
{
    protected string $uri = '/up';

    protected mixed $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function testIfUriSet()
    {
        $this->assertNotEmpty($this->uri);
    }

    public function testGuestCall()
    {
        $response = $this->get($this->uri);

        $response->assertSuccessful();
    }

    public function testUserCall()
    {
        $this->actingAs($this->user, 'web');

        $response = $this->get($this->uri);

        $response->assertSuccessful();
    }

    public function testAdminCall()
    {
        $this->user->assignRole('admin');

        $this->actingAs($this->user, 'web');

        $response = $this->get($this->uri);

        $response->assertSuccessful();
    }
}
