<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-size: 8px; }
        .title { font-size: 12px; font-weight: bold; text-align: center; }
        table { border-collapse: collapse; }

        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 8px;
        }

        .tbl-header td {
            padding: 2px;
            font-size: 8px;
        }

        .tbl-main, .tbl-main th, .tbl-main td {
            border: 0.3px solid #000;
        }

        .tbl-main th {
            font-size: 7px;
            text-align: center;
            vertical-align: middle;
        }

        .center { text-align: center; }
        .small { font-size: 7px; }
        .sign { text-align: center; }
    </style>
</head>

<body>

{{-- HEADER LOGO + TITLE --}}
<div style="margin:0; padding:0; line-height:1;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0; padding:0;">
        <tr>
            <td width="55" style="padding:0;">
                <img src="{{ public_path('assets/img/Logo CPI.png') }}" width="50">
            </td>
            <td style="padding:0;">
                <span style="font-size:14pt;"><b>PT Charoen</b></span><br>
                <span style="font-size:14pt;"><b>Pokphand Indonesia</b></span><br>
                <span style="font-size:14pt;"><b>Food Division</b></span>
            </td>
        </tr>
    </table>
</div>

<h2 class="title">PEMERIKSAAN PROSES PACKING</h2>
<br>
<br>

@php
    $dateFilter = request('date') ? \Carbon\Carbon::parse(request('date'))->format('d-m-Y') : 'All Dates';
    $shiftFilter = request('shift') ?? 'All Shifts';
    $namaProdukFilter = request('nama_produk') ?? 'All Products';
@endphp

<table width="100%" class="tbl-header">
    <tr>
        <td>Hari / Tgl : {{ $dateFilter }}</td>
        <td>Shift : {{ $shiftFilter }}</td>
        <td>Nama Varian : {{ $namaProdukFilter }}</td>
    </tr>
</table>
<br>


{{-- TABEL --}}
<table width="100%" class="tbl-main small">
    <tr>
        <th rowspan="2" class="center"><b>Waktu</b></th>
        <th rowspan="2" class="center"><b>Kalibrasi</b></th>
        <th rowspan="2" class="center"><b>QR Code</b></th>

        <th colspan="3" class="center"><b>Kode Batch</b></th>

        <th colspan="2" class="center"><b>Shrink Tunnel</b></th>

        <th rowspan="2" class="center"><b>Kondisi Segel Toples / Seal Pouch</b></th>

        <th colspan="2" class="center"><b>Berat Varian Per</b></th>

        <th colspan="3" class="center"><b>Data Kemasan</b></th>

        <th colspan="2" class="center"><b>Paraf</b></th>

        <th rowspan="2" class="center"><b>Keterangan</b></th>
    </tr>

    <tr>
        <th class="center"><b>Printing</b></th>
        <th class="center"><b>Toples</b></th>
        <th class="center"><b>Karton</b></th>

        <th class="center"><b>Suhu</b></th>
        <th class="center"><b>Speed</b></th>

        <th class="center"><b>Toples</b></th>
        <th class="center"><b>Pouch</b></th>

        <th class="center"><b>No. Lot Kemasan</b></th>
        <th class="center"><b>Tgl Kedatangan</b></th>
        <th class="center"><b>Nama Supplier</b></th>

        <th class="center"><b>QC</b></th>
        <th class="center"><b>Produksi</b></th>
    </tr>

    @forelse($packings as $packing)
    <tr>
        <td class="center">{{ \Carbon\Carbon::parse($packing->waktu)->format('H:i') }}</td>
        <td class="center">{{ $packing->kalibrasi ?? '-' }}</td>
        <td class="center">
            @if($packing->qrcode)
                <img src="{{ public_path($packing->qrcode) }}" width="60">
            @else
                -
            @endif
        </td>
        <td class="center">{{ $packing->kode_printing ? 'Ada Gambar' : '-' }}</td>
        <td class="center">
            {{ \App\Models\Mincing::where('uuid', $packing->kode_toples)->value('kode_produksi') ?? $packing->kode_toples ?? '-' }}
        </td>
        <td class="center">{{ $packing->kode_karton ?? '-' }}</td>
        <td class="center">{{ $packing->suhu ?? '-' }}</td>
        <td class="center">{{ $packing->speed ?? '-' }}</td>
        <td class="center">{{ $packing->kondisi_segel ?? '-' }}</td>
        <td class="center">{{ $packing->berat_toples ?? '-' }}</td>
        <td class="center">{{ $packing->berat_pouch ?? '-' }}</td>
        <td class="center">{{ $packing->no_lot ?? '-' }}</td>
        <td class="center">{{ $packing->tgl_kedatangan ? \Carbon\Carbon::parse($packing->tgl_kedatangan)->format('d-m-Y') : '-' }}</td>
        <td class="center">{{ $packing->nama_supplier ?? '-' }}</td>
        <td class="center">{{ $packing->username ?? '-' }}</td>
        <td class="center">{{ $packing->nama_produksi ?? '-' }}</td>
        <td class="center">{{ $packing->keterangan ?? '-' }}</td>
    </tr>
    @empty
    <tr>
        <td colspan="17" class="center">Tidak ada data packing</td>
    </tr>
    @endforelse

</table>

<div style="margin-top:5px; text-align:right; font-style:italic;">
    {{ $noDokumen ?? '-' }}
</div>
<br>

<table width="100%" class="small">
    <tr>
        <td width="50%">
            Ket :<br>
            OK : √ <br>
            Tidak OK : X
        </td>
        <table width="100%">
    <tr>
            <td width="70%"></td>
            <td width="30%" align="center">
                Disetujui Oleh
                <br><br><br><br>
                ({{  $packing->nama_spv ?: '-' }})<br>
                QC SPV
            </td>
        </tr>
    </table>
    </tr>
</table>



</body>
</html>
