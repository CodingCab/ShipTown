<?php

namespace Tests\Feature\QuickConnect\Magento;

use App\User;
use Tests\TestCase;

class IndexTest extends TestCase
{
    protected string $uri = '/quick-connect/magento';

    protected mixed $user;

    protected function setUp(): void
    {
        parent::setUp();
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
        /** @var User user */
        $user = User::factory()->create();

        $this->actingAs($user, 'web');

        $response = $this->get($this->uri);

        $response->assertSuccessful();
    }

    public function testAdminCall(): void
    {
        /** @var User user */
        $user = User::factory()->create();

        $user->assignRole('admin');

        $this->actingAs($user, 'web');

        $response = $this->get($this->uri);

        $response->assertSuccessful();
    }
}
