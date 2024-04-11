@extends('pdf.template')
@section('content')

    @foreach($labels as $label)
        @php
            $fontSize = strlen($label) > 10 ? '65px' : '85px';
        @endphp
        <div style="height: 50%; overflow: hidden;">
            <h1 style="text-align: center; font-size: {{$fontSize}}; margin-top: 10px; word-wrap: anywhere; line-height: 90%;">{{ $label }}</h1>
        </div>
        <img style="width: 180px; height: 180px; margin-top:10px; margin-left: 88px;" src="data:image/png;base64,{{ DNS2D::getBarcodePNG('shelf:'.$label, 'QRCODE') }}" alt="barcode" />
        <p style="text-align: center; font-size: 22px;  margin-top: 5px; word-wrap: anywhere;">shelf:{{ $label }}</p>
        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach

    <style>
        @page {
            size: 101.6mm 152.4mm;
            margin: 3mm;
        }

        .page-break {
            page-break-after: always;
        }

        h1, p {
            margin: 0;
            padding: 0;
        }
    </style>

@endsection
