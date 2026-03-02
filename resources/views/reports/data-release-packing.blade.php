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
<h2 class="title">DATA RELEASE PACKING</h2>
<br>
<br>


@php
    $dateFilter = request('date') ? \Carbon\Carbon::parse(request('date'))->format('d-m-Y') : 'All Dates';
    $jenisKemasanFilter = request('jenis_kemasan') ?? 'All Packaging Types';
@endphp

<table width="100%" class="tbl-header">
    <tr>
        <td>Hari/Tgl : {{ $dateFilter }}</td>
        <td>Jenis Kemasan : {{ $jenisKemasanFilter }}</td>
    </tr>
</table>
<br>

{{-- TABLE --}}
<table width="100%" class="tbl-main small">
    <tr>
        <th class="center">No.</th>
        <th class="center">Nama Varian</th>
        <th class="center">Kode Batch</th>
        <th class="center">Best Before</th>
        <th class="center">No. Palet</th>
        <th class="center">Jumlah Release</th>
        <th class="center">Paraf QC</th>
    </tr>

    @forelse($release_packings as $index => $packing)
    <tr>
        <td class="center">{{ $index + 1 }}</td>
        <td class="center">{{ $packing->nama_produk ?? '-' }}</td>
        <td class="center">{{ $packing->kode_produksi ?? '-' }}</td>
        <td class="center">{{ $packing->expired_date ? \Carbon\Carbon::parse($packing->expired_date)->format('d-m-Y') : '-' }}</td>
        <td class="center">{{ $packing->no_palet ?? '-' }}</td>
        <td class="center">{{ $packing->release ?? '-' }}</td>
        <td class="center">{{ $packing->username ?? '-' }}</td>
    </tr>
    @empty
    <tr>
        <td colspan="9" class="center">Tidak ada data release packing</td>
    </tr>
    @endforelse

</table>
<br>
<table width="100%" class="small">
    <tr>
        <td width="50%">
           Catatan :
        </td>
        <td  width="50%">
            {{-- SIGN --}}
            <table width="100%">
                <tr>
                    <td width="70%"></td>
                    <td width="30%" class="sign">
                        Diverifikasi oleh,<br><br><br><br>
                        (___________________)<br>
                        QC SPV
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>



</body>
</html>
