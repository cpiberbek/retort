
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Stuffing</title>

    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 7px;
            color: #000;
        }

        table {
            border-collapse: collapse;
        }

        .title {
            font-size: 13px;
            font-weight: bold;
            text-align: center;
        }

        .small {
            font-size: 7px;
        }

        .center {
            text-align: center;
            vertical-align: middle;
        }

        .tbl-header td {
            padding: 0;
            font-size: 8px;
            line-height: 1;
        }

        .tbl-main,
        .tbl-main th,
        .tbl-main td {
            padding: 3px 2px;
            text-align: center;
            vertical-align: middle;
            border: 0.8px solid #000;
        }

        .tbl-main th {
            background: #d9e2f3;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
            padding: 3px 2px;
            line-height: 1.15;
        }

        .tbl-main td {
            padding: 3px 2px;
            vertical-align: middle;
        }

        .note td {
            vertical-align: top;
            line-height: 1.4;
        }
    </style>
</head>

<body>

@php
    $namaPemeriksa = (!empty($pemeriksa) && $pemeriksa !== '-') ? $pemeriksa : '';
    $namaSpv = (!empty($spv) && $spv !== '-') ? $spv : '';

    $makeQr = function ($nama, $tanggal) {
        if (empty($nama)) {
            return '';
        }

        $teks = 'Ditandatangani secara digital oleh ' . $nama;

        if (!empty($tanggal)) {
            $teks .= ' pada ' . $tanggal;
        }

        $barcode = new \TCPDF2DBarcode($teks, 'QRCODE,H');

        return base64_encode(
            $barcode->getBarcodePngData(4, 4, [0, 0, 0])
        );
    };

    $qrPemeriksa = $makeQr($namaPemeriksa, $tglPemeriksa ?? '');
    $qrSpv = $makeQr($namaSpv, $tglSpv ?? '');
@endphp

@foreach($pages as $page)

    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0;">
        <tr>
            <td style="padding:0; line-height:1;">
                <span style="font-size:12pt;"><b>PT Charoen Pokphand Indonesia</b></span><br>
                <span style="font-size:12pt;"><b>Food Division</b></span>
            </td>
        </tr>
    </table>

    <h2 class="title" style="margin:0; padding:0;">
        PEMERIKSAAN STUFFING SOSIS RETORT
    </h2>

    <br>
    <br>

    <table width="100%" class="tbl-header" cellpadding="0" cellspacing="0">
        <tr>
            <td width="20%">Hari / Tanggal : {{ $tanggal }}</td>
            <td width="15%">Shift : {{ $shift }}</td>
            <td width="45%">Nama Produk : {{ $namaProduk }}</td>
            <td width="20%" align="right">Exp Date : {{ $expDate }}</td>
        </tr>
    </table>

    <br>

    <table width="100%" class="tbl-main small" cellpadding="0" cellspacing="0">
        <tr>
            <th width="10%">Kode Batch</th>
            <td width="90%" colspan="9" class="center">
                {{ $batchCodes }}
            </td>
        </tr>
    </table>

    <br>

    <table width="100%" class="tbl-main small" cellpadding="0" cellspacing="0">

        @foreach($rows as $row)

            @if($row['type'] === 'group')

                <tr>
                    <th colspan="10" style="text-align:left; padding-left:4px; background-color:#fff; font-weight:bold;">
                        {{ $row['label'] }}
                    </th>
                </tr>

            @else

                <tr>
                    <th width="10%">
                        <b>{{ $row['label'] }}</b>
                    </th>

                    @for($i = 0; $i < 9; $i++)
                        <td width="10%" class="center">
                            {{ $page[$i][$row['key']] ?? '' }}
                        </td>
                    @endfor
                </tr>

            @endif

        @endforeach

    </table>

    <div style="margin-top:5px; text-align:right;">
        <i>{{ $noDokumen ?? '-' }}</i>
    </div>

    @if($loop->last)

        <br>
        <br>

        <table width="100%" class="note small" nobr="true">
            <tr>
                <td width="60%">&nbsp;</td>

                <td width="20%" align="center" style="vertical-align:top;">
                    Dibuat oleh :<br>

                    @if($namaPemeriksa)
                        <br>
                        <img src="data:image/png;base64,{{ $qrPemeriksa }}" width="60" height="60"><br>
                        <u>{{ $namaPemeriksa }}</u><br>
                        QC
                    @endif
                </td>

                <td width="20%" align="center" style="vertical-align:top;">
                    Diverifikasi oleh :<br>

                    @if($namaSpv)
                        <br>
                        <img src="data:image/png;base64,{{ $qrSpv }}" width="60" height="60"><br>
                        <u>{{ $namaSpv }}</u><br>
                        SPV QC
                    @endif
                </td>
            </tr>
        </table>

    @else

        <br pagebreak="true" />

    @endif

@endforeach

</body>
</html>
