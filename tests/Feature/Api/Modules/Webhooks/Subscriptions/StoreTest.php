<?php

namespace Tests\Feature\Api\Modules\Webhooks\Subscriptions;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use RefreshDatabase;

    public function testStoreCallReturnsOk(): void
    {
        $this->assertTrue(true, 'Tested in External/Webhooks');
    }
}
