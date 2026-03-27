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
<h2 class="title">DATA SAMPLING VARIAN</h2>
<br>
<br>

@php
    $dateFilter = request('date') ? \Carbon\Carbon::parse(request('date'))->format('d-m-Y') : 'All Dates';
    $shiftFilter = request('shift') ?? 'All Shifts';
@endphp


<table width="100%" class="tbl-header">
    <tr>
        <td>Hari / Tanggal : {{ $dateFilter }}</td>
        <td>Shift : {{ $shiftFilter }}</td>
    </tr>
</table>
<br>

{{-- TABLE --}}
<table width="100%" class="tbl-main small">
    <tr>
        <th rowspan="2" class="center">No.</th>
        <th rowspan="2" class="center">Jenis Sampling</th>
        <th rowspan="2" class="center">Nama Varian</th>
        <th rowspan="2" class="center">Kode Batch</th>
        <th rowspan="2" class="center">Jumlah</th>
        <th colspan="16" class="center">Item Sortir</th>
        <th rowspan="2" class="center">Paraf QC</th>
    </tr>
    <tr>
        <th class="center">Jamur</th>
        <th class="center">Lendir</th>
        <th class="center">Klip Tajam</th>
        <th class="center">Pin hole</th>
        <th class="center">Air Trap PVDC</th>
        <th class="center">Air Trap Varian</th>
        <th class="center">Keriput</th>
        <th class="center">Bengkok</th>
        <th class="center">Non Kode</th>
        <th class="center">Over lap</th>
        <th class="center">Kecil</th>
        <th class="center">Terjepit</th>
        <th class="center">Double klip</th>
        <th class="center">Seal Halus / Lepas</th>
        <th class="center">Basah</th>
        <th class="center">Dll</th>
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
<div style="text-align:right; font-size:8px;font-style:italic">QT 05 / 00</div>
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
                        Disetujui oleh,<br><br><br><br>
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
