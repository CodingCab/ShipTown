@extends('pdf.template')
@section('content')

    @foreach($labels as $label)
        <h1 style="text-align: center; font-size: 85px; margin-top: 10px;">{{ $label }}</h1>
        <img style="width: 180px; height: 180px; margin-top:100px; margin-left: 88px;" src="data:image/png;base64,{{ DNS2D::getBarcodePNG($label, 'QRCODE') }}" alt="barcode" />
        <p style="text-align: center; font-size: 24px; margin-top: 20px;">self:{{ $label }}</p>
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
