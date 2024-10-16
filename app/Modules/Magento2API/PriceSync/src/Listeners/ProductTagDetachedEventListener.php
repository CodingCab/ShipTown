<?php

namespace App\Modules\Magento2API\PriceSync\src\Listeners;

use App\Events\Product\ProductTagDetachedEvent;
use App\Modules\Magento2API\PriceSync\src\Models\MagentoProduct;

class ProductTagDetachedEventListener
{
    public function handle(ProductTagDetachedEvent $event): void
    {
        if ($event->tag() === 'Available Online') {
            MagentoProduct::query()
                ->where(['product_id' => $event->product->id])
                ->delete();
        }
    }
}
