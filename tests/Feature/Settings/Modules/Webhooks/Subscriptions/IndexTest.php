<?php

namespace Tests\Feature\Settings\Modules\Webhooks\Subscriptions;

use App\Modules\Webhooks\src\WebhooksServiceProviderBase;
use App\User;
use Tests\TestCase;

class IndexTest extends TestCase
{
    protected string $uri = '/settings/modules/webhooks/subscriptions';

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

        $response = $this->get($this->uri);

        $response->assertForbidden();
    }

    public function testAdminCall(): void
    {
        WebhooksServiceProviderBase::disableModule();

        $this->user->assignRole('admin');

        $this->actingAs($this->user, 'web');

        $response = $this->get($this->uri);

        $response->assertRedirect();
    }
}
