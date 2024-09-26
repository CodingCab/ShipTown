<?php

namespace Tests\Feature\Api\MailTemplates;

use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;

class IndexTest extends TestCase
{
    public function testIndexCallReturnsOk(): void
    {
        Passport::actingAs(
            User::factory()->admin()->create()
        );

        $response = $this->get(route('api.mail-templates.index'));

        $response->assertSuccessful();
    }

    public function testIndexCallShouldBeLoggedin(): void
    {
        $response = $this->get(route('api.mail-templates.index'));

        $response->assertRedirect(route('login'));
    }

    public function testIndexCallShouldLoggedinAsAdmin(): void
    {
        Passport::actingAs(User::factory()->create());

        $response = $this->get(route('api.mail-templates.index'));

        $response->assertForbidden();
    }
}
