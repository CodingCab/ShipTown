@extends('pdf.template')
@section('content')

    @foreach($labels as $label)
        <h1 style="text-align: center; font-size: 90px; margin-top: 0;">{{ $label }}</h1>
        <br>
        <img style="width: 180px; height: 180px; margin-left: 85px;" src="data:image/png;base64,{{ DNS2D::getBarcodePNG($label, 'QRCODE') }}" alt="barcode" />
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
    </style>

@endsection
