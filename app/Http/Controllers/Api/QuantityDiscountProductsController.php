<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\QuantityDiscountProduct\DestroyRequest;
use App\Http\Requests\QuantityDiscountProduct\StoreRequest;
use App\Models\Product;
use App\Modules\QuantityDiscounts\src\Models\QuantityDiscount;
use App\Modules\QuantityDiscounts\src\Models\QuantityDiscountsProduct;
use Illuminate\Http\JsonResponse;

class QuantityDiscountProductsController extends Controller
{
    public function store(StoreRequest $request): JsonResponse
    {
        QuantityDiscountsProduct::query()->create($request->validated());

        $discountProducts = QuantityDiscountsProduct::query()
            ->where(['quantity_discount_id' => $request->validated('quantity_discount_id')])
            ->with('product')
            ->get();

        return response()->json($discountProducts);
    }

    public function destroy(DestroyRequest $request, int $quantity_discount_product_id): JsonResponse
    {
        \Log::info('Destroy method called with id: ' . $quantity_discount_product_id);
        \Log::info('Request data: ' . json_encode($request->all()));
        $product = QuantityDiscountsProduct::findOrFail($quantity_discount_product_id);

        $discount = QuantityDiscount::findOrFail($product->quantity_discount_id);
        if ($product->delete()) {
            $discountProducts = $discount->products()->get();
            $products = $this->getDiscountProducts($discountProducts);
            return response()->json($products);
        } else {
            return response()->json(['message' => 'A problem occurred when removing a product.'], 400);
        }
    }

    private function getDiscountProducts($discountProducts): array
    {
        return Product::query()
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
}
