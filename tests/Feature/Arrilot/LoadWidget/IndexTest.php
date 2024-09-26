<?php

namespace Tests\Feature\Arrilot\LoadWidget;

use App\User;
use Tests\TestCase;

class IndexTest extends TestCase
{
    protected string $uri = '/arrilot/load-widget';

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function testIfUriSet(): void
    {
        $this->assertNotEmpty($this->uri);
    }
}
