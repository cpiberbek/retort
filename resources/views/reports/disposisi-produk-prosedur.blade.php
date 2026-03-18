<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-size: 9px; }
        table { border-collapse: collapse; }

        .title {
            font-size: 13px;
            font-weight: bold;
            text-align: center;
        }

        .small { font-size: 8px; }
        .center { text-align: center; }
        .box {
            border: 0.6px solid #000;
            height: 12px;
            width: 20px;
            display: inline-block;
        }

        .tbl-main, .tbl-main td {
            border: 0.6px solid #000;
        }

        .tbl-main td {
            padding: 4px;
            vertical-align: top;
        }

        .sign { text-align: center; }
        .line { height: 18px; border-bottom: 0.4px solid #000; }
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
<h2 class="title">DISPOSISI VARIAN DAN PROSEDUR</h2>
<br>
<br>


@php
$firstDisposition = $dispositions->first();
$nomor = $firstDisposition ? $firstDisposition->nomor : '';
$tanggal = $firstDisposition ? \Carbon\Carbon::parse($firstDisposition->tanggal)->format('d-m-Y') : '';
$kepada = $firstDisposition ? $firstDisposition->kepada : '';
$produk = $firstDisposition && $firstDisposition->disposisi_produk ? '√' : '';
$material = $firstDisposition && $firstDisposition->disposisi_material ? '√' : '';
$prosedur = $firstDisposition && $firstDisposition->disposisi_prosedur ? '√' : '';
$dasar = $firstDisposition ? $firstDisposition->dasar_disposisi : '';
$uraian = $firstDisposition ? $firstDisposition->uraian_disposisi : '';
$catatan = $firstDisposition ? $firstDisposition->catatan : '';
@endphp
<table width="100%" class="tbl-main small">
    <tr>
        <td width="20%">Nomor</td>
        <td width="80%">: {{ $nomor }}</td>
    </tr>
    <tr>
        <td>Tanggal</td>
        <td>: {{ $tanggal }}</td>
    </tr>
    <tr>
        <td>Kepada</td>
        <td>: {{ $kepada }}</td>
    </tr>

    <tr>
        <td>Disposisi</td>
        <td>
            <span class="box">{{ $produk }}</span> Varian &nbsp;&nbsp;
            <span class="box">{{ $material }}</span> Material &nbsp;&nbsp;
            <span class="box">{{ $prosedur }}</span> Prosedur
        </td>
    </tr>

    <tr>
        <td>Dasar Disposisi</td>
        <td>
            <div class="line">{{ $dasar }}</div>
        </td>
    </tr>

    <tr>
        <td>Uraian Disposisi</td>
        <td>
            <div class="line">{{ $uraian }}</div>
        </td>
    </tr>

    <tr>
        <td>Catatan</td>
        <td>
            <div class="line">{{ $catatan }}</div>
        </td>
    </tr>

    <tr>
        <td>CC</td>
        <td>:</td>
    </tr>
</table>

<br><br>

<table width="100%" class="small">
    <tr>
        <td width="25%" class="sign">
            Dibuat Oleh,<br><br><br>
            ( _____________ )<br>
            Spv. QC
        </td>
        <td width="25%" class="sign">
            Mengetahui,<br><br><br>
            ( _____________ )<br>
            Spv. Produksi
        </td>
        <td width="25%" class="sign">
            Diperiksa Oleh,<br><br><br>
            ( _____________ )<br>
            Manager Produksi
        </td>
        <td width="25%" class="sign">
            <br><br><br>
            ( _____________ )<br>
            Manager QC
        </td>
    </tr>
</table>

<div style="text-align:right; font-size:8px;">QT 29 / 00</div>

</body>
</html>
