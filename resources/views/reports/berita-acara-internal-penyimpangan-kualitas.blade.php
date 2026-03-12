<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-size: 9px; }
        table { border-collapse: collapse; }

        .title {
            font-size: 12px;
            font-weight: bold;
            text-align: center;
        }

        .small { font-size: 8px; }
        .center { text-align: center; }
        .sign { text-align: center; }

        .tbl-main, .tbl-main th, .tbl-main td {
            border: 0.4px solid #000;
        }

        .tbl-main th {
            background-color: #d9d9d9;
            text-align: center;
            font-size: 9px;
        }

        .tbl-main td {
            vertical-align: top;
            padding: 4px;
        }

        .line {
            border-bottom: 0.4px solid #000;
            height: 16px;
        }
    </style>
</head>

<body>

{{-- HEADER --}}

{{-- HEADER LOGO + TITLE --}}
<table width="100%">
    <tr>
        <td class="small" width="40%">
            PT Charoen Pokphand Indonesia<br>
            Food Division
        </td>
        
    </tr>
</table>
<h2 class="title">BERITA ACARA INTERNAL PENYIMPANGAN KUALITAS</h2>
<br>
<br>


@php
$firstPenyimpangan = $penyimpanganKualitas->first();
$nomor = $firstPenyimpangan ? $firstPenyimpangan->nomor : '';
$tanggal = $firstPenyimpangan ? \Carbon\Carbon::parse($firstPenyimpangan->tanggal)->format('d-m-Y') : '';
$ditujukanUntuk = $firstPenyimpangan ? $firstPenyimpangan->ditujukan_untuk : '';
@endphp
{{-- INFO --}}
<table width="100%" class="small">
    <tr>
        <td>Nomor : {{ $nomor }}</td>
    </tr>
    <tr>
        <td>Hari / Tanggal : {{ $tanggal }}</td>
    </tr>
    <tr>
        <td>Ditujukan untuk: {{ $ditujukanUntuk }}</td>
    </tr>
</table>
<span class="small">Telah terjadi penyimpangan tidak sesuai dengan spec dengan hasil sebagai berikut :</span>

<br>

{{-- TABEL UTAMA --}}
<table width="100%" class="tbl-main small">
    <tr>
        <th width="5%" class="center">No</th>
        <th width="25%" class="center">Nama Varian</th>
        <th width="15%" class="center">Lot / Kode</th>
        <th width="15%" class="center">Jumlah</th>
        <th width="20%" class="center">Keterangan</th>
        <th width="20%" class="center">Penyelesaian</th>
    </tr>

    @if($penyimpanganKualitas->count() > 0)
        @foreach($penyimpanganKualitas as $index => $penyimpangan)
        <tr>
            <td class="center">{{ $index + 1 }}</td>
            <td>{{ $penyimpangan->nama_produk ?? '' }}</td>
            <td>{{ $penyimpangan->lot_kode ?? '' }}</td>
            <td></td>
            <td>{{ $penyimpangan->uraian_temuan ?? '' }}</td>
            <td>{{ $penyimpangan->tindakan_perbaikan ?? '' }}</td>
        </tr>
        @endforeach
    @endif
</table>

<br><br>

{{-- TTD --}}
<table width="100%" class="small">
    <tr>
        <td width="33%" class="sign">
            Dibuat Oleh,<br><br><br>
            ( ___________________ )
        </td>
        <td width="33%" class="sign">
            Diketahui Oleh,<br><br><br>
            ( ___________________ )
        </td>
        <td width="33%" class="sign">
            Disetujui Oleh,<br><br><br>
            ( ___________________ )
        </td>
    </tr>
</table>

</body>
</html>
