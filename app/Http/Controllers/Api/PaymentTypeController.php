<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentType\IndexRequest;
use App\Http\Resources\PaymentTypeResource;
use App\Models\PaymentType;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PaymentTypeController extends Controller
{
    public function index(IndexRequest $request): AnonymousResourceCollection
    {
        $query = PaymentType::getSpatieQueryBuilder()->defaultSort('name');

        return PaymentTypeResource::collection($this->getPaginatedResult($query, 999));
    }
}
