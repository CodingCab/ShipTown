<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transaction\UpdateRequest;
use App\Http\Resources\DataCollectionResource;
use App\Models\DataCollection;

class TransactionController extends Controller
{
    public function update(UpdateRequest $request, int $transactionId): DataCollectionResource
    {
        $transaction = DataCollection::findOrFail($transactionId);

        $transaction->update($request->validated());

        return DataCollectionResource::make($transaction);
    }
}
