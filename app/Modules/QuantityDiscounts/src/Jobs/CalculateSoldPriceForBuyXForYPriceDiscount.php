<?php

namespace App\Modules\QuantityDiscounts\src\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class CalculateSoldPriceForBuyXGetYForZPercentDiscount implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private array $configuration;
    private Collection $collectionRecords;
    private Collection $discountedProducts;

    /**
     * Create a new job instance.
     *
     * @return void
     */
//, Collection $discountedProducts
    public function __construct(array $configuration, Collection $collectionRecords)
    {
        $this->configuration = $configuration;
        $this->collectionRecords = $collectionRecords;
//        $this->discountedProducts = $discountedProducts;
    }

    /**
     * Execute the job.
     *
     * @return array
     */
    public function handle()
    {
        $minPrice = $this->collectionRecords->min('unit_full_price');
        $lowestPriceRecord = $this->collectionRecords->firstWhere('unit_full_price', $minPrice);

        $prices = [
            'current_sold_price' => $lowestPriceRecord->unit_sold_price,
            'current_unit_discount' => $lowestPriceRecord->unit_discount,
            'calculated_sold_price' => $lowestPriceRecord->unit_full_price - ($lowestPriceRecord->unit_full_price * $this->configuration['discount_percent'] / 100),
        ];
        $prices['calculated_unit_discount'] = $lowestPriceRecord->unit_full_price - $prices['calculated_sold_price'];

        return $prices;
    }
}