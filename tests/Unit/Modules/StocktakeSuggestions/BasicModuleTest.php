<?php

namespace Tests\Unit\Modules\StocktakeSuggestions;

use App\Models\Product;
use App\Models\StocktakeSuggestion;
use App\Models\Warehouse;
use App\Modules\StocktakeSuggestions\src\Jobs\OutdatedCountsJob;
use App\Modules\StocktakeSuggestions\src\Models\StocktakeSuggestionsConfiguration;
use App\Modules\StocktakeSuggestions\src\StocktakeSuggestionsServiceProvider;
use Tests\TestCase;

class BasicModuleTest extends TestCase
{
    public function testInStockSince(): void
    {
        StocktakeSuggestionsServiceProvider::enableModule();

        StocktakeSuggestionsConfiguration::query()->updateOrCreate([], [
            'min_count_date' => now()->subWeek(),
        ]);

        Warehouse::factory()->create();

        $inventory = Product::factory()->create()->inventory()->first();

        $inventory->update([
            'last_counted_at' => now()->subMonth(),
            'in_stock_since' => now()->subMonth(),
            'quantity' => 1,
        ]);

        OutdatedCountsJob::dispatch();

        $this->assertEquals(1, StocktakeSuggestion::query()->count());

        $inventory->update([
            'last_counted_at' => now()->subMonth(),
            'in_stock_since' => now()->subDay(),
            'quantity' => 1,
        ]);

        OutdatedCountsJob::dispatch();

        $this->assertEquals(0, StocktakeSuggestion::query()->count());
    }

    public function testInStockSinceDoesntAddSuggestion(): void
    {
        StocktakeSuggestionsServiceProvider::enableModule();

        StocktakeSuggestionsConfiguration::updateOrCreate([], ['min_count_date' => now()->subWeek()]);

        Warehouse::factory()->create();

        Product::factory()->create()
            ->inventory()
            ->first()
            ->update([
                'last_counted_at' => now()->subMonth(),
                'first_movement_at' => now()->subMonth(),
                'in_stock_since' => now()->subDay(),
                'quantity' => 1,
            ]);

        $this->assertEquals(0, StocktakeSuggestion::query()->count());
    }

    public function testOutdatedCountsSuggestionAdding(): void
    {
        StocktakeSuggestionsServiceProvider::enableModule();

        StocktakeSuggestionsConfiguration::updateOrCreate([], ['min_count_date' => now()->subWeek()]);

        Warehouse::factory()->create();

        Product::factory()->create()
            ->inventory()
            ->first()
            ->update([
                'last_counted_at' => now()->subMonth(),
                'first_movement_at' => now()->subMonth(),
                'in_stock_since' => now()->subMonth(),
                'quantity' => 1,
            ]);

        $this->assertEquals(1, StocktakeSuggestion::query()->count());
    }

    public function testOutdatedCountsSuggestion(): void
    {
        StocktakeSuggestionsServiceProvider::enableModule();

        StocktakeSuggestionsConfiguration::updateOrCreate([], ['min_count_date' => now()->subDay()]);

        Warehouse::factory()->create();

        Product::factory()->create()
            ->inventory()
            ->first()
            ->update([
                'last_counted_at' => now()->subMonth(),
                'in_stock_since' => now()->subMonth(),
                'quantity' => 1,
            ]);

        Product::factory()->create()
            ->inventory()
            ->first()
            ->update([
                'last_counted_at' => null,
                'in_stock_since' => now()->subMonth(),
                'quantity' => 1,
            ]);

        Product::factory()->create()
            ->inventory()
            ->first()
            ->update([
                'last_counted_at' => null,
                'in_stock_since' => now(),
                'quantity' => 1,
            ]);

        $this->assertEquals(2, StocktakeSuggestion::count());
    }

    public function testOutdatedCountsJob(): void
    {
        StocktakeSuggestionsConfiguration::updateOrCreate([], ['min_count_date' => now()->subDay()]);

        Warehouse::factory()->create();

        Product::factory()->create()
            ->inventory()
            ->first()
            ->update([
                'last_counted_at' => now()->subMonth(),
                'in_stock_since' => now()->subMonth(),
                'quantity' => 1,
            ]);

        Product::factory()->create()
            ->inventory()
            ->first()
            ->update([
                'last_counted_at' => null,
                'in_stock_since' => now()->subMonth(),
                'quantity' => 1,
            ]);

        Product::factory()->create()
            ->inventory()
            ->first()
            ->update([
                'last_counted_at' => null,
                'in_stock_since' => now(),
                'quantity' => 1,
            ]);

        OutdatedCountsJob::dispatch();

        $this->assertEquals(2, StocktakeSuggestion::count());
    }

    public function testModuleBasicFunctionality(): void
    {
        Warehouse::factory()->create();

        /** @var Product $product */
        $product = Product::factory()->create();

        $inventory = $product->inventory()->first();

        StocktakeSuggestion::query()->create([
            'product_id' => $product->id,
            'inventory_id' => $inventory->id,
            'points' => 1,
            'reason' => 'test',
        ]);

        StocktakeSuggestion::query()->create([
            'product_id' => $product->id,
            'inventory_id' => $inventory->id,
            'points' => 1,
            'reason' => 'test2',
        ]);

        $this->assertCount(2, StocktakeSuggestion::query()->get());
    }
}
