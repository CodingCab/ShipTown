<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Http\Requests\QuantityDiscount\StoreRequest;

//use App\Http\Requests\Warehouse\UpdateRequest;
use App\Http\Resources\QuantityDiscountsResource;
use App\Models\Product;
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
        $discount = QuantityDiscount::create($request->validated());
        return QuantityDiscountsResource::make($discount);
    }

    public function edit(int $discount_id)
    {
        try {
            $discount = QuantityDiscount::where('id', $discount_id)->firstOrFail();
            $discountProducts = $discount->products()->get();
            $products = [];

            if ($discountProducts->count()) {
                $products = Product::query()
                    ->whereIn('id', $discountProducts->pluck('product_id'))
                    ->get()
                    ->map(function ($product) use ($discountProducts) {
                        $discountProduct = $discountProducts->firstWhere('product_id', $product->id);
                        $product = $product->toArray();
                        $product['discount_product_id'] = $discountProduct ? $discountProduct->id : null;
                        return $product;
                    })
                    ->toArray();
            }

            return view('settings.modules.quantity-discounts.edit', ['discount' => $discount->toArray(), 'products' => $products]);
        } catch (\Exception $e) {
            abort(404, 'Discount not found');
        }
    }

    public function update(UpdateRequest $request, int $discount_id): QuantityDiscountsResource
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

    public function destroy(int $discount_id): QuantityDiscountsResource
    {
//        $warehouse = Warehouse::findOrFail($warehouse_id);
//
//        $warehouse->delete();
//
//        return QuantityDiscountsResource::make($warehouse);
    }
}
