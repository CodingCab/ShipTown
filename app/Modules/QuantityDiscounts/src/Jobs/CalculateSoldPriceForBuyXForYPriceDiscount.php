<?php

namespace App\Modules\QuantityDiscounts\src\Jobs;

use App\Abstracts\UniqueJob;
use App\Models\DataCollection;
use App\Modules\QuantityDiscounts\src\Models\QuantityDiscount;

class CalculateSoldPriceForBuyXForYPriceDiscount extends UniqueJob
{
    private QuantityDiscount $discount;
    private DataCollection $dataCollection;

    public function __construct(DataCollection $dataCollection, QuantityDiscount $discount)
    {
        $this->discount = $discount;
        $this->dataCollection = $dataCollection;
    }

    public function handle(): array
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
