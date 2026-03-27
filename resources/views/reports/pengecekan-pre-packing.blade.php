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

{{-- HEADER LOGO + TITLE --}}
<table width="100%">
    <tr>
        <td class="small" width="40%">
            PT Charoen Pokphand Indonesia<br>
            Food Division
        </td>
        
    </tr>
</table>
<h2 class="title">PENGECEKAN PRE PACKING</h2>
<br>
<br>

@php
    $dateFilter = request('date') ? \Carbon\Carbon::parse(request('date'))->format('d-m-Y') : 'All Dates';
@endphp

<table width="100%" class="tbl-header">
    <tr>
        <td width="15%">Hari / Tanggal</td>
        <td width="85%">: {{ $dateFilter }}</td>
    </tr>
</table>

<br>
<br>

{{-- TABEL UTAMA --}}
<table width="100%" class="tbl-main small">
    <tr>
        <th rowspan="2" class="center" style="width: 20px;">No</th>
        <th colspan="2" class="center">Varian</th>
        <th rowspan="2" class="center">No.<br>Conveyor</th>
        <th rowspan="2" class="center">Suhu<br>Varian</th>
        <th rowspan="2" class="center" style="width: 40px;">Bagian Badan Sosis</th>
        <th colspan="2" class="center">Air (%)</th>
        <th colspan="2" class="center">Minyak (%)</th>
        <th colspan="2" class="center">Berat Varian per</th>
        <th rowspan="2" class="center">PARAF<br>QC</th>
    </tr>
    <tr>
        <th class="center">Nama</th>
        <th class="center">Kode</th>
        <th class="center">Basah</th>
        <th class="center">Kering</th>
        <th class="center">Basah</th>
        <th class="center">Kering</th>
        <th class="center">pcs</th>
        <th class="center">Toples<br>(berat kotor)</th>
    </tr>

    @forelse($prepackings as $index => $prepacking)
        {{-- UJUNG --}}
        <tr>
            <td rowspan="3" class="center">{{ $index + 1 }}</td>
            <td rowspan="3" class="center">{{ $prepacking->nama_produk ?? '-' }}</td>
            <td rowspan="3" class="center">{{ $prepacking->kode_produksi ?? '-' }}</td>
            <td rowspan="3" class="center">{{ $prepacking->conveyor ?? '-' }}</td>
            <td rowspan="3" class="center">{{ $prepacking->suhu_produk ? implode(' | ', json_decode($prepacking->suhu_produk, true)) : '-' }}</td>
            <td>Ujung</td>
            @php
                $kondisi = json_decode($prepacking->kondisi_produk, true) ?? [];
                $airBasahUjung = ($kondisi['basah_air_ujung'] ?? 0);
                $airKeringUjung = ($kondisi['kering_air_ujung'] ?? 0);
                $minyakBasahUjung = ($kondisi['basah_minyak_ujung'] ?? 0);
                $minyakKeringUjung = ($kondisi['kering_minyak_ujung'] ?? 0);
                $berat = json_decode($prepacking->berat_produk, true) ?? [];
                $pcsUjung = implode(' | ', [
                    $berat['pcs_1'] ?? 0,
                    $berat['pcs_2'] ?? 0,
                    $berat['pcs_3'] ?? 0,
                ]);
                $toplesUjung = implode(' | ', [
                    $berat['toples_1'] ?? 0,
                    $berat['toples_2'] ?? 0,
                    $berat['toples_3'] ?? 0,
                ]);
            @endphp
            <td class="center">{{ $airBasahUjung }}</td>
            <td class="center">{{ $airKeringUjung }}</td>
            <td class="center">{{ $minyakBasahUjung }}</td>
            <td class="center">{{ $minyakKeringUjung }}</td>
            <td class="center">{{ $pcsUjung }}</td>
            <td class="center">{{ $toplesUjung }}</td>
            <td rowspan="3" class="center">{{ $prepacking->username ?? '-' }}</td>
        </tr>
        {{-- SEAL --}}
        <tr>
            <td>Seal</td>
            @php
                $airBasahSeal = ($kondisi['basah_air_seal'] ?? 0);
                $airKeringSeal = ($kondisi['kering_air_seal'] ?? 0);
                $minyakBasahSeal = ($kondisi['basah_minyak_seal'] ?? 0);
                $minyakKeringSeal = ($kondisi['kering_minyak_seal'] ?? 0);
            @endphp
            <td class="center">{{ $airBasahSeal }}</td>
            <td class="center">{{ $airKeringSeal }}</td>
            <td class="center">{{ $minyakBasahSeal }}</td>
            <td class="center">{{ $minyakKeringSeal }}</td>
            <td class="center">-</td>
            <td class="center">-</td>
        </tr>
        {{-- TOTAL --}}
        <tr>
            <td>Total</td>
            <td class="center">{{ $airBasahUjung + $airBasahSeal }}</td>
            <td class="center">{{ $airKeringUjung + $airKeringSeal }}</td>
            <td class="center">{{ $minyakBasahUjung + $minyakBasahSeal }}</td>
            <td class="center">{{ $minyakKeringUjung + $minyakKeringSeal }}</td>
            <td class="center">-</td>
            <td class="center">-</td>
        </tr>
    @empty
        {{-- Empty row for no data --}}
        <tr>
            <td rowspan="3" class="center">-</td>
            <td rowspan="3" class="center">-</td>
            <td rowspan="3" class="center">-</td>
            <td rowspan="3" class="center">-</td>
            <td rowspan="3" class="center">-</td>
            <td>-</td>
            <td class="center">-</td>
            <td class="center">-</td>
            <td class="center">-</td>
            <td class="center">-</td>
            <td class="center">-</td>
            <td class="center">-</td>
            <td rowspan="3" class="center">-</td>
        </tr>
        <tr>
            <td>-</td>
            <td class="center">-</td>
            <td class="center">-</td>
            <td class="center">-</td>
            <td class="center">-</td>
            <td class="center">-</td>
            <td class="center">-</td>
        </tr>
        <tr>
            <td>-</td>
            <td class="center">-</td>
            <td class="center">-</td>
            <td class="center">-</td>
            <td class="center">-</td>
            <td class="center">-</td>
            <td class="center">-</td>
        </tr>
    @endforelse

    {{-- Fill remaining rows to make 10 rows --}}
    @for($i = count($prepackings) + 1; $i <= 10; $i++)
        {{-- UJUNG --}}
        <tr>
            <td rowspan="3" class="center">{{ $i }}</td>
            <td rowspan="3"></td>
            <td rowspan="3"></td>
            <td rowspan="3"></td>
            <td rowspan="3"></td>
            <td>Ujung</td>
            <td></td><td></td>
            <td></td><td></td>
            <td></td><td></td>
            <td rowspan="3"></td>
        </tr>
        {{-- SEAL --}}
        <tr>
            <td>Seal</td>
            <td></td><td></td>
            <td></td><td></td>
            <td></td><td></td>
        </tr>
        {{-- TOTAL --}}
        <tr>
            <td>Total</td>
            <td></td><td></td>
            <td></td><td></td>
            <td></td><td></td>
        </tr>
    @endfor
</table>

<br>

<table width="100%" class="small">
    <tr>
        <td>CATATAN :</td>
    </tr>
    <tr>
        <td style="height:50px;"></td>
    </tr>
</table>

<br><br>

<table width="100%" class="small">
    <tr>
        <td width="70%"></td>
        <td width="30%" class="sign">
            Disetujui Oleh,<br><br><br>
            ( ___________________ )<br>
            QC SPV
        </td>
    </tr>
</table>

<div style="text-align:right; font-size:8px;">QT 21 / 01</div>

</body>
</html>
