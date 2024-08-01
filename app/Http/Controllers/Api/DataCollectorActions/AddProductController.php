<?php

namespace App\Http\Controllers\Api\DataCollectorActions;

use App\Http\Requests\Api\DataCollectorActions\AddProductStoreRequest;
use App\Models\DataCollection;
use App\Models\DataCollectionRecord;
use App\Models\Inventory;
use App\Models\ProductAlias;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class AddProductController
{
    public function store(AddProductStoreRequest $request): AnonymousResourceCollection
    {
        $dataCollectionRecord = $this->findOrCreateRecord($request);

        $fieldName = $request->has('quantity_scanned') ? 'quantity_scanned' : 'quantity_requested';
        $dataCollectionRecord->increment($fieldName, $request->validated($fieldName, 0));

        return JsonResource::collection(Arr::wrap($dataCollectionRecord));
    }

    public function findOrCreateRecord(AddProductStoreRequest $request): DataCollectionRecord
    {
        $productId = ProductAlias::query()
            ->where(['alias' => $request->validated('sku_or_alias')])
            ->first('product_id')->product_id;
        $warehouseId = DataCollection::query()
            ->find($request->validated('data_collection_id'), ['warehouse_id'])->warehouse_id;
        $inventory = Inventory::query()
            ->with('prices')
            ->where(['product_id' => $productId, 'warehouse_id' => $warehouseId])
            ->first();

        return DataCollectionRecord::query()
            ->where([
                'data_collection_id' => $request->validated('data_collection_id'),
                'product_id' => $productId,
                'unit_sold_price' => data_get($inventory, 'prices.current_price'),
//                'price_source' => null,
            ])
            ->firstOr(function () use ($request, $productId, $inventory) {
                return DataCollectionRecord::query()->create([
                    'unit_cost' => data_get($inventory, 'prices.cost'),
                    'unit_full_price' => data_get($inventory, 'prices.price'),
                    'unit_sold_price' => data_get($inventory, 'prices.current_price'),
//                    'price_source' => data_get($inventory, 'prices.price') === data_get($inventory, 'prices.current_price') ? 'FULL_PRICE' : 'SALE_PRICE',
                    'data_collection_id' => $request->validated('data_collection_id'),
                    'inventory_id' => $inventory->id,
                    'warehouse_id' => $inventory->warehouse_id,
                    'warehouse_code' => $inventory->warehouse_code,
                    'product_id' => $inventory->product_id,
                    'quantity_requested' => 0,
                ]);
            });
    }
}
