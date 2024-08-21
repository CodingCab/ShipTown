@extends('layouts.app')

@section('title',__('Orders'))

@section('content')
    <report
        meta-string="{{ json_encode($meta) }}"
        record-string="{{ json_encode($data) }}"
        download-button-text="{{ __('Download All') }}"
    ></report>
@endsection
