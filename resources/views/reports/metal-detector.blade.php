<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
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

    $pages = collect(range(0, 23))->chunk(24);
@endphp

<div style="margin-left:-30px; margin-bottom:0;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0;">
        <tr>
            {{-- <td width="55" style="padding:0;">
                <img src="{{ public_path('assets/img/Logo CPI.png') }}" width="50">
            </td> --}}
            <td style="padding:0; line-height:1;">
                <span style="font-size:14pt;"><b>PT Charoen Pokphand Indonesia</b></span><br>
                <span style="font-size:14pt;"><b>Food Division</b></span>
            </td>
        </tr>
    </table>
</div>

<h2 class="title" style="margin:0; padding:0;">
    PENGECEKAN METAL DETECTOR
</h2>
<br>
<br>

@foreach($pages as $pageIndex => $hours)

    <table width="100%" class="tbl-main small" cellpadding="0" cellspacing="0">
        <tr>
            <th rowspan="3" width="12%"><b>Pukul</b></th>
            <th colspan="5" width="88%"><b>Hari / Tanggal : {{ $dateFilter }}</b></th>
        </tr>
        <tr>
            <th rowspan="2" width="16%"><b>FE 1.0 mm</b></th>
            <th rowspan="2" width="16%"><b>NFE 1.5 mm</b></th>
            <th rowspan="2" width="16%"><b>SUS {{ $susText }} mm</b></th>
            <th colspan="2" width="40%"><b>TTD</b></th>
        </tr>
        <tr>
            <th width="20%"><b>QC</b></th>
            <th width="20%"><b>Produksi</b></th>
        </tr>

        @foreach($hours as $i)
            @php
                $record = $rows[$i] ?? null;
            @endphp
            <tr>
                <td width="12%" class="center">{{ sprintf('%02d:00', $i) }}</td>
                <td width="16%" class="center">{{ $record && $record->fe == 'Terdeteksi' ? 'V' : '' }}</td>
                <td width="16%" class="center">{{ $record && $record->nfe == 'Terdeteksi' ? 'V' : '' }}</td>
                <td width="16%" class="center">{{ $record && $record->sus == 'Terdeteksi' ? 'V' : '' }}</td>
                <td width="20%" class="center">{{ $record ? (\App\Models\User::where('username', $record->username)->value('name') ?? $record->username) : '' }}</td>
                <td width="20%" class="center">{{ $record->nama_produksi ?? '' }}</td>
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
                    Keterangan : V terdeteksi<br>
                    Catatan :
                    @if(!empty($catatan))
                        <u>{{ $catatan }}</u>
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

    @else
        <br pagebreak="true" />
    @endif

@endforeach

</body>
</html>