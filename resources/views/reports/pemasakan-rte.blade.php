<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-size: 10px; }
        .title { font-size: 10px; font-weight: bold; text-align: center; }
        table {
            border-collapse: collapse;
            border: 0.3px solid #000;
            width: 100%;
        }
        .tbl-header td {
            padding: 2px;
            font-size: 8px;
            border: 0.3px solid #000;
        }
        .tbl-main, .tbl-main th, .tbl-main td {
            border: 0.3px solid #000;
        }
        .tbl-main th {
            font-size: 8px;
            text-align: center;
            vertical-align: middle;
            padding: 2px;
        }
        .tbl-main td {
            padding: 2px;
            font-size: 8px;
                vertical-align: middle;
        }
        .center { text-align: center; }
        .small { font-size: 7px; }
        .sign { text-align: center; }
    </style>

</head>

<body>

@foreach ($data as $produk)
    {{-- HEADER --}}
    <table width="100%" style="border: none; border-collapse: collapse;">
    <tr>
        <td style="border: none;">
            PT Charoen Pokphand Indonesia<br>
            Food Division
        </td>
    </tr>
</table>


    <h3 class="center">PENGECEKAN PEMASAKAN RTE</h3>

    {{-- INFO --}}
    <table width="100%" class="tbl-header" style="border: none; border-collapse: collapse;">
        <tr>
            <td width="100%" style="border: none;">
                Hari/Tanggal : {{ $produk->date }}
            </td>
        </tr>
    </table>

    <br>
    <br>

   @php
$cooking = json_decode($produk->cooking, true);
@endphp


