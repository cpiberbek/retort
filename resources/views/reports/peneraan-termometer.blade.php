<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-size: 9px; }
        table { border-collapse: collapse; }

        .title {
            font-size: 13px;
            font-weight: bold;
            text-align: center;
        }

        .small { font-size: 8px; }
        .center { text-align: center; }
        .sign { text-align: center; }

        .tbl-main, .tbl-main th, .tbl-main td {
            border: 0.4px solid #000;
        }

        .tbl-main th {
            font-size: 8px;
            text-align: center;
            vertical-align: middle;
        }

        .tbl-header td {
            padding: 2px;
            font-size: 9px;
        }
        /* tnr */
        body,
        table,
        tr,
        td,
        th {
            font-family: times;
            font-size: 9pt;
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
<h2 class="title">PENERAAN TERMOMETER</h2>


{{-- INFO --}}
@php
$firstItem = $items->first();
$date = $firstItem ? \Carbon\Carbon::parse($firstItem->date)->format('d-m-Y') : '';
$shift = $firstItem ? $firstItem->shift : '';
@endphp
<table width="100%" class="tbl-header">
    <tr>
        <td>Hari / Tanggal : {{ $date }}</td>
        {{-- <td>Shift : {{ $shift }}</td> --}}
    </tr>
</table>

<br>

{{-- TABEL UTAMA --}}
<table width="100%" class="tbl-main small">
    <tr>
        <th class="center" rowspan="2"><strong>KODE TERMOMETER / AREA</strong></th>
        <th class="center" rowspan="2"><strong>STANDAR</strong></th>
        <th colspan="2" class="center"><strong>PENERAAN</strong></th>
        <th class="center" rowspan="2"><strong>TINDAKAN PERBAIKAN</strong></th>
        <th class="center" rowspan="2"><strong>DIBUAT<br>QC</strong></th>
        <th class="center" rowspan="2"><strong>DIKETAHUI<br>SPV QC</strong></th>
    </tr>
    <tr>
        <th class="center"><strong>PUKUL</strong></th>
        <th class="center"><strong>HASIL TERA</strong></th>
    </tr>

    @php
    $allPeneraan = [];

    foreach($items as $item) {
        $peneraan = $item->peneraan;

        if(is_array($peneraan)) {
            foreach($peneraan as $data) {
                $allPeneraan[] = [
                    'peneraan' => $data,
                    'username' => $item->username,
                    'nama_spv' => $item->nama_spv,
                ];
            }
        }
    }
    @endphp

    @foreach($allPeneraan as $itemPeneraan)
    <tr>
        <td style="height:35px;">
            {{ $itemPeneraan['peneraan']['kode_thermometer'] ?? '' }} /<br>
            {{ $itemPeneraan['peneraan']['area'] ?? '' }}
        </td>
        <td class="center">{{ $itemPeneraan['peneraan']['standar'] ?? '' }}</td>
        <td>{{ $itemPeneraan['peneraan']['pukul'] ?? '' }}</td>
        <td>{{ $itemPeneraan['peneraan']['hasil_tera'] ?? '' }}</td>
        <td>{{ $itemPeneraan['peneraan']['tindakan_perbaikan'] ?? '' }}</td>
        <td>{{ $itemPeneraan['username'] ?? '' }}</td>
        <td>{{ $itemPeneraan['nama_spv'] ?? '-' }}</td>
    </tr>
    @endforeach

    @if(count($allPeneraan) < 8)
    @for($i = count($allPeneraan); $i < 8; $i++)
    <tr>
        <td style="height:35px;"></td>
        <td class="center"></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    @endfor
    @endif
</table>

@php
    $noDokumen = \App\Models\List_form::where('plant', $items->first()->plant ?? null)
        ->where('laporan', 'Verifikasi Termometer')
        ->value('no_dokumen');
@endphp

<table width="100%" style="font-size:7px;">
    <tr>
        <td width="80%"></td>
        <td width="20%" style="text-align:right;">
            <i>{{ $noDokumen ?? '' }}</i>
        </td>
    </tr>
</table>


{{-- KETERANGAN --}}
<table width="100%" class="small">
    <tr>
        <td>
            <strong>Keterangan :</strong><br>
            - Tera termometer dilakukan di setiap awal shift<br>
            - Termometer ditera dengan memasukkan sensor (probe) ke es (0 °C)<br>
            - Jika ada selisih angka display suhu dengan suhu standar,
              beri keterangan (+) atau (–) angka selisih (faktor koreksi)<br>
            - Jika faktor koreksi &lt; 0,5 °C, termometer perlu perbaikan
        </td>
    </tr>
</table>



</body>
</html>
