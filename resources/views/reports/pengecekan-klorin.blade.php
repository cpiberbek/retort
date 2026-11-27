<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pengecekan Klorin</title>
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
        PENGECEKAN KLORIN
    </h2>
    <br>
    <br>

    <table width="100%" class="tbl-main small" cellpadding="0" cellspacing="0">
        <tr>
            <th colspan="3" width="68%"><b>Hari / Tanggal : {{ $tanggal }}</b></th>
            <th colspan="2" width="32%"><b>Paraf</b></th>
        </tr>
        <tr>
            <th width="12%"><b>Pukul</b></th>
            <th width="28%"><b>Foot Basin</b></th>
            <th width="28%"><b>Hand Basin</b></th>
            <th width="16%"><b>QC</b></th>
            <th width="16%"><b>Produksi</b></th>
        </tr>

        @foreach($page as $row)
            <tr>
                <td width="12%">{{ $row['pukul'] }}</td>
                <td width="28%">
                    @if($row['foot'])
                        <img src="{{ $row['foot'] }}" width="120">
                    @endif
                </td>
                <td width="28%">
                    @if($row['hand'])
                        <img src="{{ $row['hand'] }}" width="120">
                    @endif
                </td>
                <td width="16%">{{ $row['qc'] }}</td>
                <td width="16%">{{ $row['produksi'] }}</td>
            </tr>
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
                <td width="60%">
                    Keterangan : V Sesuai Standar<br>
                    Catatan :
                    @if(!empty($catatan))
                        {{ $catatan }}
                    @else
                        -
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