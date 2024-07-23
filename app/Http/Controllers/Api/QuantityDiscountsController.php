<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

//use App\Http\Requests\Warehouse\StoreRequest;
//use App\Http\Requests\Warehouse\UpdateRequest;
use App\Http\Resources\QuantityDiscountsResource;
use App\Models\Warehouse;
use App\Modules\QuantityDiscounts\src\Models\QuantityDiscount;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\Tags\Tag;

class QuantityDiscountsController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = QuantityDiscount::getSpatieQueryBuilder()->defaultSort('id');
        return QuantityDiscountsResource::collection($this->getPaginatedResult($query, 999));
    }

    public function store(StoreRequest $request): QuantityDiscountsResource
    {
//        $warehouse = Warehouse::create($request->validated());
//
//        return QuantityDiscountsResource::make($warehouse);
    }

    public function update(UpdateRequest $request, int $warehouse_id): QuantityDiscountsResource
    {
//        $warehouse = Warehouse::findOrFail($warehouse_id);
//
//        $warehouse->update($request->validated());
//
//        $tags = data_get($request->validated(), 'tags', []);
//
//        $tags = collect($tags)->filter()->map(function ($tag) use ($warehouse) {
//            $warehouse->attachTag($tag);
//            return Tag::findFromString($tag);
//        });
//
//        $warehouse->tags()->sync($tags->pluck('id'));
//
//        return QuantityDiscountsResource::make($warehouse);
    }

    public function destroy(int $warehouse_id): QuantityDiscountsResource
    {
//        $warehouse = Warehouse::findOrFail($warehouse_id);
//
//        $warehouse->delete();
//
//        return QuantityDiscountsResource::make($warehouse);
    }
}
