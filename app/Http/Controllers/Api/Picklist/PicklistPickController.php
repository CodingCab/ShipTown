<?php

namespace App\Http\Controllers\Api\Picklist;

use App\Http\Controllers\Controller;
use App\Http\Requests\PickDestroyRequest;
use App\Http\Requests\Picklist\StoreDeletedPickRequest;
use App\Models\OrderProduct;
use App\Models\Pick;
use App\Modules\AutoStatusPicking\src\Jobs\DistributePicksJob;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class PicklistPickController.
 */
class PicklistPickController extends Controller
{
    /**
     * @param StoreDeletedPickRequest $request
     *
     * @return JsonResource
     */
    public function store(StoreDeletedPickRequest $request): JsonResource
    {
        $orderProducts = OrderProduct::query()
            ->whereIn('id', $request->get('order_product_ids'))
            ->oldest('order_id')
            ->get();

        $first = $orderProducts->first();

        /** @var Pick $pick */
        $pick = Pick::query()->create([
            'user_id' => request()->user()->getKey(),
            'warehouse_code' => request()->user()->warehouse->code,
            'product_id' => $first['product_id'],
            'sku_ordered' => $first['sku_ordered'],
            'name_ordered' => $first['name_ordered'],
            'quantity_picked' => $request->get('quantity_picked', 0),
            'quantity_skipped_picking' => $request->get('quantity_skipped_picking', 0),
            'is_distributed' => false,
            'order_product_ids' => $request->get('order_product_ids'),
        ]);

        DistributePicksJob::dispatchAfterResponse($pick);

        return JsonResource::make([$pick]);
    }

    public function destroy(PickDestroyRequest $request, Pick $pick): JsonResource
    {
        $pick->delete();

        return JsonResource::make([$pick]);
    }
}
