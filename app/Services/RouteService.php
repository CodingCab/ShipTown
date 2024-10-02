<?php

namespace App\Services;

use Illuminate\Routing\PendingResourceRegistration;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class RouteService
{
    public static function apiResource(string $uri, string $controllerClass = null, array $only = ['index', 'store', 'update', 'destroy']): PendingResourceRegistration
    {
        $endpoints = collect(explode('/', $uri));

        $proposedControllerName = Str::singular($endpoints->last());
        $proposedControllerName = Str::ucfirst($proposedControllerName);

        // example: App\Http\Controllers\Api\WarehouseController
        $controllerClass = $controllerClass ?? 'App\\Http\\Controllers\\Api\\' . $proposedControllerName . 'Controller';

        $endpoints->pop();

        return Route::apiResource($uri, $controllerClass, ['as' => $endpoints->implode('')])->only($only);
    }
}
