<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-size: 8px; }
        table { border-collapse: collapse; }

        .title {
            font-size: 12px;
            font-weight: bold;
            text-align: center;
        }

        .small { font-size: 7px; }
        .center { text-align: center; }

        .tbl-main, .tbl-main th, .tbl-main td {
            border: 0.4px solid #000;
        }

        .tbl-main tr th {
            font-size: 8px;
            text-align: center;
            vertical-align: middle;
            font-weight: bold;
        }
    </style>
</head>

<body>

{{-- HEADER --}}
<table width="100%">
    <tr>
        <td class="small" width="40%">
            PT Charoen Pokphand Indonesia<br>
            Food Division
        </td>
        
    </tr>
</table>
<h2 class="title">PENGAMBILAN SAMPEL</h2>
<br>
<br>

{{-- TABEL UTAMA --}}
<table width="100%" class="tbl-main small">
    <tr>
        <th class="center">Tgl Pengambilan</th>
        <th class="center">Jenis Sampel</th>
        <th class="center">Nama Varian</th>
        <th class="center">Kode Batch</th>
        <th class="center">Keterangan</th>
        <th class="center">Paraf SPV</th>
    </tr>

    @foreach($items as $item)
    <tr>
        <td>{{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }}</td>
        <td>{{ $item->jenis_sampel }}</td>
        <td>{{ $item->nama_produk }}</td>
        <td>{{ $item->kode_produksi }}</td>
        <td>{{ $item->keterangan }}</td>
        <td></td>
    </tr>
    @endforeach

    @if(count($items) < 30)
    @for($i = count($items); $i < 30; $i++)
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    @endfor
    @endif
</table>

<div style="text-align:right; font-size:8px;font-style:italic">QT 30 / 00</div>

</body>
</html>
