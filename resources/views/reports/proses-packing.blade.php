<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-size: 8px; }
        .title { font-size: 12px; font-weight: bold; text-align: center; }
        table { border-collapse: collapse; }

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
<table width="100%">
    <tr>
        <td class="small" width="40%">
            PT Charoen Pokphand Indonesia<br>
            Food Division
        </td>
        
    </tr>
</table>
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
        <th rowspan="2" class="center">Waktu</th>
        <th rowspan="2" class="center">Kalibrasi</th>
        <th rowspan="2" class="center">QR Code</th>

        <th colspan="3" class="center">Kode Batch</th>

        <th colspan="2" class="center">Shrink Tunnel</th>

        <th rowspan="2" class="center">Kondisi Segel Toples / Seal Pouch</th>

        <th colspan="2" class="center">Berat Varian Per</th>

        <th colspan="3" class="center">Data Kemasan</th>

        <th colspan="2" class="center">Paraf</th>

        <th rowspan="2" class="center">Keterangan</th>
    </tr>

    <tr>
        <th class="center">Printing</th>
        <th class="center">Toples</th>
        <th class="center">Karton</th>

        <th class="center">Suhu</th>
        <th class="center">Speed</th>

        <th class="center">Toples</th>
        <th class="center">Pouch</th>

        <th class="center">No. Lot Kemasan</th>
        <th class="center">Tgl Kedatangan</th>
        <th class="center">Nama Supplier</th>

        <th class="center">QC</th>
        <th class="center">Produksi</th>
    </tr>

    @forelse($packings as $packing)
    <tr>
        <td class="center">{{ \Carbon\Carbon::parse($packing->waktu)->format('H:i') }}</td>
        <td class="center">{{ $packing->kalibrasi ?? '-' }}</td>
        <td class="center">{{ $packing->qrcode ?? '-' }}</td>
        <td class="center">{{ $packing->kode_printing ? 'Ada Gambar' : '-' }}</td>
        <td class="center">{{ $packing->kode_toples ?? '-' }}</td>
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

<div style="text-align:right; font-size:8px;font-style:italic;">QT 06 / 00</div>
<br>

<table width="100%" class="small">
    <tr>
        <td width="50%">
            Ket :<br>
            OK : √ <br>
            Tidak OK : X
        </td>
        <td>
            <table width="50%" class="sign small">
                <tr><td>Disetujui oleh</td></tr>
                <tr><td><br><br></td></tr>
                <tr><td>(___________________)</td></tr>
                <tr><td>QC SPV</td></tr>
            </table>
        </td>
    </tr>
</table>



</body>
</html>