{{-- //MASIHPERLUREORGANISASI --}}
<table border="1" cellpadding="3" cellspacing="0" width="100%" style="border-collapse: collapse; font-size: 9px; text-align: center;">
    <tr>
        <th style="width:5%;">No</th>
        <th style="width:30%;">Parameter</th>
        <th style="width:15%;">Satuan</th>
        <th style="width:20%;">Standar</th>
        <th style="width:30%;">Isi / Nilai</th>
    </tr>

    {{-- 1. IDENTIFIKASI --}}
    <tr>
        <td>1</td>
        <td colspan="4" style="text-align:left; font-weight:bold;">IDENTIFIKASI</td>
    </tr>
    <tr>
        <td></td>
        <td>Nama Varian</td>
        <td>-</td>
        <td>-</td>
        <td>{{ $produk->nama_produk }}</td>
    </tr>
    <tr>
        <td></td>
        <td>No. Chamber</td>
        <td>-</td>
        <td>-</td>
        <td>{{ $produk->no_chamber ?? '-' }}</td>
    </tr>
    <tr>
        <td></td>
        <td>Kode Prod.</td>
        <td>-</td>
        <td>-</td>
        <td>{{ $produk->kode_produksi }}</td>
    </tr>
    <tr>
        <td></td>
        <td>Berat Varian</td>
        <td>gram</td>
        <td>-</td>
        <td>{{ $produk->berat_produk }}</td>
    </tr>
    <tr>
        <td></td>
        <td>Suhu Varian</td>
        <td>°C</td>
        <td>15 - 18</td>
        <td>{{ $cooking['suhu_air_awal'] ?? '-' }}</td>
    </tr>
    <tr>
        <td></td>
        <td>Jumlah Tray</td>
        <td>Tray</td>
        <td>28</td>
        <td>{{ $cooking['jumlah_tray'] ?? '-' }}</td>
    </tr>

    {{-- 2. PERSIAPAN --}}
    <tr>
    <td >2</td>
        <td colspan="4"  style="text-align:left; font-weight:bold;">PERSIAPAN</td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <td>Tekanan Angin</td>
        <td>Kg/Cm²</td>
        <td>5 - 8</td>
        <td>{{ $cooking['tekanan_angin'] ?? '-' }}</td>
    </tr>
    <tr>
        <td></td>
        <td>Tekanan Steam</td>
        <td>Kg/Cm²</td>
        <td>6 - 9</td>
        <td>{{ $cooking['tekanan_steam'] ?? '-' }}</td>
    </tr>
    <tr>
        <td></td>
        <td>Tekanan Air</td>
        <td>Kg/Cm²</td>
        <td>2 - 2,5</td>
        <td>{{ $cooking['tekanan_air'] ?? '-' }}</td>
    </tr>

    {{-- 3. PEMANASAN AWAL --}}
    <tr>
        <td>3</td>
        <td>PEMANASAN AWAL</td>
        <td>-</td>
        <td>-</td>
        <td>-</td>
    </tr>
    <tr>
        <td></td>
        <td>Suhu Air</td>
        <td>°C</td>
        <td>100 - 110</td>
        <td>{{ $cooking['suhu_air_awal'] ?? '-' }}</td>
    </tr>
    <tr>
        <td></td>
        <td>Tekanan</td>
        <td>Mpa</td>
        <td>0,26</td>
        <td>{{ $cooking['tekanan_awal'] ?? '-' }}</td>
    </tr>
    <tr>
        <td></td>
        <td>Waktu Mulai</td>
        <td>WIB</td>
        <td>1,5 - 2,5 menit</td>
        <td>{{ $cooking['waktu_mulai_awal'] ?? '-' }}</td>
    </tr>
    <tr>
        <td></td>
        <td>Waktu Selesai</td>
        <td>WIB</td>
        <td>-</td>
        <td>{{ $cooking['waktu_selesai_awal'] ?? '-' }}</td>
    </tr>

    {{-- 4. PROSES PEMANASAN --}}
    <tr>
        <td>4</td>
        <td>PROSES PEMANASAN</td>
        <td>-</td>
        <td>Standar</td>
        <td>-</td>
    </tr>
    <tr>
        <td></td>
        <td>Suhu Air</td>
        <td>°C</td>
        <td>121,2</td>
        <td>{{ $cooking['suhu_air_proses'] ?? '-' }}</td>
    </tr>
    <tr>
        <td></td>
        <td>Tekanan</td>
        <td>Mpa</td>
        <td>0,26</td>
        <td>{{ $cooking['tekanan_proses'] ?? '-' }}</td>
    </tr>
    <tr>
        <td></td>
        <td>Waktu Mulai</td>
        <td>WIB</td>
        <td>8 - 10 menit</td>
        <td>{{ $cooking['waktu_mulai_proses'] ?? '-' }}</td>
    </tr>
    <tr>
        <td></td>
        <td>Waktu Selesai</td>
        <td>WIB</td>
        <td>-</td>
        <td>{{ $cooking['waktu_selesai_proses'] ?? '-' }}</td>
    </tr>

    {{-- 5. STERILISASI --}}
    <tr>
        <td>5</td>
        <td>STERILISASI</td>
        <td>-</td>
        <td>Standar</td>
        <td>-</td>
    </tr>
    <tr>
        <td></td>
        <td>Suhu Air</td>
        <td>°C</td>
        <td>121,2</td>
        <td>{{ implode(', ', $cooking['suhu_air_sterilisasi'] ?? []) }}</td>
    </tr>
    <tr>
        <td></td>
        <td>Thermometer Retort</td>
        <td>°C</td>
        <td>121,2</td>
        <td>{{ implode(', ', $cooking['thermometer_retort'] ?? []) }}</td>
    </tr>
    <tr>
        <td></td>
        <td>Tekanan</td>
        <td>Mpa</td>
        <td>0,26</td>
        <td>{{ implode(', ', $cooking['tekanan_sterilisasi'] ?? []) }}</td>
    </tr>
    <tr>
        <td></td>
        <td>Waktu Mulai</td>
        <td>WIB</td>
        <td>50-55 menit</td>
        <td>{{ $cooking['waktu_mulai_sterilisasi'] ?? '-' }}</td>
    </tr>
    <tr>
        <td></td>
        <td>Waktu Pengecekan</td>
        <td>WIB</td>
        <td>-</td>
        <td>{{ implode(', ', $cooking['waktu_pengecekan_sterilisasi'] ?? []) }}</td>
    </tr>
    <tr>
        <td></td>
        <td>Waktu Selesai</td>
        <td>WIB</td>
        <td>-</td>
        <td>{{ $cooking['waktu_selesai_sterilisasi'] ?? '-' }}</td>
    </tr>

    {{-- 6. PENDINGINAN AWAL --}}
    <tr>
        <td>6</td>
        <td>PENDINGINAN AWAL</td>
        <td>-</td>
        <td>-</td>
        <td>-</td>
    </tr>
    <tr>
        <td></td>
        <td>Suhu Air</td>
        <td>°C</td>
        <td>30 - 35</td>
        <td>{{ $cooking['suhu_air_pendinginan_awal'] ?? '-' }}</td>
    </tr>
    <tr>
        <td></td>
        <td>Tekanan</td>
        <td>Mpa</td>
        <td>0,26</td>
        <td>{{ $cooking['tekanan_pendinginan_awal'] ?? '-' }}</td>
    </tr>
    <tr>
        <td></td>
        <td>Waktu Mulai</td>
        <td>WIB</td>
        <td>3 - 6 menit</td>
        <td>{{ $cooking['waktu_mulai_pendinginan_awal'] ?? '-' }}</td>
    </tr>
    <tr>
        <td></td>
        <td>Waktu Selesai</td>
        <td>WIB</td>
        <td>-</td>
        <td>{{ $cooking['waktu_selesai_pendinginan_awal'] ?? '-' }}</td>
    </tr>

    {{-- 7. PENDINGINAN --}}
    <tr>
        <td>7</td>
        <td>PENDINGINAN</td>
        <td>-</td>
        <td>-</td>
        <td>-</td>
    </tr>
    <tr>
        <td></td>
        <td>Suhu Air</td>
        <td>°C</td>
        <td>50 ± 3</td>
        <td>{{ $cooking['suhu_air_pendinginan'] ?? '-' }}</td>
    </tr>
    <tr>
        <td></td>
        <td>Tekanan</td>
        <td>Mpa</td>
        <td>0,26</td>
        <td>{{ $cooking['tekanan_pendinginan'] ?? '-' }}</td>
    </tr>
    <tr>
        <td></td>
        <td>Waktu Mulai</td>
        <td>WIB</td>
        <td>5 menit</td>
        <td>{{ $cooking['waktu_mulai_pendinginan'] ?? '-' }}</td>
    </tr>
    <tr>
        <td></td>
        <td>Waktu Selesai</td>
        <td>WIB</td>
        <td>-</td>
        <td>{{ $cooking['waktu_selesai_pendinginan'] ?? '-' }}</td>
    </tr>

    {{-- 8. PROSES AKHIR --}}
    <tr>
        <td>8</td>
        <td>PROSES AKHIR</td>
        <td>-</td>
        <td>-</td>
        <td>-</td>
    </tr>
    <tr>
        <td></td>
        <td>Suhu Air</td>
        <td>°C</td>
        <td>36 - 42</td>
        <td>{{ $cooking['suhu_air_akhir'] ?? '-' }}</td>
    </tr>
    <tr>
        <td></td>
        <td>Tekanan</td>
        <td>Mpa</td>
        <td>0</td>
        <td>{{ $cooking['tekanan_akhir'] ?? '-' }}</td>
    </tr>
    <tr>
        <td></td>
        <td>Waktu Mulai</td>
        <td>WIB</td>
        <td>2 - 3 menit</td>
        <td>{{ $cooking['waktu_mulai_akhir'] ?? '-' }}</td>
    </tr>
    <tr>
        <td></td>
        <td>Waktu Selesai</td>
        <td>WIB</td>
        <td>-</td>
        <td>{{ $cooking['waktu_selesai_akhir'] ?? '-' }}</td>
    </tr>

    {{-- 9. TOTAL WAKTU PROSES --}}
    <tr>
        <td>9</td>
        <td>TOTAL WAKTU PROSES</td>
        <td>-</td>
        <td>Standar</td>
        <td>-</td>
    </tr>
    <tr>
        <td></td>
        <td>Waktu Mulai</td>
        <td>WIB</td>
        <td>85 - 90 menit</td>
        <td>{{ $cooking['waktu_mulai_total'] ?? '-' }}</td>
    </tr>
    <tr>
        <td></td>
        <td>Waktu Selesai</td>
        <td>WIB</td>
        <td>-</td>
        <td>{{ $cooking['waktu_selesai_total'] ?? '-' }}</td>
    </tr>

    {{-- 10. SENSORI --}}
    <tr>
        <td>10</td>
        <td>SENSORI</td>
        <td>-</td>
        <td>200 gram</td>
        <td>-</td>
    </tr>
    <tr>
        <td></td>
        <td>Suhu Varian Akhir</td>
        <td>°C</td>
        <td>-</td>
        <td>{{ $cooking['suhu_produk_akhir'] ?? '-' }}</td>
    </tr>
    <tr>
        <td></td>
        <td>Sobek Seal</td>
        <td>-</td>
        <td>-</td>
        <td>{{ $cooking['sobek_seal'] ?? '-' }}</td>
    </tr>

    {{-- 11. TOTAL REJECT --}}
    <tr>
        <td>11</td>
        <td>TOTAL REJECT</td>
        <td>Kg</td>
        <td>-</td>
        <td>{{ $cooking['total_reject'] ?? '-' }}</td>
    </tr>

    {{-- PARAF --}}
    <tr>
        <td></td>
        <td>PARAF</td>
        <td>QC</td>
        <td>Produksi</td>
        <td>-</td>
    </tr>
</table>


    <div style="text-align:right; font-size:10px;">QT 37 / 00</div>

   
    <br>

    <br>

    <table width="100%" style="border:none; border-collapse:collapse; font-size:10px;">
        <tr>
            <td width="50%" style="border:none; text-align:center;">Diperiksa oleh</td>
            <td width="50%" style="border:none; text-align:center;">Disetujui oleh</td>
        </tr>

        <tr>
            <td style="border:none; text-align:center;"><br><br></td>
            <td style="border:none; text-align:center;"><br><br></td>
        </tr>

        <tr>
            <td style="border:none; text-align:center;">(___________________)</td>
            <td style="border:none; text-align:center;">(___________________)</td>
        </tr>

        <tr>
            <td style="border:none; text-align:center;">{{ $produk->username_updated ?? $produk->username }}</td>
            <td style="border:none; text-align:center;">{{ $produk->nama_spv }}</td>
        </tr>

        <tr>
            <td style="border:none; text-align:center;">QC</td>
            <td style="border:none; text-align:center;">QC SPV</td>
        </tr>
    </table>


   

    @if (!$loop->last)
        <div style="page-break-after: always;"></div>
    @endif
@endforeach

</body>
</html>
