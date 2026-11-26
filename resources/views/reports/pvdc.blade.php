<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan PVDC</title>
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
            line-height: 1.4;
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
        DATA NO. LOT PVDC
    </h2>
    <br>
    <br>

    <table width="100%" class="tbl-header" cellpadding="0" cellspacing="0">
        <tr>
            <td width="35%"><strong>Hari / Tanggal</strong> : {{ $tanggal ?: '-' }}</td>
            <td width="20%"><strong>Shift</strong> : {{ $shiftText ?: '-' }}</td>
            <td width="45%"><strong>Nama Produk</strong> : {{ $namaProduk ?: '-' }}</td>
        </tr>
        <tr>
            <td width="55%" colspan="2"><strong>Tanggal kedatangan PVDC</strong> : {{ $tglKedatangan ?: '-' }}</td>
            <td width="45%"><strong>Tanggal expired</strong> : {{ $tglExpired ?: '-' }}</td>
        </tr>
    </table>

    <br>

    @if(empty($page))

        <table width="100%" class="tbl-main small" cellpadding="0" cellspacing="0">
            <tr>
                <th width="30%"><b>Batch</b></th>
                <th width="40%"><b>No. Lot</b></th>
                <th width="30%"><b>Waktu</b></th>
            </tr>
        </table>

    @else

        @foreach($page as $band)

            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    @foreach($band as $slot => $mesin)
                        @if($slot > 0)
                            <td width="2%"></td>
                        @endif

                        <td width="32%" style="vertical-align: top;">
                            @if($mesin)
                                <table width="100%" class="tbl-main small" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <th width="100%" colspan="3"><b>Mesin : {{ $mesin['mesin'] }}</b></th>
                                    </tr>
                                    <tr>
                                        <th width="30%"><b>Batch</b></th>
                                        <th width="40%"><b>No. Lot</b></th>
                                        <th width="30%"><b>Waktu</b></th>
                                    </tr>
                                    @foreach($mesin['rows'] as $row)
                                        <tr>
                                            <td width="30%" class="center">{{ $row['batch'] }}</td>
                                            <td width="40%" class="center">{{ $row['lot'] }}</td>
                                            <td width="30%" class="center">{{ $row['waktu'] }}</td>
                                        </tr>
                                    @endforeach
                                </table>
                            @endif
                        </td>
                    @endforeach
                </tr>
            </table>

            <br>

        @endforeach

    @endif

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