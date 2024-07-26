<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\QuantityDiscountProduct\DestroyRequest;
use App\Http\Requests\QuantityDiscountProduct\IndexRequest;
use App\Http\Requests\QuantityDiscountProduct\StoreRequest;
use App\Modules\QuantityDiscounts\src\Models\QuantityDiscountsProduct;
use Illuminate\Http\JsonResponse;

class QuantityDiscountProductsController extends Controller
{
    public function index(IndexRequest $request): JsonResponse
    {
        $result = QuantityDiscountsProduct::getSpatieQueryBuilder()
            ->get();

        return response()->json($result);
    }

    public function store(StoreRequest $request): JsonResponse
    {
        QuantityDiscountsProduct::query()->firstOrCreate($request->validated());

        $discountProducts = QuantityDiscountsProduct::query()
            ->where(['quantity_discount_id' => $request->validated('quantity_discount_id')])
            ->with('product')
            ->get();

        return response()->json($discountProducts);
    }

    public function destroy(DestroyRequest $request, QuantityDiscountsProduct $quantityDiscountProduct): JsonResponse
    {
        $quantityDiscountProduct->delete();

        $discountProducts = QuantityDiscountsProduct::query()
            ->where(['quantity_discount_id' => $quantityDiscountProduct->quantity_discount_id])
            ->with('product')
            ->get();

        return response()->json($discountProducts);
    }
}
