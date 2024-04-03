<?php

namespace App\Http\Controllers\Api\Reports;

use App\Modules\StocktakeSuggestions\src\Reports\StoctakeSuggestionReport;
use Illuminate\Http\Resources\Json\JsonResource;

class StockTakeSuggestionsController
{
    public function index()
    {
        $data = (new StoctakeSuggestionReport())
            ->queryBuilder()
            ->offset(request('page') * request('per_page'))
            ->limit(request('per_page'))
            ->get()
        ;

        return JsonResource::collection($data);
    }
}
