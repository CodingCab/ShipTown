<?php

namespace App\Modules\MagentoApi\src\Jobs;

use App\Abstracts\UniqueJob;
use App\Modules\MagentoApi\src\Models\MagentoProduct;
use App\Modules\MagentoApi\src\Services\MagentoService;

/**
 * Class SyncCheckFailedProductsJob.
 */
class SyncProductBasePricesJob extends UniqueJob
{
    public function handle(): void
    {
        MagentoProduct::query()
            ->with(['magentoConnection', 'product', 'prices'])
            ->where(['base_price_sync_required' => true])
            ->chunkById(10, function ($products) {
                collect($products)->each(function (MagentoProduct $magentoProduct) {
                    MagentoService::updateBasePrice(
                        $magentoProduct->product->sku,
                        $magentoProduct->prices->price,
                        $magentoProduct->magentoConnection->magento_store_id
                    );

                    $magentoProduct->update([
                        'base_prices_fetched_at' => null,
                        'base_prices_raw_import' => null,
                        'magento_price'          => null,
                    ]);
                });
            });
    }
}
