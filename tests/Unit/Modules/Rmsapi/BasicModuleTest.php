<?php

namespace Tests\Unit\Modules\Rmsapi;

use App\Modules\Rmsapi\src\RmsapiModuleServiceProvider;
use Tests\TestCase;

class BasicModuleTest extends TestCase
{
    public function testModuleBasicFunctionality(): void
    {
        RmsapiModuleServiceProvider::enableModule();

        $this->assertTrue(true, 'Most basic test... to be continued');
    }
}
