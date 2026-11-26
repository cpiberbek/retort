<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pemeriksaan Proses Packing</title>
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

    $lines = fn($arr) => empty($arr) ? '-' : implode('<br>', array_map('e', $arr));
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
        PEMERIKSAAN PROSES PACKING
    </h2>
    <br>
    <br>

    <table width="100%" class="tbl-header" cellpadding="0" cellspacing="0">
        <tr>
            <td width="30%">Hari / Tgl : {{ $tanggal ?: '-' }}</td>
            <td width="30%">Shift : {{ $shiftText ?: '-' }} (shift 1,2,3)</td>
            <td width="40%" align="right">Nama Varian : {{ $namaVarian ?: '-' }}</td>
        </tr>
    </table>

    <br>

    <table width="100%" class="tbl-main small" cellpadding="0" cellspacing="0">
        <tr>
            <th rowspan="2" width="4%"><b>Waktu</b></th>
            <th rowspan="2" width="4%"><b>Kalibrasi</b></th>
            <th rowspan="2" width="5%"><b>QR Code</b></th>
            <th colspan="2" width="13%"><b>Kode Produk</b></th>
            <th colspan="2" width="11%"><b>Shrinkle Tunnel</b></th>
            <th rowspan="2" width="5%"><b>Kondisi Segel / Seal</b></th>
            <th rowspan="2" width="5%"><b>Jumlah produk per pack (pcs)</b></th>
            <th rowspan="2" width="5%"><b>Berat produk per pcs (gr)</b></th>
            <th rowspan="2" width="5%"><b>Berat Produk Per Pack (gr)</b></th>
            <th colspan="4" width="25%"><b>Data Kemasan</b></th>
            <th colspan="2" width="10%"><b>Paraf</b></th>
            <th rowspan="2" width="8%"><b>Keterangan</b></th>
        </tr>
        <tr>
            <th width="5%"><b>Printing Label</b></th>
            <th width="8%"><b>Toples</b></th>
            <th width="6%"><b>Suhu</b></th>
            <th width="5%"><b>Speed Conveyor</b></th>
            <th width="5%"><b>Jenis kemasan</b></th>
            <th width="6%"><b>No. Lot Kemasan</b></th>
            <th width="6%"><b>Tgl Kedatangan</b></th>
            <th width="8%"><b>Nama Supplier</b></th>
            <th width="5%"><b>QC</b></th>
            <th width="5%"><b>Produksi</b></th>
        </tr>

        @foreach($page as $row)
            @php $span = count($row['kemasan']); @endphp

            @foreach($row['kemasan'] as $n => $k)
                <tr>
                    @if($n === 0)
                        <td width="4%" rowspan="{{ $span }}">{{ $row['waktu'] }}</td>
                        <td width="4%" rowspan="{{ $span }}">{{ $row['kalibrasi'] }}</td>
                        <td width="5%" rowspan="{{ $span }}">
                            @if($row['qrcode'])
                                <img src="{{ $row['qrcode'] }}" width="30">
                            @endif
                        </td>
                        <td width="5%" rowspan="{{ $span }}">
                            @if($row['printing'])
                                <img src="{{ $row['printing'] }}" width="30">
                            @endif
                        </td>
                        <td width="8%" rowspan="{{ $span }}">{{ $row['toples'] }}</td>
                        <td width="6%" rowspan="{{ $span }}">{!! $lines($row['suhu']) !!}</td>
                        <td width="5%" rowspan="{{ $span }}">{{ $row['speed'] }}</td>
                        <td width="5%" rowspan="{{ $span }}">{{ $row['segel'] }}</td>
                        <td width="5%" rowspan="{{ $span }}">{{ $row['jumlah'] }}</td>
                        <td width="5%" rowspan="{{ $span }}">{{ $row['berat_pcs'] }}</td>
                        <td width="5%" rowspan="{{ $span }}">{{ $row['berat_pack'] }}</td>
                    @endif

                    <td width="5%">{{ $k['jenis'] }}</td>
                    <td width="6%">{{ $k['lot'] }}</td>
                    <td width="6%">{{ $k['tgl'] }}</td>
                    <td width="8%">{{ $k['supplier'] }}</td>

                    @if($n === 0)
                        <td width="5%" rowspan="{{ $span }}">{{ $row['qc'] }}</td>
                        <td width="5%" rowspan="{{ $span }}">{{ $row['produksi'] }}</td>
                        <td width="8%" rowspan="{{ $span }}">{{ $row['ket'] }}</td>
                    @endif
                </tr>
            @endforeach
        @endforeach
    </table>

    <div style="margin-top:5px; text-align:right;">
        <i>{{ $noDokumen ?? '-' }}</i>
    </div>

    @if($loop->last)

        <br>
        <br>

        <table width="100%" class="note small" nobr="true">
            <tr>
                <td width="60%">&nbsp;</td>

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