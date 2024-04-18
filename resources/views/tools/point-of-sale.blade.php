@extends('layouts.app')

@section('title', __('ShipTown ePOS'))

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <epos></epos>
            </div>
        </div>
    </div>
@endsection
