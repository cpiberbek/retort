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
        body,
        table,
        tr,
        td,
        th {
            font-family: times;
        }
    </style>
</head>

<body>

{{-- HEADER --}}
<div style="margin-left:-30px;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td width="55">
                <img src="{{ public_path('assets/img/Logo CPI.png') }}" width="50">
            </td>
            <td>
                <span style="font-size:14pt;"><b>PT Charoen </b></span><br>
                <span style="font-size:14pt;"><b>Pokphand Indonesia</b></span><br>
                <span style="font-size:14pt;"><b>Food Division</b></span>
            </td>
        </tr>
    </table>
</div>

<h2 class="title">PENGAMBILAN SAMPEL</h2>
<br>
<br>

{{-- TABEL UTAMA --}}
<table width="100%" class="tbl-main small">
    <tr>
        <th class="center">Tgl. Pengambilan</th>
        <th class="center">Jenis Sampel</th>
        <th class="center">Nama Produk</th>
        <th class="center">Kode Produk</th>
        <th class="center">Keterangan</th>
        <th class="center">Paraf SPV</th>
    </tr>

    @foreach($items as $item)
    <tr>
        <td>{{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }}</td>
        <td>{{ $item->jenis_sampel }}</td>
        <td>{{ $item->nama_produk }}</td>
        <td>{{ optional(\App\Models\Mincing::where('uuid', $item->kode_produksi)->first())->kode_produksi }}</td>
        <td>{{ $item->keterangan }}</td>
        <td>
            {{ $item->nama_spv ? 'Disetujui oleh '.$item->nama_spv : 'Belum disetujui' }}
        </td>
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

<table width="100%">
    <tr>
        <td width="75%"></td>
        <td width="25%" align="right" style="font-style: italic;">
            {{ $noDokumen }}
        </td>
    </tr>
</table>

</body>
</html>
