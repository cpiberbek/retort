<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Sampling Finish Good</title>
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

@foreach($pages as $page)

    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0;">
        <tr>
            <td style="padding:0; line-height:1;">
                <span style="font-size:12pt;"><b>PT Charoen Pokphand Indonesia</b></span><br>
                <span style="font-size:12pt;"><b>Food Division</b></span>
            </td>
        </tr>
    </table>

    <h2 class="title" style="margin:0; padding:0;">
        PEMERIKSAAN PROSES SAMPLING FINISH GOOD
    </h2>
    <br>
    <br>

    <table width="100%" class="tbl-header" cellpadding="0" cellspacing="0">
        <tr>
            <td width="50%"><b>Hari / Tgl : {{ $tanggal }}</b></td>
            <td width="50%"><b>Shift : {{ $shiftText }}</b></td>
        </tr>
    </table>

    <br>

    <table width="100%" class="tbl-main small" cellpadding="0" cellspacing="0">
        <tr>
            <th rowspan="2" width="4%"><b>Palet</b></th>
            <th rowspan="2" width="14%"><b>Nama Produk</b></th>
            <th rowspan="2" width="10%"><b>Kode Produksi</b></th>
            <th rowspan="2" width="7%"><b>Tanggal Expired</b></th>
            <th colspan="4" width="29%"><b>Pemeriksaan Proses Cartoning</b></th>
            <th rowspan="2" width="6%"><b>Isi produk per box</b></th>
            <th rowspan="2" width="6%"><b>Jumlah (Box)</b></th>
            <th colspan="3" width="15%"><b>Status Produk</b></th>
            <th rowspan="2" width="9%"><b>Keterangan</b></th>
        </tr>
        <tr>
            <th width="5%"><b>Waktu</b></th>
            <th width="6%"><b>Kalibrasi</b></th>
            <th width="7%"><b>Berat Produk per Box</b></th>
            <th width="11%"><b>Keterangan</b></th>
            <th width="5%"><b>Release</b></th>
            <th width="5%"><b>Reject</b></th>
            <th width="5%"><b>Hold</b></th>
        </tr>

        @foreach($page as $row)
            <tr>
                <td width="4%">{{ $row['palet'] }}</td>
                <td width="14%">{{ $row['nama'] }}</td>
                <td width="10%">{{ $row['kode'] }}</td>
                <td width="7%">{{ $row['exp'] }}</td>
                <td width="5%">{{ $row['waktu'] }}</td>
                <td width="6%" style="font-family:zapfdingbats;">{{ $row['kalibrasi'] }}</td>
                <td width="7%">{{ $row['berat'] }}</td>
                <td width="11%">{{ $row['keterangan'] }}</td>
                <td width="6%">{{ $row['isi'] }}</td>
                <td width="6%">{{ $row['jumlah'] }}</td>
                <td width="5%">{{ $row['release'] }}</td>
                <td width="5%">{{ $row['reject'] }}</td>
                <td width="5%">{{ $row['hold'] }}</td>
                <td width="9%">{{ $row['catatan'] }}</td>
            </tr>
        @endforeach

        @if(empty($page))
            <tr>
                <td width="100%" colspan="14">Data tidak ditemukan.</td>
            </tr>
        @endif
    </table>

    <div style="margin-top:5px; text-align:right;">
        <i>{{ $noDokumen ?? '-' }}</i>
    </div>

    @if($loop->last)

        <br>

        <table width="100%" class="note small" nobr="true">
            <tr>
                <td width="100%">
                    <strong>Catatan :</strong>
                    @if(count($catatan))
                        <br>
                        @foreach($catatan as $c)
                            {{ $c }}<br>
                        @endforeach
                    @else
                        -
                    @endif
                </td>
            </tr>
        </table>

        <br>
        <br>

        <table width="100%" class="note small" nobr="true">
            <tr>
                <td width="25%" align="center" style="vertical-align: top;">
                    Dibuat oleh :<br>
                    @if($namaPemeriksa)
                        <br>
                        <img src="data:image/png;base64,{{ $qrPemeriksa }}" width="60" height="60"><br>
                        <u>{{ $namaPemeriksa }}</u><br>
                        QC
                    @endif
                </td>

                <td width="50%">&nbsp;</td>

                <td width="25%" align="center" style="vertical-align: top;">
                    Diverifikasi oleh :<br>
                    @if($namaSpv)
                        <br>
                        <img src="data:image/png;base64,{{ $qrSpv }}" width="60" height="60"><br>
                        <u>{{ $namaSpv }}</u><br>
                        SPV QC
                    @elseif(!$semuaDisetujui && !empty($pages[0]))
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