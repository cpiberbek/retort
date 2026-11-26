<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verifikasi Timer Chamber</title>
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
            background: #d9d9d9;
            text-align: left;
            font-weight: bold;
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
        VERIFIKASI TIMER CHAMBER
    </h2>
    <br>
    <br>

    @foreach($page as $form)

        @php
            $n      = $form['n'];
            $w      = $form['w'];
            $lead   = $form['lead'];
            $lead2  = round($lead * 2, 2);
            $group  = round($w * 5, 2);
            $pair   = round($w * 2, 2);
            $valW   = round(100 - $lead2, 2);
            $span   = $n * 5;
        @endphp

        <table width="100%" class="tbl-main small" cellpadding="0" cellspacing="0">
            <tr>
                <td width="50%" class="sec">Hari / Tanggal : {{ $form['tanggal'] }}</td>
                <td width="50%" class="sec">Shift : {{ $form['shift'] }}</td>
            </tr>
        </table>

        <table width="100%" class="tbl-main small" cellpadding="0" cellspacing="0">
            <tr>
                <th colspan="2" width="{{ $lead2 }}%"><b>Nomer Chamber</b></th>
                @for($i = 1; $i <= $n; $i++)
                    <th colspan="5" width="{{ $group }}%"><b>Chamber {{ $i }}</b></th>
                @endfor
            </tr>
            <tr>
                <th colspan="2" width="{{ $lead2 }}%"><b>RENTANG UKUR</b></th>
                @for($i = 1; $i <= $n; $i++)
                    <th colspan="2" width="{{ $pair }}%"><b>PLC</b></th>
                    <th colspan="2" width="{{ $pair }}%"><b>STOPWATCH</b></th>
                    <th width="{{ $w }}%"><b>FAKTOR</b></th>
                @endfor
            </tr>
            <tr>
                <th width="{{ $lead }}%"><b>MENIT</b></th>
                <th width="{{ $lead }}%"><b>DETIK</b></th>
                @for($i = 1; $i <= $n; $i++)
                    <th width="{{ $w }}%"><b>MENIT</b></th>
                    <th width="{{ $w }}%"><b>DETIK</b></th>
                    <th width="{{ $w }}%"><b>MENIT</b></th>
                    <th width="{{ $w }}%"><b>DETIK</b></th>
                    <th width="{{ $w }}%"><b>KOREKSI</b></th>
                @endfor
            </tr>

            @foreach($form['rows'] as $row)
                <tr>
                    <td width="{{ $lead }}%">{{ $row['menit'] }}</td>
                    <td width="{{ $lead }}%">00</td>
                    @foreach($row['cells'] as $cell)
                        <td width="{{ $w }}%">{{ $cell }}</td>
                    @endforeach
                </tr>
            @endforeach

            <tr>
                <td colspan="2" width="{{ $lead2 }}%" class="sec">PARAF QC</td>
                <td colspan="{{ $span }}" width="{{ $valW }}%">{{ $form['parafQc'] }}</td>
            </tr>
            <tr>
                <td colspan="2" width="{{ $lead2 }}%" class="sec">PARAF PRODUKSI</td>
                <td colspan="{{ $span }}" width="{{ $valW }}%">{{ $form['parafProduksi'] }}</td>
            </tr>
        </table>

        <br>

    @endforeach

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
                        Produksi
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