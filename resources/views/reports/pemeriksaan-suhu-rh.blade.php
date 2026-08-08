<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-size: 7px; }
        table { border-collapse: collapse; }

        .title {
            font-size: 11px;
            font-weight: bold;
            text-align: center;
        }

        .small { font-size: 7px; }
        .center { text-align: center; }
        .sign { text-align: center; }

        .tbl-main, .tbl-main th, .tbl-main td {
            border: 0.3px solid #000;
        }

        .tbl-main th {
            font-size: 7px;
            text-align: center;
            vertical-align: middle;
        }

        .tbl-header td {
            padding: 2px;
            font-size: 8px;
        }
         /* tnr */
        body,
        table,
        tr,
        td,
        th {
            font-family: times;
            font-size: 6pt;
        }
         /* tnr */
        body,
        table,
        tr,
        td,
        th {
            font-family: times;
            font-size: 8pt;
        }
    </style>
</head>

<body>

{{-- HEADER --}}

<div style="margin-left:-30px;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td width="55">
                <img src="{{ public_path('assets/img/Logo CPI.png') }}" width="50">
            </td>
            <td>
                <span style="font-size:12pt;"><b>PT Charoen </b></span><br>
                <span style="font-size:12pt;"><b>Pokphand Indonesia</b></span><br>
                <span style="font-size:12pt;"><b>Food Division</b></span>
            </td>
        </tr>
    </table>
</div>

<h2 class="title">PEMERIKSAAN SUHU DAN RH</h2>
<br>
<br>

{{-- INFO --}}
@php
$firstItem = $items->first();
$date = $firstItem ? \Carbon\Carbon::parse($firstItem->date)->format('d-m-Y') : '';
$shifts = $items->pluck('shift')->filter()->unique()->sort()->values()->all();
$shift = count($shifts) === 3 && $shifts === ['1', '2', '3']
    ? 'Semua Shift'
    : implode(',', $shifts);
@endphp
<table width="100%" class="tbl-header">
    <tr>
        <td width="15%">Hari / Tanggal: {{ $date }}</td>
        <td width="35%"></td>
        <td width="10%">Shift: {{ $shift }}</td>
        <td width="40%"></td>
    </tr>
</table>

<br>

