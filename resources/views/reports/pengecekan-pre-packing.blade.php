<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pemeriksaan Pre Packing</title>
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
        PEMERIKSAAN PRE PACKING
    </h2>
    <br>
    <br>

    <table width="100%" class="tbl-header" cellpadding="0" cellspacing="0">
        <tr>
            <td width="100%">Hari / Tanggal : {{ $dateFilter }}</td>
        </tr>
    </table>

    <br>

    <table width="100%" class="tbl-main small" cellpadding="0" cellspacing="0">
        <tr>
            <th rowspan="2" width="3%"><b>No</b></th>
            <th colspan="2" width="21%"><b>Varian</b></th>
            <th rowspan="2" width="7%"><b>No.<br>Conveyor</b></th>
            <th rowspan="2" width="7%"><b>Suhu<br>Varian</b></th>
            <th rowspan="2" width="8%"><b>Bagian<br>Badan Sosis</b></th>
            <th colspan="2" width="14%"><b>Air (%)</b></th>
            <th colspan="2" width="14%"><b>Minyak (%)</b></th>
            <th colspan="2" width="16%"><b>Berat Varian per</b></th>
            <th rowspan="2" width="10%"><b>PARAF<br>QC</b></th>
        </tr>
        <tr>
            <th width="12%"><b>Nama</b></th>
            <th width="9%"><b>Kode</b></th>
            <th width="7%"><b>Basah</b></th>
            <th width="7%"><b>Kering</b></th>
            <th width="7%"><b>Basah</b></th>
            <th width="7%"><b>Kering</b></th>
            <th width="8%"><b>pcs</b></th>
            <th width="8%"><b>Toples<br>(berat kotor)</b></th>
        </tr>

        @forelse($page as $row)
            @foreach($row['lines'] as $n => $ln)
                <tr>
                    @if($n === 0)
                        <td width="3%" rowspan="3">{{ $row['no'] }}</td>
                        <td width="12%" rowspan="3">{{ $row['nama'] }}</td>
                        <td width="9%" rowspan="3">{{ $row['kode'] }}</td>
                        <td width="7%" rowspan="3">{{ $row['conveyor'] }}</td>
                    @endif

                    <td width="7%">{{ $ln['suhu'] }}</td>
                    <td width="8%">{{ $ln['bagian'] }}</td>
                    <td width="7%">{{ $ln['ab'] }}</td>
                    <td width="7%">{{ $ln['ak'] }}</td>
                    <td width="7%">{{ $ln['mb'] }}</td>
                    <td width="7%">{{ $ln['mk'] }}</td>
                    <td width="8%">{{ $ln['pcs'] }}</td>
                    <td width="8%">{{ $ln['toples'] }}</td>

                    @if($n === 0)
                        <td width="10%" rowspan="3">{{ $row['paraf'] }}</td>
                    @endif
                </tr>
            @endforeach
        @empty
            <tr>
                <td width="100%" colspan="13">Tidak ada data</td>
            </tr>
        @endforelse
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
                    <strong>Catatan :</strong><br>
                    {{ $catatan ?: '-' }}
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