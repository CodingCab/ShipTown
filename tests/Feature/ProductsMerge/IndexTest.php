<?php

namespace Tests\Feature\ProductsMerge;

use App\Models\Product;
use App\User;
use Tests\TestCase;

class IndexTest extends TestCase
{
    protected string $uri = '/products-merge?sku1=sku1&sku2=sku2';

    protected mixed $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        Product::factory()->create(['sku' => 'sku1']);
        Product::factory()->create(['sku' => 'sku2']);
    }

    public function testIfUriSet(): void
    {
        $this->assertNotEmpty($this->uri);
    }

    public function testGuestCall(): void
    {
        $response = $this->get($this->uri);

        $response->assertRedirect('/login');
    }

    public function testUserCall(): void
    {
        $this->actingAs($this->user, 'web');

        $response = $this->get($this->uri);

        $response->assertSuccessful();
    }

    public function testAdminCall(): void
    {
        $this->user->assignRole('admin');

        $this->actingAs($this->user, 'web');

        $response = $this->get($this->uri);

        $response->assertSuccessful();
    }
}
