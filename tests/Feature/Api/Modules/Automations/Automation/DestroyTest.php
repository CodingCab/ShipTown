<?php

namespace Tests\Feature\Api\Modules\Automations\Automation;

use App\Modules\Automations\src\Models\Automation;
use App\User;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $admin = User::factory()->create()->assignRole('admin');
        $this->actingAs($admin, 'api');
    }

    public function testDestroyCallReturnsOk(): void
    {
        $automation = Automation::create([
            'name' => 'Store Pickup',
            'priority' => 1,
        ]);

        $response = $this->delete(route('api.modules.automations.destroy', $automation));
        $response->assertOk();
    }
}
