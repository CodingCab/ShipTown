<?php

namespace App\Services;

use Illuminate\Routing\PendingResourceRegistration;
use Illuminate\Support\Facades\Route;

class RouteService
{

    public static function apiResource(string $str, string $controller, array $only = ['index', 'store', 'update', 'destroy']): PendingResourceRegistration
    {
        return Route::apiResource($str, $controller)->only($only);
    }
}
