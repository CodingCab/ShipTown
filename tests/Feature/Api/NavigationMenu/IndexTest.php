<?php

namespace Tests\Feature\Api\NavigationMenu;

use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;

class IndexTest extends TestCase
{
    public function testIndexCallReturnsOk(): void
    {
        Passport::actingAs(
            User::factory()->create()
        );

        $response = $this->get(route('api.navigation-menu.index'));

        $response->assertSuccessful();
    }
}
