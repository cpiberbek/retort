<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-size: 9px; }
        .title { font-size: 12px; font-weight: bold; text-align: center; }
        table { border-collapse: collapse; }

        .tbl-header td {
            font-size: 9px;
            padding: 2px 0;
        }

        .tbl-main, 
        .tbl-main th, 
        .tbl-main td {
            border: 0.3px solid #000;
        }

        .tbl-main th {
            text-align: center;
            vertical-align: middle;
            font-size: 8px;
        }

        .center { text-align: center; }
        .sign { text-align: center; }
        .small { font-size: 8px; }
    </style>
</head>
<body>

{{-- HEADER LOGO + TITLE --}}
<table width="100%">
    <tr>
        <td class="small" width="40%">
            PT Charoen Pokphand Indonesia<br>
            Food Division
        </td>
        
    </tr>
</table>
<h2 class="title">PEMERIKSAAN ORGANOLEPTIK</h2>
<br>
<br>

@php
    $dateFilter = request('date') ? \Carbon\Carbon::parse(request('date'))->format('d-m-Y') : 'All Dates';
    $shiftFilter = request('shift') ?? 'All Shifts';
    $namaProdukFilter = request('nama_produk') ?? 'All Products';
@endphp

<table width="100%" class="tbl-header">
    <tr>
        <td>Tanggal : {{ $dateFilter }}</td>
        <td>Shift : {{ $shiftFilter }}</td>
        <td>Nama Varian : {{ $namaProdukFilter }}</td>
    </tr>
</table>
<br>

{{-- TABLE --}}
<table width="100%" class="tbl-main" cellpadding="1">
    <tr>
        <th rowspan="2" class="center">Kode Batch</th>
        <th colspan="8" class="center">Sensori</th>
        <th rowspan="2" class="center">Hasil Score</th>
        <th rowspan="2" class="center">Release / Tidak Release</th>
        <th rowspan="2" class="center">Paraf QC</th>
    </tr>
    <tr>
        <th class="center">Penampilan</th>
        <th class="center">Aroma</th>
        <th class="center">Kekenyalan</th>
        <th class="center">Rasa Asin</th>
        <th class="center">Rasa Gurih</th>
        <th class="center">Rasa Manis</th>
        <th class="center">Rasa Ayam/BBQ/Ikan</th>
        <th class="center">Rasa Keseluruhan</th>
    </tr>

    @php
        $allSensori = [];
        foreach ($organoleptiks as $organoleptik) {
            $sensors = json_decode($organoleptik->sensori, true) ?? [];
            foreach ($sensors as $sensor) {
                $allSensori[] = [
                    'kode_produksi' => $sensor['kode_produksi'] ?? '',
                    'penampilan' => $sensor['penampilan'] ?? '',
                    'aroma' => $sensor['aroma'] ?? '',
                    'kekenyalan' => $sensor['kekenyalan'] ?? '',
                    'rasa_asin' => $sensor['rasa_asin'] ?? '',
                    'rasa_gurih' => $sensor['rasa_gurih'] ?? '',
                    'rasa_manis' => $sensor['rasa_manis'] ?? '',
                    'rasa_daging' => $sensor['rasa_daging'] ?? '',
                    'rasa_keseluruhan' => $sensor['rasa_keseluruhan'] ?? '',
                    'rata_score' => $sensor['rata_score'] ?? '',
                    'release' => $sensor['release'] ?? '',
                    'paraf' => $organoleptik->username ?? ''
                ];
            }
        }
    @endphp

    @forelse($allSensori as $sensor)
        <tr>
            <td class="center">{{ $sensor['kode_produksi'] }}</td>
            <td class="center">{{ $sensor['penampilan'] }}</td>
            <td class="center">{{ $sensor['aroma'] }}</td>
            <td class="center">{{ $sensor['kekenyalan'] }}</td>
            <td class="center">{{ $sensor['rasa_asin'] }}</td>
            <td class="center">{{ $sensor['rasa_gurih'] }}</td>
            <td class="center">{{ $sensor['rasa_manis'] }}</td>
            <td class="center">{{ $sensor['rasa_daging'] }}</td>
            <td class="center">{{ $sensor['rasa_keseluruhan'] }}</td>
            <td class="center">{{ $sensor['rata_score'] }}</td>
            <td class="center">{{ $sensor['release'] }}</td>
            <td class="center">{{ $sensor['paraf'] }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="12" class="center">Tidak ada data sensori</td>
        </tr>
    @endforelse


</table>
<div style="text-align:right; font-size:8px;font-style:italic;">QT 39 / 00</div>
<br>

{{-- Keterangan Parameter --}}
<table width="100%" class="small">
    <tr><td><strong>Keterangan Parameter:</strong></td></tr>
    <tr>
        <td width="25%">
            Penampilan, Aroma, Rasa Keseluruhan:<br>
            1 = Sangat Tidak<br>
            2 = Biasa<br>
            3 = Sangat
        </td>
        <td width="25%">
            Kekenyalan, Manis, Asin, Gurih, Ayam/BBQ/Ikan:<br>
            1 = Terlalu<br>
            2 = Kurang<br>
            3 = Pas
        </td>
        <td width="25%">
            Keterangan Hasil Score:<br>
            1 - 1,5 = Tidak Release<br>
            1,6 - 3 = Release
        </td>
        <td>
            {{-- SIGN --}}
            <table width="100%" class="sign">
                <tr>
                    <td>Disetujui Oleh,</td>
                </tr>
                <tr><td><br><br><br></td></tr>
                <tr>
                    <td>(___________________)<br>QC SPV</td>
                </tr>
            </table>
        </td>
    </tr>
</table>



</body>
</html>
