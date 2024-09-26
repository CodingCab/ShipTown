<?php

namespace Tests\Unit\Modules\PrintNode;

use App\Modules\PrintNode\src\PrintNodeServiceProvider;
use Tests\TestCase;

class BasicModuleTest extends TestCase
{
    public function testModuleBasicFunctionality(): void
    {
        PrintNodeServiceProvider::enableModule();

        $this->assertTrue(true, 'Most basic test... to be continued');
    }
}
