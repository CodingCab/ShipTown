<?php

namespace Tests\Feature\Api\NavigationMenu\NavigationMenu;

use App\Models\NavigationMenu;
use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;

class UpdateTest extends TestCase
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

        $navigationMenu = NavigationMenu::create([
            'name' => 'testing',
            'url' => 'testing',
            'group' => 'picklist',
        ]);

        $response = $this->put(route('api.navigation-menu.update', $navigationMenu), $body);

        return $response;
    }

    public function testUpdateCallReturnsOk(): void
    {
        Passport::actingAs(
            User::factory()->admin()->create()
        );

        $response = $this->simulationTest();

        $response->assertSuccessful();
    }

    public function testUpdateCallShouldBeLoggedin(): void
    {
        $response = $this->simulationTest();

        $response->assertRedirect(route('login'));
    }

    public function testUpdateCallShouldLoggedinAsAdmin(): void
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
