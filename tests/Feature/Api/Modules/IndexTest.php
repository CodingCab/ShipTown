<?php

namespace Tests\Feature\Api\Modules;

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

        $response = $this->get(route('api.modules.index'));

        $response->assertSuccessful();
    }

    public function testIndexCallShouldBeLoggedin(): void
    {
        $response = $this->get(route('api.modules.index'));

        $response->assertRedirect(route('login'));
    }

    public function testIndexCallShouldLoggedinAsAdmin(): void
    {
        Passport::actingAs(
            User::factory()->create()
        );

        $response = $this->get(route('api.modules.index'));

        $response->assertForbidden();
    }
}
