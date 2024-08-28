<?php

namespace App\Modules\MagentoApi\src\Services;

use App\Modules\MagentoApi\src\Api\MagentoApi;
use App\Modules\MagentoApi\src\Exceptions\UnauthorizedException;
use App\Modules\MagentoApi\src\Models\MagentoProduct;
use Exception;
use Grayloon\Magento\Magento;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class MagentoService
{
    public static function api(): MagentoApi
    {
        return new MagentoApi(new Magento());
    }

    public static function updateBasePrice(string $sku, float $price, int $store_id)
    {
        self::api()->postProductsBasePrices(
            $sku,
            $price,
            $store_id
        );
    }

    public static function updateSalePrice(string $sku, float $sale_price, $start_date, $end_date, int $store_id)
    {
        $response = self::api()->postProductsSpecialPrice(
            $sku,
            $store_id,
            $sale_price,
            $start_date,
            $end_date
        );

        if (! $response->successful()) {
            Log::error('Failed to fetch sale prices for product '.$sku);
        }
    }

    public static function fetchSpecialPrices(MagentoProduct $magentoProduct)
    {
        $response = self::api()->postProductsSpecialPriceInformation($magentoProduct->product->sku);

        if ($response === null || $response->failed()) {
            Log::error('Failed to fetch sale prices for product '.$magentoProduct->product->sku);
            return;
        }

        $specialPrices = collect($response->json())
            ->filter(function ($apiSpecialPriceRecord) use ($magentoProduct) {
                return $apiSpecialPriceRecord['store_id'] == $magentoProduct->magentoConnection->magento_store_id;
            });

        // magento sometimes randomly returns multiple special prices for the same store,
        // so we need to filter them out but only one is valid
        // randomizing result will match it sometimes, normally 3 special prices are returned,
        // so we will have statistically 1/3 chance to get the correct one,
        // it's a quick hack, but it works
        $specialPrices = $specialPrices->shuffle();

        $specialPrice = $specialPrices->first();

        if ($specialPrice) {
            $magentoProduct->magento_sale_price = $specialPrice['price'];
            $magentoProduct->magento_sale_price_start_date = $specialPrice['price_from'];
            $magentoProduct->magento_sale_price_end_date = $specialPrice['price_to'];
        }

        $magentoProduct->special_prices_fetched_at = now();
        $magentoProduct->special_prices_raw_import = $response->json();
        $magentoProduct->save();
    }

    /**
     * @throws UnauthorizedException
     */
    public static function fetchBasePrices(MagentoProduct $magentoProduct): void
    {
        $response = self::api()->postProductsBasePricesInformation($magentoProduct->product->sku);

        if ($response->unauthorized()) {
            throw new UnauthorizedException();
        }

        if ($response === null || $response->failed()) {
            Log::error('Failed to fetch base prices for product '.$magentoProduct->product->sku);
            return;
        }

        $magentoProduct->base_prices_fetched_at = now();
        $magentoProduct->base_prices_raw_import = $response->json();

        collect($response->json())
            ->filter(function ($item) use ($magentoProduct) {
                return $item['store_id'] === $magentoProduct->magentoConnection->magento_store_id;
            })
            ->each(function ($item) use ($magentoProduct) {
                $magentoProduct->magento_price = $item['price'];
            });

        $magentoProduct->save();
    }

    public static function fetchInventory(MagentoProduct $magentoProduct)
    {
        if (config('magento.store_code') === 'all') {
            self::fetchStockItem($magentoProduct);
            return;
        }

        self::fetchFromInventorySourceItems($magentoProduct);
    }

    public static function updateInventory(string $sku, float $quantity)
    {
        if (config('magento.store_code') === 'all') {
            self::updateStockItems($sku, $quantity);
            return;
        }

        self::updateInventorySourceItems($sku, $quantity);
    }

    private static function updateStockItems(string $sku, float $quantity): void
    {
        $params = [
            'is_in_stock' => $quantity > 0,
            'qty' => $quantity,
        ];

        $response = self::api()->putStockItems($sku, $params);

        Log::debug('MagentoApi: stockItem update', [
            'sku'                  => $sku,
            'response_status_code' => $response->status(),
            'response_body'        => $response->json(),
            'params'               => $params
        ]);
    }

    private static function updateInventorySourceItems(string $sku, float $quantity): void
    {
        $response = self::api()->postInventorySourceItems($sku, config('magento.store_code'), $quantity);

        Log::debug('MagentoApi: updateInventorySourceItems', [
            'sku'                  => $sku,
            'response_status_code' => $response->status(),
            'response_body'        => $response->json(),
        ]);
    }

    /**
     * @throws Exception
     */
    private static function fetchStockItem(MagentoProduct $product)
    {
        $response = self::api()->getStockItems($product->product->sku);

        if ($response === null) {
            throw new Exception('Magento API call returned null '.$product->product->sku);
        }

        if ($response->notFound()) {
            $product->update(['exists_in_magento' => false]);
            return;
        }

        if ($response->failed()) {
            throw new Exception('Failed to fetch stock items for product '.$product->product->sku);
        }

        $product->stock_items_raw_import    = $response->json();
        $product->stock_items_fetched_at    = now();
        $product->quantity                  = null;

        if (Arr::has($response->json(), 'qty')) {
            $product->quantity = data_get($response->json(), 'qty') ?: 0;
        }

        $product->save();
    }

    /**
     * @throws Exception
     */
    private static function fetchFromInventorySourceItems(MagentoProduct $product)
    {
        $response = self::api()->getInventorySourceItems($product->product->sku, config('magento.store_code'));

        if ($response === null || $response->failed()) {
            throw new Exception('Failed to fetch stock items for product '.$product->product->sku);
        }

        $product->stock_items_fetched_at = now();
        $product->stock_items_raw_import = data_get($response->json(), 'items.0');

        if (data_get($response->json(), 'items.0')) {
            $product->quantity = data_get($response->json(), 'items.0.quantity') ?: 0;
        } else {
            $product->quantity = null;
        }

        $product->save();
    }
}
