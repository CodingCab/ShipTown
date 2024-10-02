@extends('layouts.app')

@section('title', __('Payments - Settings'))

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                <payments-configuration-page></payments-configuration-page>
            </div>
        </div>
@endsection
