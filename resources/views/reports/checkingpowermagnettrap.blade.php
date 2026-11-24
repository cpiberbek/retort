<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
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
            padding: 2px 0;
            font-size: 8px;
            line-height: 1.15;
        }

        .tbl-main,
        .tbl-main th,
        .tbl-main td {
            border: 0.8px solid #000;
            padding: 3px 2px;
            text-align: center;
            vertical-align: middle;
        }

        .tbl-main th {
            background-color: #d9e2f3;
            font-weight: bold;
            line-height: 1.15;
        }

        .note td {
            padding: 3px 2px;
            vertical-align: top;
            line-height: 1.4;
        }

        .sign td {
            text-align: center;
            vertical-align: top;
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

    $pages = $magnetTraps->count() > 0
        ? $magnetTraps->chunk(20)
        : collect([collect()]);
@endphp

<div style="margin-left:-30px; margin-bottom:0;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0">
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
    VERIFIKASI MAGNET TRAP
</h2>

<br>
<br>

<table width="100%" class="tbl-header">
    <tr>
        <td width="100%">
            Periode: {{ $periode }}
        </td>
    </tr>
</table>

<br>

@foreach($pages as $pageIndex => $pageItems)

<table width="100%" class="tbl-main small" cellpadding="0" cellspacing="0">
    <tr>
        <th rowspan="3" width="5%">NO</th>
        <th colspan="10" width="95%" style="text-align:left;">
            Periode: {{ $periode }}
        </th>
    </tr>

    <tr>
        <th rowspan="2" width="10%">TANGGAL</th>
        <th colspan="3" width="30%">
            KEKUATAN MEDAN MAGNET (GAUSS)<br>
            MAGNET KE-
        </th>
        <th colspan="2" width="15%">
            * PARAMETER SETTINGAN
        </th>
        <th rowspan="2" width="10%">
            * KONDISI<br>
            MAGNET<br>
            TRAP
        </th>
        <th rowspan="2" width="15%">
            * KETERANGAN
        </th>
        <th colspan="2" width="15%">
            PETUGAS
        </th>
    </tr>

    <tr>
        <th width="10%">1</th>
        <th width="10%">2</th>
        <th width="10%">3</th>
        <th width="7.5%">SESUAI</th>
        <th width="7.5%">TIDAK SESUAI</th>
        <th width="7.5%">QC</th>
        <th width="7.5%">ENG</th>
    </tr>

    @foreach($pageItems as $index => $item)
    <tr>
        <td width="5%">
            {{ ($pageIndex * 20) + $index + 1 }}
        </td>

        <td width="10%">
            {{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') : '-' }}
        </td>

        <td width="10%">
            {{ $item->kekuatan_median_1 ?? '-' }}
        </td>

        <td width="10%">
            {{ $item->kekuatan_median_2 ?? '-' }}
        </td>

        <td width="10%">
            {{ $item->kekuatan_median_3 ?? '-' }}
        </td>

        <td width="7.5%">
            {{ $item->parameter_sesuai == 1 ? 'V' : '-' }}
        </td>

        <td width="7.5%">
            {{ $item->parameter_sesuai == 0 ? 'X' : '-' }}
        </td>

        <td width="10%">
            {{ $item->kondisi_magnet_trap ?? '-' }}
        </td>

        <td width="15%">
            {{ \Illuminate\Support\Str::limit($item->keterangan ?? '-', 30) }}
        </td>

        <td width="7.5%">
            {{ $item->petugas_qc ?? '-' }}
        </td>

        <td width="7.5%">
            {{ $item->petugas_eng ?? '-' }}
        </td>
    </tr>
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
        <td width="60%">
            <b>* Keterangan Pengisian data:</b><br><br>

            <u>Kolom Parameter Settingan:</u><br>
            Beri tanda V untuk kondisi baik<br>
            Beri tanda X untuk kondisi tidak sesuai<br><br>

            <u>Kolom Kondisi Magnet Trap:</u><br>
            Diisi dengan keadaan visual magnet tersebut (contoh: magnet/slongsong gembel, penyok atau dalam keadaan baik, sempurna, dll)<br><br>

            <u>Kolom Keterangan Mengacu pada Standar Kekuatan Medan Magnet:</u><br>
            &gt;= 8000 Gauss maka magnet masih bisa digunakan<br>
            &lt; 8000 Gauss maka magnet harus dilakukan penggantian
        </td>

        <td width="20%" align="center">
            Dibuat oleh :<br>

            @if($namaPemeriksa)
                <u>{{ $namaPemeriksa }}</u><br>
                <br>
                <img src="data:image/png;base64,{{ $qrPemeriksa }}" width="60" height="60"><br>
                QC
            @endif
        </td>

        <td width="20%" align="center">
            Diverifikasi oleh :<br>

            @if($namaSpv)
                <u>{{ $namaSpv }}</u><br>
                <br>
                <img src="data:image/png;base64,{{ $qrSpv }}" width="60" height="60"><br>
                SPV QC
            @elseif($magnetTraps->count() > 0 && !$semuaDisetujui)
                <i>Belum semua entry disetujui</i>
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