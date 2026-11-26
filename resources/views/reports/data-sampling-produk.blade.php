<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Sampling Produk</title>
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 7px;
            color: #000;
        }

        table {
            border-collapse: collapse;
        }

        .title {
            font-size: 13px;
            font-weight: bold;
            text-align: center;
        }

        .small {
            font-size: 7px;
        }

        .center {
            text-align: center;
            vertical-align: middle;
        }

        .tbl-header td {
            padding: 0;
            font-size: 8px;
            line-height: 1;
        }

        .tbl-main,
        .tbl-main th,
        .tbl-main td {
            padding: 3px 2px;
            text-align: center;
            vertical-align: middle;
            border: 0.8px solid #000;
        }

        .tbl-main th {
            background: #d9e2f3;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
            padding: 3px 2px;
            line-height: 1.15;
        }

        .tbl-main td {
            padding: 3px 2px;
            vertical-align: middle;
        }

        .note td {
            vertical-align: top;
            line-height: 1.4;
        }
    </style>
</head>

<body>

@php
    $namaPemeriksa = (!empty($pemeriksa) && $pemeriksa !== '-') ? $pemeriksa : '';
    $namaSpv       = (!empty($spv) && $spv !== '-') ? $spv : '';

    $makeQr = function ($nama, $tanggal) {
        if (empty($nama)) {
            return '';
        }
        $teks = 'Ditandatangani secara digital oleh ' . $nama;
        if (!empty($tanggal)) {
            $teks .= ' pada ' . $tanggal;
        }
        $barcode = new \TCPDF2DBarcode($teks, 'QRCODE,H');
        return base64_encode($barcode->getBarcodePngData(4, 4, [0, 0, 0]));
    };

    $qrPemeriksa = $makeQr($namaPemeriksa, $tglPemeriksa ?? '');
    $qrSpv       = $makeQr($namaSpv, $tglSpv ?? '');
@endphp

@foreach($pages as $pageIndex => $page)

    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0;">
        <tr>
            <td style="padding:0; line-height:1;">
                <span style="font-size:12pt;"><b>PT Charoen Pokphand Indonesia</b></span><br>
                <span style="font-size:12pt;"><b>Food Division</b></span>
            </td>
        </tr>
    </table>

    <h2 class="title" style="margin:0; padding:0;">
        DATA SAMPLING PRODUK
    </h2>
    <br>
    <br>

    <table width="100%" class="tbl-header" cellpadding="0" cellspacing="0">
        <tr>
            <td width="50%" align="center">Hari / Tanggal : <u>{{ $dateFilter }}</u></td>
            <td width="50%" align="center">Shift : <u>{{ $shiftFilter }}</u></td>
        </tr>
    </table>

    <br>

    <table width="100%" class="tbl-main small" cellpadding="0" cellspacing="0">
        <tr>
            <th rowspan="2" width="3%"><b>No.</b></th>
            <th rowspan="2" width="6%"><b>Jenis Sampling</b></th>
            <th rowspan="2" width="8%"><b>Nama Produk</b></th>
            <th rowspan="2" width="8%"><b>Kode Batch</b></th>
            <th rowspan="2" width="6%"><b>Jumlah</b></th>
            <th colspan="16" width="62%"><b>Item Sortir</b></th>
            <th rowspan="2" width="7%"><b>Paraf QC</b></th>
        </tr>
        <tr>
            @foreach($labels as $label)
                <th width="3.875%"><b>{{ $label }}</b></th>
            @endforeach
        </tr>

        @foreach($page as $row)
            <tr>
                <td width="3%">{{ $row['no'] }}</td>
                <td width="6%">{{ $row['jenis'] }}</td>
                <td width="8%">{{ $row['nama'] }}</td>
                <td width="8%">{{ $row['kode'] }}</td>
                <td width="6%">{{ $row['jumlah'] }}</td>
                @foreach($row['values'] as $value)
                    <td width="3.875%">{{ $value }}</td>
                @endforeach
                <td width="7%">{{ $row['paraf'] }}</td>
            </tr>
        @endforeach

        @if(empty($page))
            <tr>
                <td width="100%" colspan="22">Tidak ada data sampling</td>
            </tr>
        @endif
    </table>

    <div style="margin-top:5px; text-align:right;">
        <i>{{ $noDokumen ?? '-' }}</i>
    </div>

    @if($loop->last)

        <br>
        <br>

        <table width="100%" class="note small" nobr="true">
            <tr>
                <td width="60%">
                    @if(count($catatanList))
                        <strong>Catatan :</strong><br>
                        @foreach($catatanList as $catatan)
                            {{ $catatan }}<br>
                        @endforeach
                    @endif
                </td>

                <td width="20%" align="center" style="vertical-align: top;">
                    Dibuat oleh :<br>
                    @if($namaPemeriksa)
                        <br>
                        <img src="data:image/png;base64,{{ $qrPemeriksa }}" width="60" height="60"><br>
                        <u>{{ $namaPemeriksa }}</u><br>
                        QC
                    @endif
                </td>

                <td width="20%" align="center" style="vertical-align: top;">
                    Diverifikasi oleh :<br>
                    @if($namaSpv)
                        <br>
                        <img src="data:image/png;base64,{{ $qrSpv }}" width="60" height="60"><br>
                        <u>{{ $namaSpv }}</u><br>
                        SPV QC
                    @elseif(!$semuaDisetujui)
                        <i>Belum semua entry disetujui</i>
                    @endif
                </td>
            </tr>
        </table>

    @else
        <br pagebreak="true" />
    @endif

@endforeach

</body>
</html>