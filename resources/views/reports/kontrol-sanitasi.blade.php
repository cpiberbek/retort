<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-size: 8px; }
        table { border-collapse: collapse; }

        .title { font-size: 12px; font-weight: bold; text-align: center; }
        .small { font-size: 7px; }
        .center { text-align: center; }
        .sign { text-align: center; }

        .tbl-main, .tbl-main th, .tbl-main td {
            border: 0.3px solid #000;
        }

        .tbl-main th {
            text-align: center;
            vertical-align: middle;
            font-size: 7px;
            background-color: #ccffcc;
        }

        .tbl-header td {
            padding: 2px;
            font-size: 8px;
        }

        .section {
            font-weight: bold;
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>

{{-- HEADER --}}
{{-- HEADER LOGO + TITLE --}}
<table width="100%">
    <tr>
        <td class="small" width="40%">
            PT Charoen Pokphand Indonesia<br>
            Food Division
        </td>
        
    </tr>
</table>
<h2 class="title">KONTROL SANITASI</h2>
<br>
<br>
<table width="100%">
    <tr>
        <td width="50%" class="right small">
            @if($request->date)
                Hari / Tgl : <strong>{{ \Carbon\Carbon::parse($request->date)->format('d-m-Y') }}</strong>
            @else
                Hari / Tgl : ________________________
            @endif
        </td>
        <td width="50%" class="right small">
            @if($request->shift)
                <br>Shift : <strong>{{ $request->shift }}</strong>
            @endif
        </td>
    </tr>
</table>

<br>

{{-- TABEL UTAMA --}}
<table width="100%" class="tbl-main small">
    <tr>
        <th class="center" rowspan="2">LOKASI</th>
        <th class="center" rowspan="2">Waktu<br>Periksa<br>(pukul)</th>
        <th colspan="2" class="center">KONDISI AREA</th>
        <th colspan="2" class="center">TINDAKAN KOREKSI</th>
        <th class="center" rowspan="2">Dikerjakan<br>Oleh</th>
        <th class="center" rowspan="2">Verifikasi Koreksi<br>(waktu)</th>
        <th class="center" rowspan="2">Diverifikasi<br>Oleh</th>
    </tr>
    <tr>
        <th class="center">Kondisi*</th>
        <th class="center">Keterangan</th>
        <th class="center">Rencana Tindakan</th>
        <th class="center">Waktu</th>
    </tr>

    {{-- DATA FROM DATABASE --}}
    @foreach($sanitasies as $index => $sanitasi)
        @php
            $pemeriksaan = json_decode($sanitasi->pemeriksaan, true) ?? [];
            $kondisiMapping = [
                '✔' => 'OK (bersih)',
                '1' => 'Basah',
                '2' => 'Berdebu',
                '3' => 'Kerak',
                '4' => 'Noda',
                '5' => 'Karat',
                '6' => 'Sampah',
                '7' => 'Retak/Pecah',
                '8' => 'Sisa Varian',
                '9' => 'Sisa Adonan',
                '10' => 'Berjamur',
                '11' => 'Lain-lain'
            ];
        @endphp

        {{-- AREA HEADER --}}
        <tr class="section">
            <td colspan="9">{{ $index + 1 }}. AREA: {{ $sanitasi->area }} </td>
        </tr>

        {{-- AREA ITEMS --}}
        @foreach($pemeriksaan as $bagian => $item)
        <tr>
            <td>• {{ $bagian }}</td>
            <td class="center">{{ $item['waktu'] ?? '-' }}</td>
            <td class="center">{{ $kondisiMapping[$item['kondisi']] ?? '-' }}</td>
            <td class="center">{{ $item['keterangan'] ?? '-' }}</td>
            <td class="center">{{ $item['tindakan'] ?? '-' }}</td>
            <td class="center">{{ $item['waktu_koreksi'] ?? '-' }}</td>
            <td class="center">{{ $item['dikerjakan_oleh'] ?? '-' }}</td>
            <td class="center">{{ $item['waktu_verifikasi'] ?? '-' }}</td>
            <td class="center">{{ $sanitasi->username ?? '-' }}</td>
        </tr>
        @endforeach

        {{-- EMPTY ROW IF NO INSPECTION DATA --}}
        @if(empty($pemeriksaan))
        <tr>
            <td colspan="9" class="center">Tidak ada data pemeriksaan</td>
        </tr>
        @endif
    @endforeach

   
</table>

<br>
<br>
{{-- KETERANGAN --}}
<table width="100%" class="small">
    <tr>
        <td width="50%">
            <strong>* Kondisi :</strong><br>
            0 : OK (bersih)<br>
            1 : Basah<br>
            2 : Berdebu<br>
            3 : Kerak<br>
            4 : Noda<br>
            5 : Karat
        </td>
        <td width="50%">
            6 : Sampah<br>
            7 : Retak / Pecah<br>
            8 : Sisa Varian<br>
            9 : Sisa Adonan<br>
            10 : Berjamur<br>
            11 : Lain-lain
        </td>
    </tr>
</table>

<br><br>

{{-- TTD --}}
<table width="100%" class="small">
    <tr>
        <td width="50%" class="sign">
            Dibuat Oleh,<br><br><br>
            ( ___________________ )<br>
            QC Sanitasi
        </td>
        <td width="50%" class="sign">
            Disetujui Oleh,<br><br><br>
            ( ___________________ )<br>
            Spv. QC
        </td>
    </tr>
</table>

</body>
</html>
