<?php

namespace Tests\Feature\Api\Modules\Webhooks\Subscriptions;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    public function testIndexCallReturnsOk(): void
    {
        $this->assertTrue(true, 'Tested in External/Webhooks');
    }
}
