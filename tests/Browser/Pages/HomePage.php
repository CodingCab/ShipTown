<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;

class HomePage extends Page
{
    /**
     * Get the URL for the page.
     */
    public function testUrl(): string
    {
        return '/';
    }

    /**
     * Assert that the browser is on the page.
     */
    public function testAssert(Browser $browser): void
    {
        //
    }

    /**
     * Get the element shortcuts for the page.
     */
    public function testElements(): array
    {
        return [
            '@element' => '#selector',
        ];
    }
}
