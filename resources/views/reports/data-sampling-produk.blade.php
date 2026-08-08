<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-size: 9px; }
        .title { font-size: 12px; font-weight: bold; text-align: center; }
        table { border-collapse: collapse; }

        .tbl-header td {
            font-size: 9px;
            padding: 2px 0;
        }

        .tbl-main, 
        .tbl-main th, 
        .tbl-main td {
            border: 0.3px solid #000;
        }

        .tbl-main th {
            text-align: center;
            vertical-align: middle;
            font-size: 8px;
        }

        .center { text-align: center; }
        .sign { text-align: center; }
        .small { font-size: 8px; }

        /* tnr */
        body,
        table,
        tr,
        td,
        th {
            font-family: times;
            font-size: 9pt;
        }
    </style>
</head>

<body>

{{-- HEADER LOGO + TITLE --}}
<div style="margin-left:-30px;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td width="55">
                <img src="{{ public_path('assets/img/Logo CPI.png') }}" width="50">
            </td>
            <td>
                <span style="font-size:12pt;"><b>PT Charoen </b></span><br>
                <span style="font-size:12pt;"><b>Pokphand Indonesia</b></span><br>
                <span style="font-size:12pt;"><b>Food Division</b></span>
            </td>
        </tr>
    </table>
</div>

<h1 class="title" style="font-size:12pt;">DATA SAMPLING PRODUK</h1>
<br>
<br>

@php
    $dateFilter = request('date') ? \Carbon\Carbon::parse(request('date'))->format('d-m-Y') : 'All Dates';
    $shiftFilter = request('shift') ?? 'Semua Shift';
@endphp

<table width="100%" class="tbl-header">
    <tr>
        <td style="text-align: center;">Hari / Tanggal : <u>{{ $dateFilter }}</u></td>
        <td style="text-align: center;">Shift : <u>{{ $shiftFilter }}</u></td>
    </tr>
</table>
<br><br>

{{-- TABLE --}}
<table width="100%" class="tbl-main small">
    <tr>
    <th rowspan="2" class="center"><b>No.</b></th>
    <th rowspan="2" class="center"><b>Jenis Sampling</b></th>
    <th rowspan="2" class="center"><b>Nama Produk</b></th>
    <th rowspan="2" class="center"><b>Kode Batch</b></th>
    <th rowspan="2" class="center"><b>Jumlah</b></th>
    <th colspan="16" class="center"><b>Item Sortir</b></th>
    <th rowspan="2" class="center"><b>Paraf QC</b></th>
    </tr>
    <tr>
        <th class="center"><b>Jamur</b></th>
        <th class="center"><b>Lendir</b></th>
        <th class="center"><b>Klip Tajam</b></th>
        <th class="center"><b>Pin hole</b></th>
        <th class="center"><b>Air Trap PVDC</b></th>
        <th class="center"><b>Air Trap Produk</b></th>
        <th class="center"><b>Keriput</b></th>
        <th class="center"><b>Bengkok</b></th>
        <th class="center"><b>Non Kode</b></th>
        <th class="center"><b>Over lap</b></th>
        <th class="center"><b>Kecil</b></th>
        <th class="center"><b>Terjepit</b></th>
        <th class="center"><b>Double klip</b></th>
        <th class="center"><b>Seal Halus / Lepas</b></th>
        <th class="center"><b>Basah</b></th>
        <th class="center"><b>Dll</b></th>
    </tr>

    @forelse($samplings as $index => $sampling)
    <tr>
        <td class="center">{{ $index + 1 }}</td>
        <td class="center">{{ $sampling->jenis_sampel ?? '-' }}</td>
        <td class="center">{{ $sampling->nama_produk ?? '-' }}</td>
        <td class="center">{{ $sampling->kode_produksi ?? '-' }}</td>
        <td class="center">{{ $sampling->jumlah ? ($sampling->jumlah . ' ' . ($sampling->jenis_kemasan ?? '')) : '-' }}</td>
        <td class="center">{{ $sampling->jamur ?? '-' }}</td>
        <td class="center">{{ $sampling->lendir ?? '-' }}</td>
        <td class="center">{{ $sampling->klip_tajam ?? '-' }}</td>
        <td class="center">{{ $sampling->pin_hole ?? '-' }}</td>
        <td class="center">{{ $sampling->air_trap_pvdc ?? '-' }}</td>
        <td class="center">{{ $sampling->air_trap_produk ?? '-' }}</td>
        <td class="center">{{ $sampling->keriput ?? '-' }}</td>
        <td class="center">{{ $sampling->bengkok ?? '-' }}</td>
        <td class="center">{{ $sampling->non_kode ?? '-' }}</td>
        <td class="center">{{ $sampling->over_lap ?? '-' }}</td>
        <td class="center">{{ $sampling->kecil ?? '-' }}</td>
        <td class="center">{{ $sampling->terjepit ?? '-' }}</td>
        <td class="center">{{ $sampling->double_klip ?? '-' }}</td>
        <td class="center">{{ $sampling->seal_halus ?? '-' }}</td>
        <td class="center">{{ $sampling->basah ?? '-' }}</td>
        <td class="center">{{ $sampling->dll ?? '-' }}</td>
        <td class="center">{{ $sampling->username ?? '-' }}</td>
    </tr>
    @empty
    <tr>
        <td colspan="22" class="center">Tidak ada data sampling</td>
    </tr>
    @endforelse


</table>
<div style="text-align:right; font-size:8px; font-style:italic;">
    {{ $noDokumen }}
</div>
<br>

@php
    $catatanList = $samplings->filter(function ($item) {
        return !empty($item->catatan);
    });

    $allApproved = $samplings->every(function ($item) {
        return !empty($item->nama_spv);
    });

    $namaSpv = $allApproved
        ? $samplings->pluck('nama_spv')->filter()->unique()->implode(', ')
        : 'Belum Semua Entry Disetujui Oleh SPV';
@endphp

<table width="100%">
    <tr>
        <td width="70%" valign="top">
            @if($catatanList->count())
                <b>Catatan:</b><br>

                @foreach($catatanList as $item)
                    {{ $item->kode_produksi ?? '-' }} : {{ $item->catatan }}<br>
                @endforeach
            @endif
        </td>

        <td width="30%" align="center" valign="top">
            Disetujui Oleh,
            <br><br><br><br>
            (<u>{{ $namaSpv }}</u>)<br>
            QC SPV
        </td>
    </tr>
</table>


</body>
</html>
