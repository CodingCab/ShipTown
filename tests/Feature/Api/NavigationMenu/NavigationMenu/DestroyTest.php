<?php

namespace Tests\Feature\Api\NavigationMenu\NavigationMenu;

use App\Models\NavigationMenu;
use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    private function simulationTest()
    {
        $navigationMenu = NavigationMenu::create([
            'name' => 'testing',
            'url' => 'testing',
            'group' => 'picklist',
        ]);

        return $this->delete(route('api.navigation-menu.destroy', $navigationMenu));
    }

    public function testDeleteCallReturnsOk(): void
    {
        Passport::actingAs(
            User::factory()->admin()->create()
        );

        $response = $this->simulationTest();

        $response->assertSuccessful();
    }

    public function testDeleteCallShouldBeLoggedin(): void
    {
        $response = $this->simulationTest();

        $response->assertRedirect(route('login'));
    }
}
