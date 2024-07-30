<?php

namespace App\Modules\QuantityDiscounts\src\Jobs;

use App\Modules\QuantityDiscounts\src\Models\QuantityDiscount;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class CalculateSoldPriceForBuyXGetYForZPercentDiscount implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private QuantityDiscount $discount;
    private Collection $filteredCollectionRecords;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(QuantityDiscount $discount, Collection $filteredCollectionRecords)
    {
        $this->discount = $discount;
        $this->filteredCollectionRecords = $filteredCollectionRecords;
    }

    /**
     * Execute the job.
     *
     * @return array
     */
    public function handle()
    {
        dd($this->discount->configuration);
        $minPrice = $this->filteredCollectionRecords->min('unit_full_price');
        $lowestPriceRecord = $this->filteredCollectionRecords->firstWhere('unit_full_price', $minPrice);

        $prices = [
            'current_sold_price' => $lowestPriceRecord->unit_sold_price,
            'current_unit_discount' => $lowestPriceRecord->unit_discount,
            'calculated_sold_price' => $lowestPriceRecord->unit_full_price - ($lowestPriceRecord->unit_full_price * $this->discount->configuration['discount_percent'] / 100),
        ];
        $prices['calculated_unit_discount'] = $lowestPriceRecord->unit_full_price - $prices['calculated_sold_price'];

        return $prices;
    }
}
