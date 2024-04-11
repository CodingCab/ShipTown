@extends('pdf.template')
@section('content')

    @foreach(collect($labels)->chunk(3) as $chunk)
        @foreach($chunk as $index => $label)
            <div class="label_box">
                <div style="width: 32%; height: 99%; display: inline-block">
                    <img style="width: 100px; height: 100px; margin-left: 15px; margin-top: 40px;" src="data:image/png;base64,{{ DNS2D::getBarcodePNG('shelf:'.$label, 'QRCODE') }}" alt="barcode" />
                    <p style="text-align: center; word-wrap: anywhere; font-size: 13px; margin-left: 15px; margin-top: 5px;">shelf:{{ $label }}</p>
                </div>
                <div style="width: 65%; height: 99%; float: right;">
                    <table style="border: black; width: 100%" >
                        <tbody>
                        <tr>
                            <td style="height: 100%; vertical-align:middle; text-align: center; margin: 0; padding: 0">
                                <h1 style="font-size: 50px; word-wrap: anywhere; line-height: 90%">{{ $label }}</h1>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach

    <style>
        h1, p, img, table, tbody, tr, td {
            margin: 0;
            padding: 0;
        }

        @page {
            size:101.6mm 152.4mm;
            margin: 3mm;
        }

        .page-break {
            page-break-after: always;
        }

        .label_box {
            height: 33%;
            clear: bottom;
        }

    </style>

@endsection
