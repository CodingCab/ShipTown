<?php

namespace App\Http\Controllers\Api;

use App\Models\OrderAddress;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderAddressResource;
use App\Http\Requests\UpdateOrderAddressRequest;

/**
 *
 */
class OrderAddressController extends Controller
{
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderAddressRequest $request, OrderAddress $address): OrderAddressResource
    {
        $address->update($request->validated());

        return OrderAddressResource::make($address);
    }
}
