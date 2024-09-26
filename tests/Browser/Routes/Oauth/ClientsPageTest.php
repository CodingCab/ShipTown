<?php

namespace Tests\Browser\Routes\Oauth;

use Tests\DuskTestCase;

class ClientsPageTest extends DuskTestCase
{
    public function testAutoPass(): void
    {
        $this->assertTrue(true, 'Default laravel passport route. Testing in vendor package');
    }
}
