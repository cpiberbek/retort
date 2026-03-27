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
<h2 class="title">PEMERIKSAAN SUHU DAN RH</h2>
<br>
<br>

{{-- INFO --}}
@php
$firstItem = $items->first();
$date = $firstItem ? \Carbon\Carbon::parse($firstItem->date)->format('d-m-Y') : '';
$shift = $firstItem ? $firstItem->shift : '';
@endphp
<table width="100%" class="tbl-header">
    <tr>
        <td width="15%">Hari / Tanggal</td>
        <td width="35%">: {{ $date }}</td>
        <td width="10%">Shift</td>
        <td width="40%">: {{ $shift }}</td>
    </tr>
</table>

<br>

{{-- TABEL UTAMA --}}
<table width="100%" class="tbl-main small">
    <tr>
        <td rowspan="3" class="center">Pukul</td>
        <td colspan="18" class="center">Ruangan (oC)</td>
        <td rowspan="3" class="center">Keterangan</td>
        <td colspan="2" class="center">PARAF</td>
    </tr>
    <tr>
        <td colspan="2" class="center">Chill Room</td>
        <td colspan="2" class="center">Cold Storage Meat    </td>
        <td rowspan="2" class="center">Seasoning </td>
        <td rowspan="2" class="center">Meat Preparation</td>
        <td rowspan="2" class="center">Hopper</td>
        <td rowspan="2" class="center">Stuffer</td>
        <td rowspan="2" class="center">Susun</td>
        <td rowspan="2" class="center">Retort Chamber</td>
        <td rowspan="2" class="center">PVDC</td>
        <td colspan="2" class="center">Drying   </td>
        <td colspan="2" class="center">Packing  </td>
        <td rowspan="2" class="center">Dry Store</td>
        <td colspan="2" class="center">FG   </td>
        <td rowspan="2" class="center">QC</td>
        <td rowspan="2" class="center">PROD.</td>
    </tr>
    <tr>
        <td class="center">Ruang</td>
        <td class="center">Meat</td>
        <td class="center">Ruang</td>
        <td class="center">Meat</td>
        <td class="center">Suhu</td>
        <td class="center">RH</td>
        <td class="center">Suhu</td>
        <td class="center">RH</td>
        <td class="center">Suhu</td>
        <td class="center">RH</td>
    </tr>
    <tr>
        <th class="center">STD (°C)</th>
        <th class="center">0 - 4</th>
        <th class="center"> < 10 </th>
        <th class="center">-18 sd. -22</th>
        <th class="center">-18 sd. -22</th>
        <th class="center">25 - 30</th>
        <th class="center">9 - 15</th>
        <th class="center">8 - 12</th>
        <th class="center">16 - 20</th>
        <th class="center">12 - 18</th>
        <th class="center">30 - 40</th>
        <th class="center">27 - 33</th>
        <th class="center">22 - 50</th>
        <th class="center">20 - 60</th>
        <th class="center">20 - 30</th>
        <th class="center">40 - 70</th>
        <th class="center">25 - 36</th>
        <th class="center">28 - 36</th>
        <th class="center">35 - 70</th>
        <th class="center"></th>
        <th class="center"></th>
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
        <td class="center">{{ $suhuByArea['Chillroom (Ruang)']['nilai'] ?? '' }}</td>
        <td class="center">{{ $suhuByArea['Chillroom (Meat)']['nilai'] ?? '' }}</td>
        <td class="center">{{ $suhuByArea['Cold Storage Meat (Ruang)']['nilai'] ?? '' }}</td>
        <td class="center">{{ $suhuByArea['Cold Storage Meat (Meat)']['nilai'] ?? '' }}</td>
        <td class="center">{{ $suhuByArea['Seasoning']['nilai'] ?? '' }}</td>
        <td class="center">{{ $suhuByArea['Meat Preparation']['nilai'] ?? '' }}</td>
        <td class="center">{{ $suhuByArea['Hopper']['nilai'] ?? '' }}</td>
        <td class="center">{{ $suhuByArea['Stuffer']['nilai'] ?? '' }}</td>
        <td class="center">{{ $suhuByArea['Susun']['nilai'] ?? '' }}</td>
        <td class="center">{{ $suhuByArea['Retort Chamber']['nilai'] ?? '' }}</td>
        <td class="center">{{ $suhuByArea['PVDC']['nilai'] ?? '' }}</td>
        <td class="center">{{ $suhuByArea['Drying (Suhu)']['nilai'] ?? '' }}</td>
        <td class="center">{{ $suhuByArea['Drying (RH)']['nilai'] ?? '' }}</td>
        <td class="center">{{ $suhuByArea['Packing (Suhu)']['nilai'] ?? '' }}</td>
        <td class="center">{{ $suhuByArea['Packing (RH)']['nilai'] ?? '' }}</td>
        <td class="center">{{ $suhuByArea['Dry Store']['nilai'] ?? '' }}</td>
        <td class="center">{{ $suhuByArea['FG (Suhu)']['nilai'] ?? '' }}</td>
        <td class="center">{{ $suhuByArea['FG (RH)']['nilai'] ?? '' }}</td>
        <td class="center">{{ $item ? $item->keterangan : '' }}</td>
        <td></td>
        <td></td>
    </tr>
    @endfor
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
            Diperiksa Oleh : ________________________
        </td>
        <td width="50%" style="text-align:right;">
            Disetujui Oleh : ________________________
        </td>
    </tr>
</table>

<div style="text-align:right; font-size:7px;">QT 25 / 01</div>

</body>
</html>