@extends('pdf.template')
@section('content')

    @foreach(collect($labels)->chunk(2) as $chunk)
        @foreach($chunk as $index => $label)
            <div class="half_{{ $index === 0 ? 'first' : 'second'}}">
                <h1 style="text-align: center; font-size: 60px; margin-top: 5px;">{{ $label }}</h1>
                <img style="width: 120px; height: 120px; margin-left: 75px; margin-top: 30px;" src="data:image/png;base64,{{ DNS2D::getBarcodePNG($label, 'QRCODE') }}" alt="barcode" />
                <p style="text-align: center; font-size: 18px; margin-top: 20px;">{{ $label }}</p>
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
            width: 50%;
            float: left;
        }

        .half_second {
            width: 50%;
            float: right;
        }
    </style>

@endsection