{{-- TABEL UTAMA --}}
<table width="100%" class="tbl-main small">
    <tr>
        <td rowspan="3" class="center"><strong>Pukul</strong></td>
        <td colspan="18" class="center"><strong>Ruangan (oC)</strong></td>
        <td rowspan="3" class="center"><strong>Keterangan</strong></td>
        <td colspan="2" class="center"><strong>PARAF</strong></td>
    </tr>
    <tr>
        <td colspan="2" class="center"><strong>Chill Room</strong></td>
        <td colspan="2" class="center"><strong>Cold Storage Meat</strong></td>
        <td rowspan="2" class="center"><strong>Seasoning</strong></td>
        <td rowspan="2" class="center"><strong>Meat Preparation</strong></td>
        <td rowspan="2" class="center"><strong>Hopper</strong></td>
        <td rowspan="2" class="center"><strong>Stuffer</strong></td>
        <td rowspan="2" class="center"><strong>Susun</strong></td>
        <td rowspan="2" class="center"><strong>Retort Chamber</strong></td>
        <td rowspan="2" class="center"><strong>PVDC</strong></td>
        <td colspan="2" class="center"><strong>Drying</strong></td>
        <td colspan="2" class="center"><strong>Packing</strong></td>
        <td rowspan="2" class="center"><strong>Dry Store</strong></td>
        <td colspan="2" class="center"><strong>FG</strong></td>
        <td rowspan="2" class="center"><strong>QC</strong></td>
        <td rowspan="2" class="center"><strong>PROD.</strong></td>
    </tr>
    <tr>
        <td class="center"><strong>Ruang</strong></td>
        <td class="center"><strong>Meat</strong></td>
        <td class="center"><strong>Ruang</strong></td>
        <td class="center"><strong>Meat</strong></td>
        <td class="center"><strong>Suhu</strong></td>
        <td class="center"><strong>RH</strong></td>
        <td class="center"><strong>Suhu</strong></td>
        <td class="center"><strong>RH</strong></td>
        <td class="center"><strong>Suhu</strong></td>
        <td class="center"><strong>RH</strong></td>
    </tr>
    <tr>
        <th class="center"><strong>STD (°C)</strong></th>
        <th class="center"><strong>0 - 4</strong></th>
        <th class="center"><strong>&lt; 10</strong></th>
        <th class="center"><strong>-18 sd. -22</strong></th>
        <th class="center"><strong>-18 sd. -22</strong></th>
        <th class="center"><strong>25 - 30</strong></th>
        <th class="center"><strong>9 - 15</strong></th>
        <th class="center"><strong>8 - 12</strong></th>
        <th class="center"><strong>16 - 20</strong></th>
        <th class="center"><strong>12 - 18</strong></th>
        <th class="center"><strong>30 - 40</strong></th>
        <th class="center"><strong>27 - 33</strong></th>
        <th class="center"><strong>22 - 50</strong></th>
        <th class="center"><strong>20 - 60</strong></th>
        <th class="center"><strong>20 - 30</strong></th>
        <th class="center"><strong>40 - 70</strong></th>
        <th class="center"><strong>25 - 36</strong></th>
        <th class="center"><strong>28 - 36</strong></th>
        <th class="center"><strong>35 - 70</strong></th>
        <th class="center"><strong></strong></th>
        <th class="center"><strong></strong></th>
    </tr>

    @php
    // Group items by pukul hour
    $dataByHour = [];
    foreach($items as $item) {
        $hour = \Carbon\Carbon::parse($item->pukul)->format('H');
        $dataByHour[$hour] = $item;
    }
    // ARRAY $areaMapping SUDAH DIHAPUS KARENA TIDAK PERLU
    @endphp

    @for($i=0; $i<=23; $i++)
    @php
    $hourStr = str_pad($i, 2, '0', STR_PAD_LEFT);
    $item = $dataByHour[$hourStr] ?? null;
    
    // Pastikan decode JSON berjalan dengan baik (karena di database tipenya text/json)
    $hasilSuhu = [];
    if ($item && !empty($item->hasil_suhu)) {
        $hasilSuhu = is_string($item->hasil_suhu) ? json_decode($item->hasil_suhu, true) : $item->hasil_suhu;
    }
    
    // Jadikan array dengan key berdasarkan NAMA AREA PERSIS DI DATABASE
    $suhuByArea = collect($hasilSuhu)->keyBy('area');
    @endphp
    <tr>
        <td class="center">{{ $hourStr }}:00</td>
        
        {{-- PASTIKAN STRING DI DALAM KURUNG SIKU SAMA PERSIS DENGAN DATABASE --}}
        <td class="center">{{ $suhuByArea['Chill Room (Ruang)']['suhu'] ?? '' }}</td>
        <td class="center">{{ $suhuByArea['Chill Room (Meat)']['suhu'] ?? '' }}</td>
        <td class="center">{{ $suhuByArea['Cold Storage (Ruang)']['suhu'] ?? '' }}</td>
        <td class="center">{{ $suhuByArea['Cold Storage (Meat)']['suhu'] ?? '' }}</td>
        <td class="center">{{ $suhuByArea['Seasoning']['suhu'] ?? '' }}</td>
        <td class="center">{{ $suhuByArea['Meat Preparation']['suhu'] ?? '' }}</td>
        <td class="center">{{ $suhuByArea['Hopper']['suhu'] ?? '' }}</td>
        <td class="center">{{ $suhuByArea['Stuffer']['suhu'] ?? '' }}</td>
        <td class="center">{{ $suhuByArea['Susun']['suhu'] ?? '' }}</td>
        <td class="center">{{ $suhuByArea['Retort Chamber']['suhu'] ?? '' }}</td>
        <td class="center">{{ $suhuByArea['PVDC']['suhu'] ?? '' }}</td>
        <td class="center">{{ $suhuByArea['Drying']['suhu'] ?? '' }}</td>
        <td class="center">{{ $suhuByArea['Drying']['rh'] ?? '' }}</td>
        <td class="center">{{ $suhuByArea['Packing']['suhu'] ?? '' }}</td>
        <td class="center">{{ $suhuByArea['Packing']['rh'] ?? '' }}</td>
        <td class="center">{{ $suhuByArea['Dry Store']['suhu'] ?? '' }}</td>
        <td class="center">{{ $suhuByArea['Finish Good']['suhu'] ?? '' }}</td>
        <td class="center">{{ $suhuByArea['Finish Good']['rh'] ?? '' }}</td>
        <td class="center">{{ $item ? $item->keterangan : '' }}</td>
        <td class="center">{{ $item ? $item->nama_spv : '' }}</td>
        <td class="center">{{ $item ? $item->username : '' }}</td>
    </tr>
    @endfor
</table>
@php
    $noDokumen = \App\Models\List_form::where('plant', $items->first()->plant ?? null)
        ->where('laporan', 'Pemeriksaan Suhu dan RH Ruangan')
        ->value('no_dokumen');
@endphp

<table width="100%" style="font-size:7px;">
    <tr>
        <td width="80%"></td>
        <td width="20%" style="text-align:right;">
            {{ $noDokumen ?? '' }}
        </td>
    </tr>
</table>

<br>

<table width="100%" class="small">
    <tr>
        <td>Catatan :</td>
    </tr>
    <tr>
        <td style="height:40px;"></td>
    </tr>
</table>

<br><br>

<table width="100%" class="small">
    <tr>
        <td width="50%">
            <strong>Diperiksa Oleh :</strong><br>
            <u>({{ $items->first()->username ?? '' }})</u>
        </td>
        <td width="50%" style="text-align:right;">
            <strong>Disetujui Oleh :</strong><br>
            <u>
                @if($items->every(fn($item) => !is_null($item->nama_spv)))
                    ({{ $items->first()->nama_spv }})
                @else
                    Belum Semua Entry Disetujui Oleh SPV
                @endif
            </u>
        </td>
    </tr>
</table>



</body>
</html>