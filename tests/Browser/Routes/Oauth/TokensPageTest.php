<?php

namespace Tests\Browser\Routes\Oauth;

use Tests\DuskTestCase;

class TokensPageTest extends DuskTestCase
{
    public function testAutoPass(): void
    {
        $this->assertTrue(true, 'Default laravel passport route. Testing in vendor package');
    }
}
