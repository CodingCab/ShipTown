<?php

namespace Tests\Feature\Admin\Settings\Modules\Webhooks\Subscriptions;

use App\Modules\Webhooks\src\WebhooksServiceProviderBase;
use App\User;
use Tests\TestCase;

/**
 *
 */
class IndexTest extends TestCase
{
    /**
     * @var string
     */
    protected string $uri = '/admin/settings/modules/webhooks/subscriptions';

    /**
     * @var User
     */
    protected User $user;

    /**
     *
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function test_if_uri_set(): void
    {
        $this->assertNotEmpty($this->uri);
    }

    /** @test */
    public function test_guest_call(): void
    {
        $response = $this->get($this->uri);

        $response->assertRedirect('/login');
    }

    /** @test */
    public function test_user_call(): void
    {
        $this->actingAs($this->user, 'web');

        $response = $this->get($this->uri);

        $response->assertForbidden();
    }

    /** @test */
    public function test_admin_call(): void
    {
        WebhooksServiceProviderBase::disableModule();

        $this->user->assignRole('admin');

        $this->actingAs($this->user, 'web');

        $response = $this->get($this->uri);

        $response->assertRedirect();
    }
}
