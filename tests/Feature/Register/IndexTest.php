<?php

namespace Tests\Feature\Register;

use App\User;
use Tests\TestCase;

class IndexTest extends TestCase
{
    protected string $uri = '/register';

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

        if (User::query()->doesntExist()) {
            $response->assertOk();
        } else {
            $response->assertRedirect();
        }
    }

    public function testUserCall(): void
    {
        $this->actingAs($this->user, 'web')
            ->get($this->uri)
            ->assertRedirect();
    }

    public function testAdminCall(): void
    {
        $this->user->assignRole('admin');

        $this->actingAs($this->user, 'web')
            ->get($this->uri)
            ->assertRedirect();
    }
}
