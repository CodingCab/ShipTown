@extends('layouts.app')

@section('title', __('Vat Rates - Settings'))

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                <vat-rates-configuration-page></vat-rates-configuration-page>
            </div>
        </div>
@endsection
