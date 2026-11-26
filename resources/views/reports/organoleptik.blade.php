<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pemeriksaan Organoleptik</title>
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
        PEMERIKSAAN ORGANOLEPTIK
    </h2>
    <br>
    <br>

    <table width="100%" class="tbl-header" cellpadding="0" cellspacing="0">
        <tr>
            <td width="30%">Tanggal : {{ $dateFilter }}</td>
            <td width="30%">Shift : {{ $shiftFilter }}</td>
            <td width="40%">Nama Varian : {{ $namaProdukFilter }}</td>
        </tr>
    </table>

    <br>

    <table width="100%" class="tbl-main small" cellpadding="0" cellspacing="0">
        <tr>
            <th rowspan="2" width="12%"><b>Kode Batch</b></th>
            <th colspan="8" width="64%"><b>Sensori</b></th>
            <th rowspan="2" width="8%"><b>Hasil Score</b></th>
            <th rowspan="2" width="8%"><b>Release / Tidak Release</b></th>
            <th rowspan="2" width="8%"><b>Paraf QC</b></th>
        </tr>
        <tr>
            <th width="8%"><b>Penampilan</b></th>
            <th width="8%"><b>Aroma</b></th>
            <th width="8%"><b>Kekenyalan</b></th>
            <th width="8%"><b>Rasa Asin</b></th>
            <th width="8%"><b>Rasa Gurih</b></th>
            <th width="8%"><b>Rasa Manis</b></th>
            <th width="8%"><b>Rasa Ayam/BBQ/Ikan</b></th>
            <th width="8%"><b>Rasa Keseluruhan</b></th>
        </tr>

        @foreach($page as $row)
            <tr>
                <td width="12%">{{ $row['kode'] }}</td>
                <td width="8%">{{ $row['penampilan'] }}</td>
                <td width="8%">{{ $row['aroma'] }}</td>
                <td width="8%">{{ $row['kekenyalan'] }}</td>
                <td width="8%">{{ $row['asin'] }}</td>
                <td width="8%">{{ $row['gurih'] }}</td>
                <td width="8%">{{ $row['manis'] }}</td>
                <td width="8%">{{ $row['daging'] }}</td>
                <td width="8%">{{ $row['keseluruhan'] }}</td>
                <td width="8%">{{ $row['score'] }}</td>
                <td width="8%">{{ $row['release'] }}</td>
                <td width="8%">{{ $row['paraf'] }}</td>
            </tr>
        @endforeach

        @if(empty($page))
            <tr>
                <td width="100%" colspan="12">Tidak ada data sensori</td>
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
                    <strong>Keterangan Parameter :</strong><br>
                    Penampilan, Aroma, Rasa Keseluruhan : 1 = Sangat Tidak, 2 = Biasa, 3 = Sangat<br>
                    Kekenyalan, Manis, Asin, Gurih, Ayam/BBQ/Ikan : 1 = Terlalu, 2 = Kurang, 3 = Pas<br>
                    Keterangan Hasil Score : 1 - 1,5 = Tidak Release, 1,6 - 3 = Release
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