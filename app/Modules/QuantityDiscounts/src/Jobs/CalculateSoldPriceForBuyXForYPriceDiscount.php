<?php

namespace App\Modules\QuantityDiscounts\src\Jobs;

use App\Modules\QuantityDiscounts\src\Models\QuantityDiscount;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class CalculateSoldPriceForBuyXForYPriceDiscount implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private QuantityDiscount $discount;
    private Collection $collectionRecords;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(QuantityDiscount $discount, Collection $collectionRecords)
    {
        $this->discount = $discount;
        $this->collectionRecords = $collectionRecords;
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
            'calculated_sold_price' => $this->configuration['discounted_unit_price'],
        ];
        $prices['calculated_unit_discount'] = $lowestPriceRecord->unit_full_price - $prices['calculated_sold_price'];

        return $prices;
    }
}
