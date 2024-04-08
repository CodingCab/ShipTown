@extends('pdf.template')
@section('content')

    @foreach(collect($labels)->chunk(3) as $chunk)
        @foreach($chunk as $index => $label)
            <div class="label_box">
                <div style="width: 29%; height:100%; display: inline-block">
                    <img style="width: 100px; height: 100px; margin-left: 2px; margin-top: 40px;" src="data:image/png;base64,{{ DNS2D::getBarcodePNG($label, 'QRCODE') }}" alt="barcode" />
                    <p style="text-align: center; font-size: 16px; margin-top: 5px;">self:{{ $label }}</p>
                </div>
                <div style="width: 66%; float: right; display: inline-block">
                    <h1 style="text-align: center; font-size: 60px; margin-top: 50px;">{{ $label }}</h1>
                </div>
            </div>
        @endforeach
        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach

    <style>
        @page {
            size:101.6mm 152.4mm;
            margin: 3mm;
        }

        .page-break {
            page-break-after: always;
        }

        .label_box {
            height: 33.3%;
        }

    </style>

@endsection
