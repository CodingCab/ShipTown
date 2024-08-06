<?php

namespace Tests\Unit\Modules\TransactionProducts;

use App\Modules\TransactionProducts\src\TransactionProductsServiceProvider;
use Tests\TestCase;

class BasicModuleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        TransactionProductsServiceProvider::enableModule();
    }

    /** @test */
    public function testBasicFunctionality()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }
}
