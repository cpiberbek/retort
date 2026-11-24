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

    $pages = $magnetTraps->chunk(20);
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
    CHECKLIST CLEANING MAGNET TRAP
</h2>

<br>
<br>

<table width="100%" class="tbl-header">
    <tr>
        <td width="100%">
            Tanggal: {{ $dateFilter }}
        </td>
    </tr>
</table>

<br>

@foreach($pages as $pageIndex => $pageItems)

<table width="100%" class="tbl-main small" cellpadding="0" cellspacing="0">
    <tr>
        <th rowspan="3" width="5%">No.</th>
        <th rowspan="3" width="10%">Pukul</th>
        <th colspan="6" width="85%">
            Tanggal: {{ $dateFilter }}
        </th>
    </tr>
    <tr>
        <th rowspan="2" width="15%">Batch Ke-</th>
        <th rowspan="2" width="12%">Jumlah Temuan</th>
        <th rowspan="2" width="28%">Keterangan</th>
        <th colspan="3" width="30%">PARAF</th>
    </tr>
    <tr>
        <th width="10%">QC</th>
        <th width="10%">Prod</th>
        <th width="10%">Eng</th>
    </tr>

    @foreach($pageItems as $index => $item)
    <tr>
        <td width="5%">
            {{ ($pageIndex * 20) + $index + 1 }}
        </td>
        <td width="10%">
            {{ $item->pukul ? \Carbon\Carbon::parse($item->pukul)->format('H:i') : '-' }}
        </td>
        <td width="15%">
            {{ $kodeBatchMap[$item->kode_batch] ?? $item->kode_batch }}
        </td>
        <td width="12%">
            {{ $item->jumlah_temuan ?? '-' }}
        </td>
        <td width="28%">
            {{ \Illuminate\Support\Str::limit($item->keterangan ?? '-', 30) }}
        </td>
        <td width="10%">
            {{ optional($item->creator)->name ?? '-' }}
        </td>
        <td width="10%">
            {{ optional($item->produksi)->nama_karyawan ?? '-' }}
        </td>
        <td width="10%">
            {{ optional($item->engineer)->nama_karyawan ?? '-' }}
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

<table width="100%" class="sign small" nobr="true">
    <tr>
        <td width="60%"></td>

        <td width="20%">
            Dibuat oleh :<br>

            @if($namaPemeriksa)
                <u>{{ $namaPemeriksa }}</u><br>
                <br>
                <img src="data:image/png;base64,{{ $qrPemeriksa }}" width="60" height="60"><br>
                QC
            @endif
        </td>

        <td width="20%">
            Diverifikasi oleh :<br>

            @if($namaSpv)
                <u>{{ $namaSpv }}</u><br>
                <br>
                <img src="data:image/png;base64,{{ $qrSpv }}" width="60" height="60"><br>
                SPV QC
            @elseif(!$semuaDisetujui)
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