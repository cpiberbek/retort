<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-size: 8px; }
        table { border-collapse: collapse; }

        .title {
            font-size: 12px;
            font-weight: bold;
            text-align: center;
        }

        .small { font-size: 7px; }
        .center { text-align: center; }
        .right { text-align: right; }
        .sign { text-align: center; }

        .tbl-main, .tbl-main th, .tbl-main td {
            border: 0.3px solid #000;
        }

        .tbl-main th {
            background-color: #cfddee;
            text-align: center;
            vertical-align: middle;
            font-size: 7px;
        }

        .tbl-header td {
            padding: 2px;
            font-size: 8px;
        }
    </style>
</head>

<body>

{{-- HEADER --}}

<table width="100%">
    <tr>
        <td class="small" width="40%">
            PT Charoen Pokphand Indonesia<br>
            Food Division
        </td>
        
    </tr>
</table>
<h2 class="title">PENGECEKAN RETAIN SAMPEL</h2>
<br>
<br>

@php
$firstRetain = $retains->first();
$date = $firstRetain ? \Carbon\Carbon::parse($firstRetain->tanggal)->format('d-m-Y') : '';
@endphp
{{-- INFO --}}
<table width="100%" class="tbl-header">
    <tr>
        <td width="15%">Hari / Tanggal</td>
        <td width="85%">: {{ $date }}</td>
    </tr>
</table>

<br>

{{-- TABEL UTAMA --}}
<table width="100%" class="tbl-main small">
    <tr>
        <th rowspan="2" class="center">Kode Batch</th>
        <th rowspan="2" class="center">Varian</th>
        <th rowspan="2" class="center">Panjang<br>(cm)</th>
        <th rowspan="2" class="center">Diameter<br>(cm)</th>
        <th colspan="4" class="center">Sensori</th>
        <th colspan="5" class="center">Temuan</th>
        <th colspan="3" class="center">Parameter Lab</th>
        <th rowspan="2" class="center">Keterangan</th>
    </tr>

    <tr>
        <th class="center">Rasa</th>
        <th class="center">Warna</th>
        <th class="center">Aroma</th>
        <th class="center">Texture</th>

        <th class="center">Jamur</th>
        <th class="center">Lendir</th>
        <th class="center">Pinehole</th>
        <th class="center">Kejepit</th>
        <th class="center">Seal<br>halus / lepas</th>

        <th class="center">Kadar Garam</th>
        <th class="center">Kadar Air</th>
        <th class="center">Mikro</th>
    </tr>

    @php
    $allItems = [];
    foreach($retains as $retain) {
        if($retain->items && $retain->items->count() > 0) {
            foreach($retain->items as $item) {
                $allItems[] = $item;
            }
        }
    }
    @endphp

    @if(count($allItems) > 0)
        @foreach($allItems as $index => $item)
        <tr>
            <td>{{ $item->kode_produksi ?? '' }}</td>
            <td>{{ $item->varian ?? '' }}</td>
            <td>{{ $item->panjang ?? '' }}</td>
            <td>{{ $item->diameter ?? '' }}</td>
            <td>{{ $item->sensori_rasa ?? '' }}</td>
            <td>{{ $item->sensori_warna ?? '' }}</td>
            <td>{{ $item->sensori_aroma ?? '' }}</td>
            <td>{{ $item->sensori_texture ?? '' }}</td>
            <td>{{ $item->temuan_jamur ? 'V' : '' }}</td>
            <td>{{ $item->temuan_lendir ? 'V' : '' }}</td>
            <td>{{ $item->temuan_pinehole ? 'V' : '' }}</td>
            <td>{{ $item->temuan_kejepit ? 'V' : '' }}</td>
            <td>{{ $item->temuan_seal ? 'V' : '' }}</td>
            <td></td>
            <td>{{ $item->lab_garam ?? '' }}</td>
            <td>{{ $item->lab_air ?? '' }}</td>
            <td>{{ $item->lab_mikro ?? '' }}</td>
            <td></td>
        </tr>
        @endforeach
    @endif

</table>

<br><br>

<table width="100%" class="small">
    <tr>
        <td width="60%"></td>
        <td width="40%" class="sign">
            Mengetahui,<br><br><br>
            ( ___________________ )<br>
            SPV QC
        </td>
    </tr>
</table>

</body>
</html>
