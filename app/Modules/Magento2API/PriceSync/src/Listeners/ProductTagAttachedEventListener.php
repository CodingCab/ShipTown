<?php

namespace App\Modules\Magento2API\PriceSync\src\Listeners;

use App\Events\Product\ProductTagAttachedEvent;
use App\Modules\Magento2API\PriceSync\src\Models\MagentoConnection;
use App\Modules\Magento2API\PriceSync\src\Models\MagentoProduct;

class ProductTagAttachedEventListener
{
    public function handle(ProductTagAttachedEvent $event): void
    {
        if ($event->tag === 'Available Online') {
            MagentoConnection::query()
                ->get()
                ->each(function (MagentoConnection $connection) use ($event) {
                    MagentoProduct::query()->firstOrCreate([
                        'connection_id' => $connection->getKey(),
                        'product_id' => $event->product->id,
                    ], []);
                });
        }
    }
}
