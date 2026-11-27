<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pemeriksaan Suhu dan RH</title>
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
            padding: 1.5px 2px;
            text-align: center;
            vertical-align: middle;
            border: 0.8px solid #000;
        }

        .tbl-main th {
            background: #d9e2f3;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
            padding: 1.5px 2px;
            line-height: 1.15;
        }

        .tbl-main td {
            padding: 1.5px 2px;
            vertical-align: middle;
        }

        .note td {
            vertical-align: top;
            line-height: 1.3;
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
    PEMERIKSAAN SUHU DAN RH
</h2>
<br>

<table width="100%" class="tbl-header" cellpadding="0" cellspacing="0">
    <tr>
        <td width="50%">Hari / Tanggal : {{ $tanggal }}</td>
        <td width="50%">Shift : {{ $shiftText }}</td>
    </tr>
</table>

<br>

<table width="100%" class="tbl-main small" cellpadding="0" cellspacing="0">
    <tr>
        <th rowspan="3" width="4.5%"><b>Pukul</b></th>
        <th colspan="18" width="76.5%"><b>Ruangan (°C)</b></th>
        <th rowspan="4" width="8%"><b>Keterangan</b></th>
        <th colspan="2" width="11%"><b>PARAF</b></th>
    </tr>
    <tr>
        <th colspan="2" width="8.5%"><b>Chill Room</b></th>
        <th colspan="2" width="8.5%"><b>Cold Storage Meat</b></th>
        <th rowspan="2" width="4.25%"><b>Seasoning</b></th>
        <th rowspan="2" width="4.25%"><b>Meat Preparation</b></th>
        <th rowspan="2" width="4.25%"><b>Hopper</b></th>
        <th rowspan="2" width="4.25%"><b>Stuffer</b></th>
        <th rowspan="2" width="4.25%"><b>Susun</b></th>
        <th rowspan="2" width="4.25%"><b>Retort Chamber</b></th>
        <th rowspan="2" width="4.25%"><b>PVDC</b></th>
        <th colspan="2" width="8.5%"><b>Drying</b></th>
        <th colspan="2" width="8.5%"><b>Packing</b></th>
        <th rowspan="2" width="4.25%"><b>Dry Store</b></th>
        <th colspan="2" width="8.5%"><b>FG</b></th>
        <th rowspan="2" width="5.5%"><b>QC</b></th>
        <th rowspan="2" width="5.5%"><b>PROD.</b></th>
    </tr>
    <tr>
        <th width="4.25%"><b>Ruang</b></th>
        <th width="4.25%"><b>Meat</b></th>
        <th width="4.25%"><b>Ruang</b></th>
        <th width="4.25%"><b>Meat</b></th>
        <th width="4.25%"><b>Suhu</b></th>
        <th width="4.25%"><b>RH</b></th>
        <th width="4.25%"><b>Suhu</b></th>
        <th width="4.25%"><b>RH</b></th>
        <th width="4.25%"><b>Suhu</b></th>
        <th width="4.25%"><b>RH</b></th>
    </tr>
    <tr>
        <th width="4.5%"><b>STD (°C)</b></th>
        @foreach($std as $s)
            <th width="4.25%"><b>{{ $s }}</b></th>
        @endforeach
        <th width="5.5%"></th>
        <th width="5.5%"></th>
    </tr>

    @foreach($rows as $row)
        <tr>
            <td width="4.5%">{{ $row['pukul'] }}</td>
            @foreach($row['values'] as $value)
                <td width="4.25%">{{ $value }}</td>
            @endforeach
            <td width="8%">{{ $row['keterangan'] }}</td>
            <td width="5.5%">{{ $row['qc'] }}</td>
            <td width="5.5%">{{ $row['produksi'] }}</td>
        </tr>
    @endforeach
</table>

<div style="margin-top:3px; text-align:right;">
    <i>{{ $noDokumen ?? '-' }}</i>
</div>

<br>

<table width="100%" class="note small" nobr="true">
    <tr>
        <td width="60%">
            <strong>Catatan :</strong>
        </td>

        <td width="20%" align="center" style="vertical-align: top;">
            Dibuat oleh :<br>
            @if($namaPemeriksa)
                <img src="data:image/png;base64,{{ $qrPemeriksa }}" width="50" height="50"><br>
                <u>{{ $namaPemeriksa }}</u><br>
                QC
            @endif
        </td>

        <td width="20%" align="center" style="vertical-align: top;">
            Diverifikasi oleh :<br>
            @if($namaSpv)
                <img src="data:image/png;base64,{{ $qrSpv }}" width="50" height="50"><br>
                <u>{{ $namaSpv }}</u><br>
                SPV QC
            @elseif(!$semuaDisetujui && $tanggal !== '')
                <i>Belum semua entry disetujui</i>
            @endif
        </td>
    </tr>
</table>

</body>
</html>