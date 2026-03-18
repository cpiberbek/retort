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
        .sign { text-align: center; }

        .tbl-main, .tbl-main th, .tbl-main td {
            border: 0.3px solid #000;
        }

        .tbl-main th {
            text-align: center;
            vertical-align: middle;
            font-size: 8px;
        }

        .tbl-header td {
            padding: 2px;
            font-size: 8px;
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
<h2 class="title">PEMERIKSAAN LOADING – UNLOADING PRODUK</h2>
<br>
<br>

@php
$firstLoading = $loadings->first();
$date = $firstLoading ? \Carbon\Carbon::parse($firstLoading->tanggal)->format('d-m-Y') : '';
$shift = $firstLoading ? $firstLoading->shift : '';
$jamMulai = $firstLoading ? $firstLoading->jam_mulai : '';
$jamSelesai = $firstLoading ? $firstLoading->jam_selesai : '';
$noPol = $firstLoading ? $firstLoading->no_pol_mobil : '';
$namaSupir = $firstLoading ? $firstLoading->nama_supir : '';
$jenisKendaraan = $firstLoading ? $firstLoading->jenis_kendaraan : '';
$ekspedisi = $firstLoading ? $firstLoading->ekspedisi : '';
$tujuanAsal = $firstLoading ? $firstLoading->tujuan_asal : '';
$noSegel = $firstLoading ? $firstLoading->no_segel : '';
@endphp
{{-- INFO --}}
<table width="100%" class="tbl-header">
    <tr>
        <td width="15%">Hari / Tanggal</td>
        <td width="35%">: {{ $date }}</td>
        <td width="15%">Jam Mulai</td>
        <td width="35%">: {{ $jamMulai }}</td>
    </tr>
    <tr>
        <td>Shift</td>
        <td>: {{ $shift }}</td>
        <td>Jam Selesai</td>
        <td>: {{ $jamSelesai }}</td>
    </tr>
    <tr>
        <td>No. Pol Mobil</td>
        <td>: {{ $noPol }}</td>
        <td>Nama Sopir</td>
        <td>: {{ $namaSupir }}</td>
    </tr>
    <tr>
        <td>Jenis Kendaraan</td>
        <td>: {{ $jenisKendaraan }}</td>
        <td>Ekspedisi</td>
        <td>: {{ $ekspedisi }}</td>
    </tr>
    <tr>
        <td>Kondisi Mobil</td>
        <td colspan="3">: isi sesuai kolom keterangan di bawah</td>
    </tr>
    <tr>
        <td>Tujuan / Asal</td>
        <td>: {{ $tujuanAsal }}</td>
        <td>No. Segel</td>
        <td>: {{ $noSegel }}</td>
    </tr>
</table>

<br>

{{-- TABEL UTAMA --}}
<table width="100%" class="tbl-main small">
    <tr>
        <th width="4%" class="center">No</th>
        <th width="26%" class="center">Nama Varian</th>
        <th width="18%" class="center">Kode Batch</th>
        <th width="18%" class="center">Kode Expired</th>
        <th width="10%" class="center">Jumlah</th>
        <th width="24%" class="center"> Keterangan </th>
    </tr>

    @php
    $allDetails = [];
    foreach($loadings as $loading) {
        if($loading->details && $loading->details->count() > 0) {
            foreach($loading->details as $detail) {
                $allDetails[] = $detail;
            }
        }
    }
    @endphp

    @if(count($allDetails) > 0)
        @foreach($allDetails as $index => $detail)
        <tr>
            <td class="center">{{ $index + 1 }}</td>
            <td>{{ $detail->nama_produk ?? '' }}</td>
            <td>{{ $detail->kode_produksi ?? '' }}</td>
            <td>{{ $detail->kode_expired ? \Carbon\Carbon::parse($detail->kode_expired)->format('d-m-Y') : '' }}</td>
            <td class="center">{{ $detail->jumlah ?? '' }}</td>
            <td>{{ $detail->keterangan ?? '' }}</td>
        </tr>
        @endforeach
    @endif

    @if(count($allDetails) < 18)
    @for($i = count($allDetails) + 1; $i <= 18; $i++)
    <tr>
        <td class="center">{{ $i }}</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    @endfor
    @endif
</table>
<div style="text-align:right; font-size:8px; font-style:italic">QT 18 / 01</div>
<br>

{{-- KETERANGAN --}}
<table width="100%" class="small">
    <tr>
        <td colspan="4">Keterangan :<br></td>
    </tr>
    <tr>
        <td>√ OK<br>X Tidak</td>
        <td>1 Bersih<br>2 Bocor<br>3 Debu</td>
        <td>4 Kering<br>5 Basah<br>6 Hama</td>
        <td>7 Noda (Karat, cat, tinta)<br>8 Bekas oli di lantai, di dinding<br>9 Tidak ada varian non halal</td>
    </tr>
</table>

<br><br>
<br>

{{-- TTD --}}
<table width="100%" class="small">
    <tr>
        <td width="33%" class="sign">
            Diperiksa oleh,<br><br><br>
            ( ___________________ )<br>
            QC
        </td>
        <td width="33%" class="sign">
            Diketahui oleh,<br><br><br>
            ( ___________________ )<br>
            Warehouse
        </td>
        <td width="33%" class="sign">
            Disetujui oleh,<br><br><br>
            ( ___________________ )<br>
            QC SPV
        </td>
    </tr>
</table>



</body>
</html>
