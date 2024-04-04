<?php

namespace App\Http\Controllers\Api\Reports;

use App\Modules\StocktakeSuggestions\src\Reports\StoctakeSuggestionReport;

class StockTakeSuggestionsController
{
    public function index()
    {
        return StoctakeSuggestionReport::toJsonResource();
    }
}
