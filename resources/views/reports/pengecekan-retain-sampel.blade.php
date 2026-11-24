<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body, table, tr, td, th {
            font-family: helvetica;
            font-size: 7px;
        }

        table {
            border-collapse: collapse;
        }

        .header td {
            vertical-align: middle;
        }

        .company {
            font-size: 10px;
            font-weight: bold;
            text-align: center;
        }

        .title {
            font-size: 12px;
            font-weight: bold;
            text-align: center;
        }

        .tbl-main, .tbl-main th, .tbl-main td {
            border: 0.8px solid #000;
        }

        .tbl-main th {
            background-color: #d9e2f3;
            text-align: center;
            vertical-align: middle;
            font-size: 7px;
            padding: 3px 2px;
        }

        .tbl-main td {
            padding: 3px 2px;
            vertical-align: middle;
        }

        .center {
            text-align: center;
        }

        .sign {
            text-align: center;
            vertical-align: top;
        }

        .small {
            font-size: 7px;
        }
    </style>
</head>

<body>

@foreach($pages as $pageIndex => $retains)

    @if($pageIndex > 0)
        <br pagebreak="true" />
    @endif

   <div style="margin-left:-30px; margin-bottom:0;">
        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0;">
            <tr>
                <td width="55" style="padding:0;">
                    <img src="{{ public_path('assets/img/Logo CPI.png') }}" width="50">
                </td>
                <td style="padding:0; line-height:1;">
                    <span style="font-size:14pt;"><b>PT Charoen</b></span><br>
                    <span style="font-size:14pt;"><b>Pokphand Indonesia</b></span><br>
                    <span style="font-size:14pt;"><b>Food Division</b></span>
                </td>
            </tr>
        </table>
    </div>

    <h2 class="title" style="margin:0; padding:0;">
        PEMERIKSAAN PROSES PACKING
    </h2>
    <br>
    <br>
    <br>

    @php
        $firstRetain = $retains->first();
        $tanggal = $firstRetain
            ? \Carbon\Carbon::parse($firstRetain->tanggal)->format('d-m-Y')
            : '';

        $qrPemeriksa = null;
        $qrSpv = null;

        if ($pemeriksa && $tglPemeriksa) {
            $qrText = 'Ditandatangani secara digital oleh ' . $pemeriksa . ' pada ' . $tglPemeriksa;
            $barcode = new \TCPDF2DBarcode($qrText, 'QRCODE,H');
            $qrPemeriksa = base64_encode($barcode->getBarcodePngData(3, 3));
        }

        if ($semuaDisetujui && $spv && $tglSpv) {
            $qrText = 'Ditandatangani secara digital oleh ' . $spv . ' pada ' . $tglSpv;
            $barcode = new \TCPDF2DBarcode($qrText, 'QRCODE,H');
            $qrSpv = base64_encode($barcode->getBarcodePngData(3, 3));
        }
    @endphp

    <table width="100%">
        <tr>
            <td width="100%">Hari / Tanggal: {{ $tanggal }}</td>
        </tr>
    </table>

    <br>

    <table width="100%" class="tbl-main">
        <tr>
            <th width="6%" rowspan="2">Kode Batch</th>
            <th width="6%" rowspan="2">Varian</th>
            <th width="5%" rowspan="2">Panjang<br>(cm)</th>
            <th width="5%" rowspan="2">Diameter<br>(cm)</th>
            <th width="20%" colspan="4">Sensori</th>
            <th width="25%" colspan="5">Temuan</th>
            <th width="18%" colspan="3">Parameter Lab</th>
            <th width="15%" rowspan="2">Keterangan</th>
        </tr>

        <tr>
            <th width="5%">Rasa</th>
            <th width="5%">Warna</th>
            <th width="5%">Aroma</th>
            <th width="5%">Texture</th>

            <th width="5%">Jamur</th>
            <th width="5%">Lendir</th>
            <th width="5%">Pinehole</th>
            <th width="5%">Kejepit</th>
            <th width="5%">Seal<br>halus / lepas</th>

            <th width="6%">Kadar Garam</th>
            <th width="6%">Kadar Air</th>
            <th width="6%">Mikro</th>
        </tr>

        @foreach($retains as $retain)
            @if($retain->items && $retain->items->count() > 0)
                @foreach($retain->items as $item)
                    <tr>
                        <td width="6%">{{ $item->kode_produksi ?? '' }}</td>
                        <td width="6%">{{ $item->varian ?? '' }}</td>
                        <td width="5%" class="center">{{ $item->panjang ?? '' }}</td>
                        <td width="5%" class="center">{{ $item->diameter ?? '' }}</td>

                        <td width="5%">{{ $item->sensori_rasa ?? '' }}</td>
                        <td width="5%">{{ $item->sensori_warna ?? '' }}</td>
                        <td width="5%">{{ $item->sensori_aroma ?? '' }}</td>
                        <td width="5%">{{ $item->sensori_texture ?? '' }}</td>

                        <td width="5%" class="center">{{ $item->temuan_jamur ? 'V' : '' }}</td>
                        <td width="5%" class="center">{{ $item->temuan_lendir ? 'V' : '' }}</td>
                        <td width="5%" class="center">{{ $item->temuan_pinehole ? 'V' : '' }}</td>
                        <td width="5%" class="center">{{ $item->temuan_kejepit ? 'V' : '' }}</td>
                        <td width="5%" class="center">{{ $item->temuan_seal ? 'V' : '' }}</td>

                        <td width="6%">{{ $item->lab_garam ?? '' }}</td>
                        <td width="6%">{{ $item->lab_air ?? '' }}</td>
                        <td width="6%">{{ $item->lab_mikro ?? '' }}</td>

                        <td width="15%">{{ $retain->keterangan ?? '' }}</td>
                    </tr>
                @endforeach
            @endif
        @endforeach
    </table>
    <div style="margin-top:5px; text-align:right;">
        <i>{{ $noDokumen ?? '-' }}</i>
    </div>

    @if($loop->last)
        <br><br>

        <table width="100%" nobr="true">
            <tr>
                <td width="60%" valign="top"></td>

                <td width="20%" class="sign">
                    Diperiksa oleh:
                    <br><br>

                    @if($pemeriksa)
                        @if($qrPemeriksa)
                            <img src="data:image/png;base64,{{ $qrPemeriksa }}" width="60" height="60">
                            <br>
                        @endif
                        <u>{{ $pemeriksa }}</u>
                        <br>
                        QC
                    @endif
                </td>

                <td width="20%" class="sign">
                    Diverifikasi oleh:
                    <br><br>

                    @if($semuaDisetujui && $spv)
                        @if($qrSpv)
                            <img src="data:image/png;base64,{{ $qrSpv }}" width="60" height="60">
                            <br>
                        @endif
                        <u>{{ $spv }}</u>
                        <br>
                        QC SPV
                    @else
                        <u>Belum Semua Entry Disetujui Oleh SPV</u>
                    @endif
                </td>
            </tr>
        </table>
    @endif

@endforeach

</body>
</html>