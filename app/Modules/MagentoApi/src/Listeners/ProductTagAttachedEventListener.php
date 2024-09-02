<?php

namespace App\Modules\MagentoApi\src\Listeners;

use App\Events\Product\ProductTagAttachedEvent;
use App\Modules\MagentoApi\src\Models\MagentoConnection;
use App\Modules\MagentoApi\src\Models\MagentoProduct;

class ProductTagAttachedEventListener
{
    /**
     * Handle the event.
     */
    public function handle(ProductTagAttachedEvent $event): void
    {
        if ($event->tag === 'Available Online') {
            MagentoConnection::query()
                ->get()
                ->each(function (MagentoConnection $connection) use ($event) {
                    MagentoProduct::firstOrCreate([
                        'connection_id' => $connection->getKey(),
                        'product_id' => $event->product->id
                    ], []);
                });
        }
    }
}
