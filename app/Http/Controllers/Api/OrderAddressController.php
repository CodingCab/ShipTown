<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductsRequest;
use App\Http\Resources\OrderAddressResource;
use App\Models\OrderAddress;
use Illuminate\Http\Request;

class OrderAddressController extends Controller
{
    public function index(Request $request)
    {
        $query = OrderAddress::getSpatieQueryBuilder()
            ->simplePaginate(request()->input('per_page', 10));

        return OrderAddressResource::collection($query);
    }

    public function store(StoreProductsRequest $request)
    {
//        $product = Product::query()->updateOrCreate(
//            ['sku' => $request->sku],
//            $request->validated()
//        );
//
//        return response()->json($product, 200);
    }

//    public function publish($sku)
//    {
//        $product = Product::query()->where('sku', $sku)->firstOrFail();
//
//        $product->save();
//
//        $this->respondOK200();
//    }
}
