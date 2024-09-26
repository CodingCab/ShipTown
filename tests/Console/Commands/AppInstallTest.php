<?php

namespace Tests\Console\Commands;

use Tests\TestCase;

class AppInstallTest extends TestCase
{
    public function testBasicFunctionality()
    {
        $this->artisan('db:wipe');
        $this->artisan('migrate');
        $this->artisan('app:install')
            ->assertExitCode(0);
    }
}
