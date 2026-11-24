<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 8px;
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

        .subtitle {
            font-size: 9px;
            text-align: center;
        }

        .tbl-header td {
            padding: 0;
            font-size: 8px;
            line-height: 1;
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

    $tanggalLaporan = $request->date
        ? \Carbon\Carbon::parse($request->date)->format('d-m-Y')
        : 'SEMUA';

    $bd  = 'border:0.8px solid #000;';
    $hd  = 'border:0.8px solid #000; background-color:#d9e2f3;';
    $nb  = 'border:none;';
@endphp

@foreach($pages as $pageGrids)

    <div style="margin-left:-30px; margin-bottom:0;">
        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0;">
            <tr>
                <td width="55" style="padding:0;">
                    <img src="{{ public_path('assets/img/Logo CPI.png') }}" width="50">
                </td>
                <td style="padding:0; line-height:1;">
                    <span style="font-size:14pt;"><b>PT Charoen</b></span><br>
                    <span style="font-size:14pt;"><b>Pokphand Indonesia</b></span><br>
                    <span style="font-size:14pt;"><b>Food Division</b></span>
                </td>
            </tr>
        </table>
    </div>

    <h2 class="title" style="margin:0; padding:0;">
        DATA LOT NO. WIRE
    </h2>
    <div class="subtitle">
        Supplier Wire : {{ $supplier }}
    </div>
    <br>
    <br>

    <table width="100%" class="tbl-header">
        <tr>
            <td width="100%">Hari / Tanggal : {{ $tanggalLaporan }}</td>
        </tr>
    </table>

    <br>

    <table width="100%" cellpadding="3" cellspacing="0">

        @forelse(array_chunk($pageGrids, 3) as $row)

            <tr>
                @for($i = 0; $i < 3; $i++)
                    @php $grid = $row[$i] ?? null; @endphp
                    @if($grid)
                        <td width="15.5%" align="center" style="{{ $hd }}"><b>MESIN : {{ $grid['mesin'] }}</b></td>
                        <td width="15.5%" align="center" style="{{ $hd }}"><b>Start : {{ $grid['start'] }}</b></td>
                    @else
                        <td width="15.5%" style="{{ $nb }}"></td>
                        <td width="15.5%" style="{{ $nb }}"></td>
                    @endif
                    @if($i < 2)
                        <td width="3.5%" style="{{ $nb }}"></td>
                    @endif
                @endfor
            </tr>

            <tr>
                @for($i = 0; $i < 3; $i++)
                    @php $grid = $row[$i] ?? null; @endphp
                    @if($grid)
                        <td width="15.5%" align="center" style="{{ $hd }}"><b>End</b></td>
                        <td width="15.5%" align="center" style="{{ $hd }}"><b>No. Lot</b></td>
                    @else
                        <td width="15.5%" style="{{ $nb }}"></td>
                        <td width="15.5%" style="{{ $nb }}"></td>
                    @endif
                    @if($i < 2)
                        <td width="3.5%" style="{{ $nb }}"></td>
                    @endif
                @endfor
            </tr>

            <tr>
                @for($i = 0; $i < 3; $i++)
                    @php $grid = $row[$i] ?? null; @endphp
                    @if($grid)
                        <td width="15.5%" align="center" style="{{ $bd }}">{{ $grid['end'] }}</td>
                        <td width="15.5%" align="center" style="{{ $bd }}"><b>{{ $grid['no_lot'] }}</b></td>
                    @else
                        <td width="15.5%" style="{{ $nb }}"></td>
                        <td width="15.5%" style="{{ $nb }}"></td>
                    @endif
                    @if($i < 2)
                        <td width="3.5%" style="{{ $nb }}"></td>
                    @endif
                @endfor
            </tr>

            <tr>
                <td colspan="8" style="{{ $nb }} font-size:4px;">&nbsp;</td>
            </tr>

        @empty
            <tr>
                <td colspan="8" align="center" style="{{ $bd }}">Tidak ada data</td>
            </tr>
        @endforelse

    </table>

    <div style="margin-top:5px; text-align:right;">
        <i>{{ $noDokumen ?? '-' }}</i>
    </div>

    @if($loop->last)

        <br>
        <br>

        <table width="100%" class="note" nobr="true">
            <tr>
                <td width="60%"></td>

                <td width="20%" align="center" style="vertical-align: top;">
                    @if($namaPemeriksa)
                        Dibuat oleh :<br>
                        <u>{{ $namaPemeriksa }}</u><br>
                        <br>
                        <img src="data:image/png;base64,{{ $qrPemeriksa }}" width="60" height="60"><br>
                        QC
                    @endif
                </td>

                <td width="20%" align="center" style="vertical-align: top;">
                    @if($namaSpv)
                        Diverifikasi oleh :<br>
                        <u>{{ $namaSpv }}</u><br>
                        <br>
                        <img src="data:image/png;base64,{{ $qrSpv }}" width="60" height="60"><br>
                        QC SPV
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