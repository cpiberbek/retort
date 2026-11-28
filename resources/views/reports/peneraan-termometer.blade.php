<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Peneraan Termometer</title>
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
        PENERAAN TERMOMETER
    </h2>
    <br>
    <br>

    <table width="100%" class="tbl-header" cellpadding="0" cellspacing="0">
        <tr>
            <td width="100%">Hari / Tanggal : {{ $tanggal }}</td>
        </tr>
    </table>

    <br>

    <table width="100%" class="tbl-main small" cellpadding="0" cellspacing="0">
        <tr>
            <th rowspan="2" width="22%"><b>KODE TERMOMETER / AREA</b></th>
            <th rowspan="2" width="12%"><b>STANDAR</b></th>
            <th colspan="2" width="20%"><b>PENERAAN</b></th>
            <th rowspan="2" width="22%"><b>TINDAKAN PERBAIKAN</b></th>
            <th rowspan="2" width="12%"><b>DIBUAT<br>QC</b></th>
            <th rowspan="2" width="12%"><b>DIKETAHUI<br>SPV QC</b></th>
        </tr>
        <tr>
            <th width="8%"><b>PUKUL</b></th>
            <th width="12%"><b>HASIL TERA</b></th>
        </tr>

        @foreach($page as $row)
            <tr>
                <td width="22%" style="height:28px;">
                    @if($row['kode'] !== '' || $row['area'] !== '')
                        {{ $row['kode'] }} /<br>{{ $row['area'] }}
                    @endif
                </td>
                <td width="12%">{{ $row['standar'] }}</td>
                <td width="8%">{{ $row['pukul'] }}</td>
                <td width="12%">{{ $row['hasil'] }}</td>
                <td width="22%">{{ $row['tindakan'] }}</td>
                <td width="12%">{{ $row['qc'] }}</td>
                <td width="12%">{{ $row['spv'] }}</td>
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
                    <strong>Keterangan :</strong><br>
                    - Tera termometer dilakukan di setiap awal shift<br>
                    - Termometer ditera dengan memasukkan sensor (probe) ke es (0 °C)<br>
                    - Jika ada selisih angka display suhu dengan suhu standar, beri keterangan (+) atau (–) angka selisih (faktor koreksi)<br>
                    - Jika faktor koreksi &lt; 0,5 °C, termometer perlu perbaikan
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
                    @elseif(!$semuaDisetujui && $tanggal !== '')
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