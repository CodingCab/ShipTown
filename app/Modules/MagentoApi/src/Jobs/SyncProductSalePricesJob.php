<?php

namespace App\Modules\MagentoApi\src\Jobs;

use App\Abstracts\UniqueJob;
use App\Modules\MagentoApi\src\Models\MagentoProduct;
use App\Modules\MagentoApi\src\Models\MagentoProductPricesComparisonView;
use App\Modules\MagentoApi\src\Services\MagentoService;

/**
 * Class SyncCheckFailedProductsJob.
 */
class SyncProductSalePricesJob extends UniqueJob
{
    public function handle(): void
    {
        MagentoProduct::query()
            ->with(['magentoConnection', 'product', 'prices'])
            ->where(['base_price_sync_required' => true])
            ->chunkById(10, function ($products) {
                collect($products)->each(function (MagentoProduct $magentoProduct) {
                    MagentoService::updateSalePrice(
                        $magentoProduct->product->sku,
                        $magentoProduct->prices->sale_price,
                        $magentoProduct->prices->sale_price_start_date->format('Y-m-d H:i:s'),
                        $magentoProduct->prices->sale_price_end_date->format('Y-m-d H:i:s'),
                        $magentoProduct->magentoConnection->magento_store_id
                    );

                    $magentoProduct->update([
                        'special_prices_fetched_at'     => null,
                        'special_prices_raw_import'     => null,
                        'magento_sale_price'            => null,
                        'magento_sale_price_start_date' => null,
                        'magento_sale_price_end_date'   => null,
                    ]);
                });
            });

        MagentoProductPricesComparisonView::query()
            ->whereRaw('special_prices_fetched_at IS NOT NULL

            AND (
                IFNULL(magento_sale_price, 0) != expected_sale_price
                OR magento_sale_price_start_date != expected_sale_price_start_date
                OR magento_sale_price_end_date != expected_sale_price_end_date
                OR magento_sale_price IS NULL
                OR magento_sale_price_start_date IS NULL
                OR magento_sale_price_end_date IS NULL
            )')
            ->chunkById(100, function ($products) {
                collect($products)->each(function (MagentoProductPricesComparisonView $comparison) {
                });
            }, 'modules_magento2api_products_id');
    }
}
