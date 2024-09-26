<?php

namespace Tests\Feature\Api\Admin\User\Roles;

use App\User;
use Tests\TestCase;

class IndexTest extends TestCase
{
    public function testIndexCallReturnsOk(): void
    {
        $user = User::factory()->create()->assignRole('admin');

        $response = $this->actingAs($user, 'api')->getJson(route('admin.users.roles.index'));

        $response->assertOk();

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'guard_name',
                ],
            ],
        ]);
    }
}
