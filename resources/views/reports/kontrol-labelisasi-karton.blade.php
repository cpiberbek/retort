<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-size: 9px; }
        table { border-collapse: collapse; }

        .title { font-weight: bold; font-size: 12px; text-align: center; }
        .small { font-size: 8px; }
        .center { text-align: center; }
        .right { text-align: right; }

        .box, .box td, .box th {
            border: 0.5px solid #000;
        }

        .box td {
            padding: 4px;
            vertical-align: top;
        }

        .header td {
            border: 0.5px solid #000;
            padding: 4px;
            vertical-align: middle;
        }

        .sign { text-align: center; }
    </style>
</head>
<body>

{{-- HEADER --}}
<table width="100%" class="header">
    <tr>
        <td width="15%" class="center">
            <strong>CP</strong>
        </td>
        <td width="45%" class="center">
            <div class="title">FORM</div>
            <div><strong>KONTROL LABELISASI KARTON</strong></div>
        </td>
        <td width="40%" class="small">
            <table width="100%">
                <tr><td>No. Dokumen</td><td>: FR-QC-14</td></tr>
                <tr><td>Revisi</td><td>: 2</td></tr>
                <tr><td>Tanggal Efektif</td><td>: 18/12/2003</td></tr>
                <tr><td>Halaman</td><td>: 1 dari 1</td></tr>
            </table>
        </td>
    </tr>
</table>

<br>

@php
    $dateFilter = request('date') ? \Carbon\Carbon::parse(request('date'))->format('d-m-Y') : 'All Dates';
@endphp

{{-- BODY --}}
<table width="100%" class="box">
    <tr>
        <th width="5%">No.</th>
        <th width="10%">Tanggal</th>
        <th width="10%">Start - Finish</th>
        <th width="15%">Nama Varian</th>
        <th width="15%">Kode Batch</th>
        <th width="15%">Nama Supplier</th>
        <th width="10%">No. Lot Karton</th>
        <th width="5%">Operator</th>
        <th width="5%">QC</th>
        <th width="5%">Koordinator</th>
        <th width="10%">Keterangan</th>
    </tr>

    @forelse($kartons as $index => $karton)
    <tr>
        <td class="center">{{ $index + 1 }}</td>
        <td class="center">{{ \Carbon\Carbon::parse($karton->date)->format('d-m-Y') }}</td>
        <td class="center">{{ \Carbon\Carbon::parse($karton->waktu_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($karton->waktu_selesai)->format('H:i') }}</td>
        <td class="center">{{ $karton->nama_produk ?? '-' }}</td>
        <td class="center">{{ $karton->kode_produksi ?? '-' }}</td>
        <td class="center">{{ $karton->nama_supplier ?? '-' }}</td>
        <td class="center">{{ $karton->no_lot ?? '-' }}</td>
        <td class="center">{{ $karton->nama_operator ?? '-' }}</td>
        <td class="center">{{ $karton->username ?? '-' }}</td>
        <td class="center">{{ $karton->nama_koordinator ?? '-' }}</td>
        <td class="center">{{ $karton->keterangan ?? '-' }}</td>
    </tr>
    @empty
    <tr>
        <td colspan="11" class="center">Tidak ada data kontrol labelisasi karton</td>
    </tr>
    @endforelse
</table>

<br><br><br>

{{-- TTD --}}
<table width="100%" class="small">
    <tr>
        <td width="60%"></td>
        <td width="40%" class="sign">
            Diverifikasi oleh,<br><br><br><br>
            ( ___________________ )<br>
            QC SPV
        </td>
    </tr>
</table>

</body>
</html>
