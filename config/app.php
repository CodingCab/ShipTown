<?php

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Facade;

return [

    'license_valid_until' => env('APP_LICENSE_VALID_UNTIL', '2025-06-01 00:00:00'),

    'demo_mode' => env('DEMO_MODE', false),

    'tenant_name' => env('TENANT_NAME', 'demo'),

    'sns_topic_prefix' => '',

    'api2cart_api_key' => env('API2CART_API_KEY', ''),


    'aliases' => Facade::defaultAliases()->merge([
        'AWS' => Aws\Laravel\AwsFacade::class,
        'DNS1D' => Milon\Barcode\Facades\DNS1DFacade::class,
        'DNS2D' => Milon\Barcode\Facades\DNS2DFacade::class,
        'PDF' => Barryvdh\DomPDF\Facade::class,
        'Redis' => Illuminate\Support\Facades\Redis::class,
        'Sentry' => Sentry\Laravel\Facade::class,
    ])->toArray(),

];
