<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Stuffing</title>
    <style>
        body {
            font-family: helvetica, sans-serif;
            font-size: 8px;
        }
        
        /* HEADER COMPANY */
        .company-header {
            width: 100%;
            border-bottom: 2px solid #000;
            margin-bottom: 10px;
        }
        .company-name {
            font-size: 10px;
            font-weight: bold;
        }
        .report-title {
            font-size: 12px;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            line-height: 1.5;
        }

        /* TABLE DATA STYLING */
        table.tbl-data {
            width: 100%;
            border-collapse: collapse; /* Wajib agar border nyambung */
        }
        
        /* Header Tabel */
        table.tbl-data th {
            background-color: #e3e3e3;
            border: 1px solid #000;
            font-weight: bold;
            text-align: center;
            vertical-align: middle; /* Tengah Vertikal */
            padding: 5px 0;
            line-height: 1.2;
        }

        /* Isi Tabel */
        table.tbl-data td {
            border: 1px solid #000;
            text-align: center;
            vertical-align: middle; /* KUNCI AGAR SEJAJAR */
            padding: 4px 2px;
            line-height: 1.2; /* Jarak antar baris teks */
        }

        /* Utility */
        .text-left { text-align: left !important; padding-left: 4px !important; }
        .text-bold { font-weight: bold; }
        
        /* Status Label */
        .status-ok { color: #006400; font-weight: bold; }
        .status-rev { color: #8B0000; font-weight: bold; }
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
    $pages = array_chunk($columns, 9);
@endphp

@foreach($pages as $page)

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

<table width="100%" border="0" cellpadding="3" cellspacing="0">
    <tr>
        <td width="18%">
        </td>
        <td width="64%" align="center" style="font-size:12pt;"><b>PEMERIKSAAN STUFFING SOSIS RETORT</b></td>
        <td width="18%"></td>
    </tr>
</table>

<br>

<table width="100%" cellpadding="2">
    <tr>
        <td width="20%">Hari/Tgl : {{ \Carbon\Carbon::parse($date)->format('d-m-Y') }}</td>
        <td width="18%">Shift : {{ $shift }}</td>
        <td width="45%">Nama Produk : {{ $namaProduk }}</td>
        <td width="15%" align="right">Exp Date : {{ $expDate }}</td>
    </tr>
</table>

<br>


<table width="100%" border="1" cellpadding="3" cellspacing="0">
    <tr>
        <td width="10%"><b>Kode Batch</b></td>
        <td width="90%" align="center">{{ $batchCodes }}</td>
    </tr>
</table>

<br>

<table width="100%" border="1" cellpadding="2" cellspacing="0">
    @foreach($rows as $row)
        <tr>
            <td width="10%"><b>{{ $row }}</b></td>

            @for($i = 0; $i < 9; $i++)
                <td width="10%" align="center">
                    {{ $page[$i][$row] ?? '' }}
                </td>
            @endfor
        </tr>
    @endforeach
</table>
<table width="100%">
    <tr>
        <td width="75%"></td>
        <td width="25%" align="right">{{ $noDokumen }}</td>
    </tr>
</table>

<table width="100%">
    <tr>
        <td height="30"></td>
    </tr>
</table>


<table width="100%">
    <tr>
        <td width="70%"></td>
        <td width="30%" align="center">
            Disetujui Oleh
            <br><br><br><br>
            ({{ $namaSpv ?: '-' }})<br>
            QC SPV
        </td>
    </tr>
</table>



@if(!$loop->last)
    <table width="100%">
        <tr>
            <td height="80"></td>
        </tr>
    </table>
    <tcpdf method="AddPage" />
@endif

@endforeach

</body>
</html>