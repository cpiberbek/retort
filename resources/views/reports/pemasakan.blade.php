<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pengecekan Pemasakan</title>
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

        .tbl-main td.sec {
            background: #f2f2f2;
            text-align: left;
        }

        .note td {
            vertical-align: top;
            line-height: 1.4;
        }
    </style>
</head>

<body>

@php
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
@endphp

@foreach($forms as $form)

    @php
        $cols = $form['cols'];
        $resW = $form['resW'];

        $namaPemeriksa = (!empty($form['pemeriksa']) && $form['pemeriksa'] !== '-') ? $form['pemeriksa'] : '';
        $namaSpv       = (!empty($form['spv']) && $form['spv'] !== '-') ? $form['spv'] : '';

        $qrPemeriksa = $makeQr($namaPemeriksa, $form['tglPemeriksa']);
        $qrSpv       = $makeQr($namaSpv, $form['tglSpv']);
    @endphp

    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0;">
        <tr>
            <td style="padding:0; line-height:1;">
                <span style="font-size:12pt;"><b>PT Charoen Pokphand Indonesia</b></span><br>
                <span style="font-size:12pt;"><b>Food Division</b></span>
            </td>
        </tr>
    </table>

    <h2 class="title" style="margin:0; padding:0;">
        PENGECEKAN PEMASAKAN
    </h2>
    <br>
    <br>

    <table width="100%" class="tbl-main small" cellpadding="0" cellspacing="0">
        <tr>
            <th width="5%"><b>NO</b></th>
            <th width="35%"><b>IDENTIFIKASI / PROSES</b></th>
            <th width="15%"><b>SATUAN</b></th>
            <th width="20%"><b>STANDAR</b></th>
            <th width="25%" colspan="{{ $cols }}"><b>HASIL</b></th>
        </tr>

        @foreach($form['sections'] as $section)
            <tr>
                <td width="5%"><b>{{ $section['no'] }}</b></td>
                <td width="95%" colspan="{{ 3 + $cols }}" class="sec"><b>{{ $section['title'] }}</b></td>
            </tr>

            @foreach($section['rows'] as $row)
                <tr>
                    <td width="5%"></td>
                    <td width="35%">{{ $row['label'] }}</td>
                    <td width="15%">{{ $row['unit'] }}</td>
                    <td width="20%">{{ $row['std'] }}</td>

                    @if(isset($row['values']))
                        @foreach($row['values'] as $value)
                            <td width="{{ $resW }}%">{{ $value }}</td>
                        @endforeach
                    @else
                        <td width="25%" colspan="{{ $cols }}">{{ $row['value'] }}</td>
                    @endif
                </tr>
            @endforeach
        @endforeach

        <tr>
            <td width="5%"><b>11</b></td>
            <td width="70%" colspan="3" class="sec"><b>TOTAL REJECT</b></td>
            <td width="25%" colspan="{{ $cols }}">{{ $form['reject'] }}</td>
        </tr>
    </table>

    <div style="margin-top:5px; text-align:right;">
        <i>{{ $noDokumen ?? '-' }}</i>
    </div>

    <br>
    <br>

    <table width="100%" class="note small" nobr="true">
        <tr>
            <td width="60%">
                <strong>Catatan :</strong><br>
                @if(!empty($form['catatan']))
                    {{ $form['catatan'] }}
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
                @endif
            </td>
        </tr>
    </table>

    @if(!$loop->last)
        <br pagebreak="true" />
    @endif

@endforeach

</body>
</html>