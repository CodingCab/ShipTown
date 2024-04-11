@extends('pdf.template')
@section('content')


    @foreach(collect($labels)->chunk(2) as $chunk)
        @foreach($chunk as $index => $label)
            @php
                $fontSize = strlen($label) > 10 ? '40px' : '70px';
                $marginTop = strlen($label) > 10 ? '5px' : '10px';
            @endphp
            <div class="half_{{ $index % 2 === 0 ? 'first' : 'second'}}">
                <div style="height: 50%; overflow: hidden;">
                    <h1 style="text-align: center; font-size: {{ $fontSize }}; word-wrap: anywhere; line-height: 90%; margin-top: {{ $marginTop }};">{{ $label }}</h1>
                </div>
                <img style="width: 120px; height: 120px; margin-left: 75px; margin-top: 2px;" src="data:image/png;base64,{{ DNS2D::getBarcodePNG('shelf:'.$label, 'QRCODE') }}" alt="barcode" />
                <p style="text-align: center; font-size: 16px; margin-top: 4px;  word-wrap: anywhere;">shelf:{{ $label }}</p>
            </div>
        @endforeach
        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach

    <style>
        @page {
            size:152.4mm 101.6mm;
            margin: 3mm;
        }

        .page-break {
            page-break-after: always;
        }

        .half_first {
            width: 49%;
            float: right;
        }

        .half_second {
            width: 49%;
            float: left;
        }

        h1, p, img{
            margin: 0;
            padding: 0;
        }
    </style>

@endsection
