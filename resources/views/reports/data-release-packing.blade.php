
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Release Packing</title>
    <style>
        body {
            font-family: times;
            font-size: 9pt;
            line-height: 1.15;
            color: #000;
        }

        table {
            border-collapse: collapse;
        }

        .company-name {
            font-size: 12pt;
            font-weight: bold;
            line-height: 1.1;
        }

        .report-title {
            font-size: 13pt;
            font-weight: bold;
            text-align: center;
        }

        .filter-table td {
            font-size: 8pt;
            padding: 2px;
        }

        .tbl-main {
            width: 100%;
            border-collapse: collapse;
        }

        .tbl-main th,
        .tbl-main td {
            border: 0.8px solid #000;
            padding: 3px 2px;
            text-align: center;
            vertical-align: middle;
            font-size: 8pt;
        }

        .tbl-main th {
            background-color: #d9e2f3;
            font-weight: bold;
            line-height: 1.15;
        }

        .document-number {
            font-size: 8pt;
            text-align: right;
        }

        .notes {
            font-size: 8pt;
            text-align: left;
            vertical-align: top;
            padding: 3px;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }

        .signature {
            font-size: 9pt;
            text-align: center;
            vertical-align: top;
            line-height: 1.2;
        }

        .signature img {
            width: 25mm;
            height: 25mm;
        }
    </style>
</head>
<body>

<table width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td width="60" align="left" valign="middle">
            <img src="{{ public_path('assets/img/Logo CPI.png') }}" width="48">
        </td>
        <td align="left" valign="middle" class="company-name">
            PT Charoen<br>
            Pokphand Indonesia<br>
            Food Division
        </td>
    </tr>
</table>

<br>

<table width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td class="report-title">
            DATA RELEASE PACKING
        </td>
    </tr>
</table>

<br>

<table width="100%" cellpadding="2" cellspacing="0" border="0" class="filter-table">
    <tr>
        <td width="50%" align="center">
            <b>Hari/Tgl:</b>
            {{ $date ? \Carbon\Carbon::parse($date)->format('d-m-Y') : 'SEMUA' }}
        </td>
        <td width="50%" align="center">
            <b>Jenis Kemasan:</b>
            {{ $jenisKemasan ?: 'Semua Jenis Kemasan' }}
        </td>
    </tr>
</table>

<br>

<table width="100%" class="tbl-main" cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <th width="4%">No.</th>
            <th width="24%">Nama Varian</th>
            <th width="24%">Kode Batch</th>
            <th width="14%">Best Before</th>
            <th width="10%">No. Palet</th>
            <th width="14%">Jumlah Release</th>
            <th width="10%">Paraf QC</th>
        </tr>
    </thead>
    <tbody>
        @forelse($release_packings as $packing)
            <tr>
                <td width="4%">
                    {{ ($pageIndex * $perPage) + $loop->iteration }}
                </td>
                <td width="24%">
                    {{ $packing->nama_produk ?? '-' }}
                </td>
                <td width="24%">
                    {{ $packing->kode_produksi ?? '-' }}
                </td>
                <td width="14%">
                    {{ $packing->expired_date ? \Carbon\Carbon::parse($packing->expired_date)->format('d-m-Y') : '-' }}
                </td>
                <td width="10%">
                    {{ $packing->no_palet ?? '-' }}
                </td>
                <td width="14%">
                    {{ $packing->release ?? '-' }}
                </td>
                <td width="10%">
                    {{ \App\Models\User::where('username', $packing->username_updated ?? $packing->username)->value('name') ?? '-' }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" width="100%" align="center">
                    Tidak ada data release packing
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

<br>

<table width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td width="100%" class="document-number">
            <i>{{ $noDokumen ?? '-' }}</i>
        </td>
    </tr>
</table>

@if($lastPage)

    <br>

    <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td width="100%" class="notes">
                <b>Catatan:</b>
                @if($keterangan->isNotEmpty())
                    @foreach($keterangan as $catatan)
                        <br>- {{ $catatan }}
                    @endforeach
                @else
                    <br>-
                @endif
            </td>
        </tr>
    </table>

    <br><br>

    
@php
    $namaQcBersih  = (!empty($namaQc) && trim($namaQc) !== '-') ? trim($namaQc) : '';
    $namaSpvBersih = ($semuaDisetujui && !empty($namaSpv) && trim($namaSpv) !== '-') ? trim($namaSpv) : '';

    $makeQrTtd = function ($nama, $tanggal) {
        if (empty($nama)) {
            return '';
        }
        $teks = 'Ditandatangani secara digital oleh ' . $nama;
        if (!empty($tanggal)) {
            $teks .= ' pada ' . \Carbon\Carbon::parse($tanggal)->format('d-m-Y');
        }
        $barcode = new \TCPDF2DBarcode($teks, 'QRCODE,H');
        return base64_encode($barcode->getBarcodePngData(4, 4, [0, 0, 0]));
    };

    $qrQc  = $makeQrTtd($namaQcBersih, $tanggalQc ?? '');
    $qrSpv = $makeQrTtd($namaSpvBersih, $tanggalSpv ?? '');
@endphp

<table width="100%" cellpadding="0" cellspacing="0" border="0" nobr="true">
    <tr>
        <td width="60%"></td>

        <td width="20%" align="center" style="vertical-align: top;">
            @if($namaQcBersih)
                Dibuat oleh :<br>
                <u>{{ $namaQcBersih }}</u><br>
                <br>
                <img src="data:image/png;base64,{{ $qrQc }}" width="60" height="60"><br>
                QC
            @endif
        </td>

        <td width="20%" align="center" style="vertical-align: top;">
            Diverifikasi oleh :<br>
            @if($namaSpvBersih)
                <u>{{ $namaSpvBersih }}</u><br>
                <br>
                <img src="data:image/png;base64,{{ $qrSpv }}" width="60" height="60"><br>
                SPV QC
            @else
                <i>Belum semua entry disetujui</i><br>
                <br>
                <br>
                <br>
                <br>
                <br>
                SPV QC
            @endif
        </td>
    </tr>
</table>


@endif

</body>
</html>
