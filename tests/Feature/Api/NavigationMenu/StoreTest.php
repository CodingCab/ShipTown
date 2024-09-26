<?php

namespace Tests\Feature\Api\NavigationMenu;

use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;

class StoreTest extends TestCase
{
    private function simulationTest($body = null)
    {
        if (is_null($body)) {
            $body = [
                'name' => 'testing',
                'url' => 'testing',
                'group' => 'picklist',
            ];
        }

        $response = $this->post(route('api.navigation-menu.store'), $body);

        return $response;
    }

    public function testStoreCallReturnsOk(): void
    {
        Passport::actingAs(
            User::factory()->admin()->create()
        );

        $response = $this->simulationTest();

        $response->assertSuccessful();
    }

    public function testStoreCallShouldBeLoggedin(): void
    {
        $response = $this->simulationTest();

        $response->assertRedirect(route('login'));
    }

    public function testStoreCallShouldLoggedinAsAdmin(): void
    {
        Passport::actingAs(
            User::factory()->create()
        );

        $response = $this->simulationTest();

        $response->assertForbidden();
    }

    public function testAllFieldIsRequired(): void
    {
        Passport::actingAs(
            User::factory()->admin()->create()
        );

        $response = $this->simulationTest([]);

        $response->assertSessionHasErrors([
            'name',
            'url',
            'group',
        ]);
    }

    public function testGroupNotPacklistOrPicklist(): void
    {
        Passport::actingAs(
            User::factory()->admin()->create()
        );

        $response = $this->simulationTest([
            'name' => 'tes',
            'url' => 'tes',
            'group' => 'tes',
        ]);

        $response->assertSessionHasErrors([
            'group',
        ]);
    }
}
