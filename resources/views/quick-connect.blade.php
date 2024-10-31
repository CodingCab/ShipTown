@extends('layouts.body')

@section('title', __('Quick Connect'))

@section('app-content')
    <div class="col-3 mx-auto mt-5 text-center">
{{--        <div class="h3">Connect</div>--}}
{{--        <img src="{{ asset('img/logos/magento_shopify.png') }}" alt="Magento Logo">--}}
        <a href="quick-connect/magento" class="btn btn-block d-flex align-items-center justify-content-center">
            <img src="{{ asset('img/logos/magento2.png') }}" alt="Magento Logo">
        </a>

        <a href="quick-connect/shopify" class="btn btn-block d-flex align-items-center justify-content-center">
            <img src="{{ asset('img/logos/shopify.svg') }}" alt="Magento Logo" style="width: 85%;">
        </a>

        <br><br>

        <a href="/products" class="btn btn-primary btn-block">Skip</a>
    </div>
@endsection
