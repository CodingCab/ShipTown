<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\VatRate\StoreRequest;
use App\Http\Requests\VatRate\UpdateRequest;
use App\Http\Resources\VatRateResource;
use App\Modules\VatRates\src\Models\VatRate;

class VatRateController extends Controller
{
    public function index()
    {
        $query = VatRate::getSpatieQueryBuilder()->defaultSort('rate');

        return VatRateResource::collection($this->getPaginatedResult($query, 999));
    }

    public function store(StoreRequest $request): VatRateResource
    {
        $vatRate = VatRate::create($request->validated());

        return VatRateResource::make($vatRate);
    }

    public function update(UpdateRequest $request, int $vatRateId): VatRateResource
    {
        $vatRate = VatRate::findOrFail($vatRateId);

        $vatRate->update($request->validated());

        return VatRateResource::make($vatRate);
    }

    public function destroy(int $vatRateId): VatRateResource
    {
        $vatRate = VatRate::findOrFail($vatRateId);
        $vatRate->delete();

        return VatRateResource::make($vatRate);
    }
}
