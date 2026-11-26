<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Washing - Drying</title>
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
    $namaSpv       = (!empty($spv) && $spv !== '-') ? $spv : '';

    $makeQr = function ($nama, $tanggal) {
        if (empty($nama)) {
            return '';
        }
        $teks = 'Ditandatangani secara digital oleh ' . $nama;
        if (!empty($tanggal)) {
            $teks .= ' pada ' . $tanggal;
        }
        $barcode = new \TCPDF2DBarcode($teks, 'QRCODE,H');
        return base64_encode($barcode->getBarcodePngData(4, 4, [0, 0, 0]));
    };

    $qrPemeriksa = $makeQr($namaPemeriksa, $tglPemeriksa ?? '');
    $qrSpv       = $makeQr($namaSpv, $tglSpv ?? '');
@endphp

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0;">
    <tr>
        <td style="padding:0; line-height:1;">
            <span style="font-size:12pt;"><b>PT Charoen Pokphand Indonesia</b></span><br>
            <span style="font-size:12pt;"><b>Food Division</b></span>
        </td>
    </tr>
</table>

<h2 class="title" style="margin:0; padding:0;">
    PEMERIKSAAN WASHING - DRYING
</h2>
<br>
<br>

<table width="100%" class="tbl-main small" cellpadding="0" cellspacing="0">
    @foreach($rows as $label => $value)
        <tr>
            <th width="28%"><b>{{ $label }}</b></th>
            <td width="72%" class="center">{{ $value }}</td>
        </tr>
    @endforeach
</table>

<div style="margin-top:5px; text-align:right;">
    <i>{{ $noDokumen ?? '-' }}</i>
</div>

<br>
<br>

<table width="100%" class="note small" nobr="true">
    <tr>
        <td width="60%">
            <strong>Standar :</strong><br>
            &nbsp;&nbsp;&nbsp;&nbsp;Suhu PC Kleer : 46 ± 3°C<br>
            &nbsp;&nbsp;&nbsp;&nbsp;Suhu Heater : 125 - 135°C<br>
            &nbsp;&nbsp;&nbsp;&nbsp;Konsentrasi PC Kleer : 0.7% (Ayam), 1% (Sapi), 0.8% (Cuci Ulang)<br>
            &nbsp;&nbsp;&nbsp;&nbsp;Konsentrasi Potassium Sorbate : 0.15%<br>
            <strong>Catatan :</strong>
            @if(!empty($catatan))
                {{ $catatan }}
            @else
                -
            @endif
        </td>

        <td width="20%" align="center" style="vertical-align: top;">
            Dibuat oleh :<br>
            @if($namaPemeriksa)
                <br>
                <img src="data:image/png;base64,{{ $qrPemeriksa }}" width="60" height="60"><br>
                <u>{{ $namaPemeriksa }}</u><br>
                QC
            @endif
        </td>

        <td width="20%" align="center" style="vertical-align: top;">
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

</body>
</html>