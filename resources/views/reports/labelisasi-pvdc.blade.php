<!DOCTYPE html>
<html>

<head>
<meta charset="utf-8">

<style>
body{
    font-family: helvetica;
    font-size:10px;
}

.title{
    text-align:center;
    font-size:15px;
    font-weight:bold;
    margin-bottom:10px;
}

.info{
    width:100%;
    border-collapse:collapse;
    margin-bottom:8px;
}

.info td{
    border:none;
    padding:2px;
}

.main{
    width:100%;
    border-collapse:collapse;
}

.main th,
.main td{
    border:1px solid #000;
    padding:5px;
    vertical-align:middle;
}

.main th{
    text-align:center;
    font-weight:bold;
}

.center{
    text-align:center;
}

.row{
    height:28px;
}

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

@php
$header = $produks->first();
@endphp

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

<div class="title">
    DATA LABELISASI PVDC
</div>
<br>
<table class="info">
    <tr>
        <td width="10%"><b>Hari / Tanggal</b></td>
        <td width="15%">: {{ $header ? \Carbon\Carbon::parse($header->date)->format('d-m-Y') : '-' }}</td>

        <td width="10%"><b>Shift</b></td>
        <td width="15%">: {{ $header->shift ?? '-' }}</td>

        <td width="10%"><b>Nama Produk</b></td>
        <td width="40%">: {{ $header->nama_produk ?? '-' }}</td>
    </tr>
</table>
<br><br>
<table class="main">

    <thead>
        <tr>
            <th width="20%" align="center">Kode<br>Mesin</th>
            <th width="20%" align="center">Kode Produksi</th>
            <th width="20%" align="center">Paraf<br>Operator</th>
            <th width="20%" align="center">Paraf<br>QC</th>
            <th width="20%" align="center">Keterangan</th>
        </tr>
    </thead>

    <tbody>

    @foreach($produks as $row)
        @foreach($row->labelisasi_detail as $item)

        <tr class="row">
            <td class="center">
                {{ $item['mesin'] }}
            </td>

            <td>
                {{ optional($item['mincing'])->kode_produksi }}
            </td>

            <td class="center">
                {{ !empty($row->username_updated) ? $row->username_updated : $row->username }}
            </td>

            <td class="center">
                {{ $row->nama_spv }}
            </td>

            <td>
                {{ $item['keterangan'] }}
            </td>
        </tr>

        @endforeach
    @endforeach

    </tbody>

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