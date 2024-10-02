<?php

namespace App\Services;

use Illuminate\Routing\PendingResourceRegistration;
use Illuminate\Support\Facades\Route;

class RouteService
{
    public static function apiResource(string $str, string $controller, array $only = ['index', 'store', 'update', 'destroy']): PendingResourceRegistration
    {
        $uri = collect(explode('/', $str));

        $uri->pop();

        $endpoints = $uri->implode('');

//        $endpoints= '';
        return Route::apiResource($str, $controller, ['as' => $endpoints])->only($only);
    }
}
