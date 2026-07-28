<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: times;
            font-size: 9pt;
            margin: 0;
            padding: 0;
        }


        .title {
            font-size: 12pt;
            font-weight: bold;
            text-align: center;
        }

        table {
            border-collapse: collapse;
            margin: 0;
            padding: 0;
        }


        .tbl-header td {
            padding: 2px;
            font-size: 9pt;
        }

        .tbl-main,
        .tbl-main th,
        .tbl-main td {
            border: 0.3px solid #000;
        }

        .tbl-main th {
            font-size: 10pt;
            text-align: center;
            vertical-align: middle;
        }

        .center {
            text-align: center;
        }

        .small {
            font-size: 8pt;
        }

        .sign {
            text-align: center;
        }
    </style>
</head>

<body>

@php
    use Carbon\Carbon;

    $dateFilter = $date
        ? Carbon::parse($date)->format('d-m-Y')
        : 'All Dates';

    $susText = $plantName == 'Berbek' ? '2.5' : '2.0';

    $rows = [];

    foreach ($metals as $item) {
        $rows[(int) Carbon::parse($item->pukul)->format('H')] = $item;
    }

    $namaSpv = $metals->pluck('nama_spv')->filter()->unique()->first();
    $catatan = $metals->pluck('catatan')->filter()->unique()->implode(', ');
@endphp

<div style="margin:0;padding:0;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td width="55">
                <img src="{{ public_path('assets/img/Logo CPI.png') }}" width="50">
            </td>
            <td>
                <span style="font-size:14pt;"><b>PT Charoen </b></span><br>
                <span style="font-size:14pt;"><b>Pokphand Indonesia</b></span><br>
                <span style="font-size:14pt;"><b>Food Division</b></span>
            </td>
        </tr>
    </table>
</div>

<h2 class="title">PENGECEKAN METAL DETECTOR</h2>

<br><br>

<table width="100%" class="tbl-header">
    <tr>
        <td>Hari / Tanggal : {{ $dateFilter }}</td>
    </tr>
</table>

<br>

<table width="100%" class="tbl-main small">
    <tr>
        <th rowspan="2" class="center"><b>Pukul</b></th>
        <th rowspan="2" class="center"><b>FE 1.0 mm</b></th>
        <th rowspan="2" class="center"><b>NFE 1.5 mm</b></th>
        <th rowspan="2" class="center"><b>SUS {{ $susText }} mm</b></th>
        <th colspan="2" class="center"><b>Paraf</b></th>
    </tr>
    <tr>
        <th class="center"><b>QC</b></th>
        <th class="center"><b>Produksi</b></th>
    </tr>

    @for($i = 0; $i < 24; $i++)
        @php
            $record = $rows[$i] ?? null;
        @endphp

        <tr>
            <td class="center">{{ sprintf('%02d:00', $i) }}</td>
            <td class="center">{{ $record && $record->fe == 'Terdeteksi' ? 'V' : '' }}</td>
            <td class="center">{{ $record && $record->nfe == 'Terdeteksi' ? 'V' : '' }}</td>
            <td class="center">{{ $record && $record->sus == 'Terdeteksi' ? 'V' : '' }}</td>
            <td class="center">{{ $record->username ?? '' }}</td>
            <td class="center">{{ $record->nama_produksi ?? '' }}</td>
        </tr>
    @endfor

</table>

<div style="text-align:right;font-size:8px;font-style:italic">
    {{ $noDokumen }}
</div>

<br>

<table width="100%">
    <tr>
        <td width="55%" valign="top">
            <table width="100%" class="small">
                <tr>
                    <td>Keterangan : V terdeteksi</td>
                </tr>
                <tr>
                    <td>
                        Catatan :
                        @if($catatan)
                            <u>{{ $catatan }}</u>
                        @else
                            -
                        @endif
                    </td>
                </tr>
            </table>
        </td>

        <td width="45%">
            <table width="100%" class="sign small">
                <tr>
                    <td>Disetujui Oleh,</td>
                </tr>
                <tr>
                    <td><br><br><br></td>
                </tr>
                <tr>
                    <td>
                        (<u>{{ $namaSpv ?? '-' }}</u>)
                    </td>
                </tr>
                <tr>
                    <td>QC SPV</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

</body>
</html>
