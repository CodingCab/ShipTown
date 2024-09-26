<?php

namespace Tests\Feature\Api\Modules\Module;

use App\Models\Module;
use App\Modules\BaseModuleServiceProvider;
use App\User;
use Illuminate\Testing\TestResponse;
use Laravel\Passport\Passport;
use Tests\TestCase;

class TestModule extends BaseModuleServiceProvider
{
    public static string $module_name = 'Test Module';

    public static string $module_description = 'Test Description';
}

class UpdateTest extends TestCase
{
    private function simulationTest(): TestResponse
    {
        TestModule::enableModule();

        $module = Module::where(['service_provider_class' => TestModule::class])->first();

        return $this->put(route('api.modules.update', $module));
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
}
