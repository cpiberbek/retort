<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pengecekan Pemasakan</title>
    <style>
        body { font-family: helvetica, sans-serif; font-size: 9pt; }
        
        /* Layout Tabel Utama */
        table.main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            table-layout: fixed; /* KUNCI UTAMA: Agar lebar kolom konsisten */
        }
        table.main-table th, table.main-table td {
            border: 1px solid #000;
            padding: 4px;
            vertical-align: middle;
            word-wrap: break-word; /* Mencegah teks panjang merusak layout */
        }
        
        /* Header Kolom & Definisi Lebar */
        .header-col {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }
        
        /* Definisi Lebar Kolom (Total 100%) */
        .col-no { width: 5%; text-align: center; }
        .col-desc { width: 35%; }
        .col-unit { width: 15%; text-align: center; }
        .col-std { width: 20%; text-align: center; }
        .col-res { width: 25%; text-align: center; }

        /* Styling Khusus */
        .text-bold { font-weight: bold; }
        .text-blue { color: blue; font-weight: bold; }
        .bg-grey { background-color: #e0e0e0; }

        /* Judul Section */
        .section-title {
            background-color: #e0e0e0;
            font-weight: bold;
            text-align: left;
            padding-left: 5px;
            border: 1px solid #000;
        }

        /* Tanda Tangan */
        .sign-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .sign-table td { border: 1px solid #000; text-align: center; padding: 5px; }
        
        /* Utility */
        .text-center { text-align: center; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>

@foreach($items as $item)
    @php 
        $c = json_decode($item->cooking, true); 
        
        $show = function($key) use ($c) {
            $val = $c[$key] ?? null;
            if (is_array($val)) {
                return implode(', ', $val);
            }
            return $val ?? '-';
        };

        $fmtTime = function($key) use ($c) {
            $t = $c[$key] ?? null;
            if (is_array($t)) $t = $t[0] ?? null;
            return $t ? \Carbon\Carbon::parse($t)->format('H:i') : '-';
        };
    @endphp

    {{-- HEADER HALAMAN --}}
    <table width="100%" cellpadding="2">
        <tr>
            <td width="60%">
                <strong>PT Charoen Pokphand Indonesia</strong><br>
                Food Division
            </td>
            <td width="40%" align="right">
                <table width="100%" style="border-collapse:collapse;">
                    <tr>
                        <td width="40%">Hari/Tanggal</td>
                        <td width="60%">: {{ \Carbon\Carbon::parse($item->date)->isoFormat('dddd, D MMMM Y') }}</td>
                    </tr>
                    <tr>
                        <td>Shift</td>
                        <td>: {{ $item->shift }}</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2" align="center" style="padding-top:10px; padding-bottom:5px;">
                <h3 style="margin:0; text-decoration: underline;">PENGECEKAN PEMASAKAN</h3>
            </td>
        </tr>
    </table>

    {{-- TABEL FORMULIR --}}
    <table class="main-table" nobr="true">
        <thead>
            <tr class="header-col">
                <td class="col-no text-bold">NO</td>
                <td class="col-desc text-center">IDENTIFIKASI / PROSES</td>
                <td class="col-unit text-bold">SATUAN</td>
                <td class="col-std text-bold">STANDAR</td>
                <td class="col-res text-blue">HASIL</td>
            </tr>
        </thead>
        <tbody>
            {{-- 1. IDENTIFIKASI --}}
            <tr>
                <td class="col-no text-bold">1</td>
                <td colspan="4" class="section-title">IDENTIFIKASI</td>
            </tr>
            <tr>
                <td class="col-no"></td>
                <td class="col-desc">Nama Varian</td>
                <td class="col-unit">-</td>
                <td class="col-std">-</td>
                <td class="col-res text-blue">{{ $item->nama_produk }}</td>
            </tr>
            <tr>
                <td class="col-no"></td>
                <td class="col-desc">No. Chamber</td>
                <td class="col-unit">-</td>
                <td class="col-std">-</td>
                <td class="col-res text-blue">{{ $item->no_chamber }}</td>
            </tr>
            <tr>
                <td class="col-no"></td>
                <td class="col-desc">Kode Prod</td>
                <td class="col-unit">-</td>
                <td class="col-std">-</td>
                <td class="col-res text-blue">{{ $item->kode_produksi }}</td>
            </tr>
            <tr>
                <td class="col-no"></td>
                <td class="col-desc">Berat Varian</td>
                <td class="col-unit">gram</td>
                <td class="col-std">-</td>
                <td class="col-res text-blue">{{ $item->berat_produk }}</td>
            </tr>
            <tr>
                <td class="col-no"></td>
                <td class="col-desc">Suhu Varian</td>
                <td class="col-unit">°C</td>
                <td class="col-std">-</td>
                <td class="col-res text-blue">{{ $item->suhu_produk }}</td>
            </tr>
            <tr>
                <td class="col-no"></td>
                <td class="col-desc">Jumlah Tray</td>
                <td class="col-unit">Tray</td>
                <td class="col-std">-</td>
                <td class="col-res text-blue">{{ $item->jumlah_tray }}</td>
            </tr>

            {{-- 2. PERSIAPAN --}}
            <tr>
                <td class="col-no text-bold">2</td>
                <td colspan="4" class="section-title">PERSIAPAN</td>
            </tr>
            <tr>
                <td class="col-no"></td>
                <td class="col-desc">Tekanan Angin</td>
                <td class="col-unit">Kg/Cm²</td>
                <td class="col-std">5 - 8</td>
                <td class="col-res text-blue">{{ $show('tekanan_angin') }}</td>
            </tr>
            <tr>
                <td class="col-no"></td>
                <td class="col-desc">Tekanan Steam</td>
                <td class="col-unit">Kg/Cm²</td>
                <td class="col-std">6 - 9</td>
                <td class="col-res text-blue">{{ $show('tekanan_steam') }}</td>
            </tr>
            <tr>
                <td class="col-no"></td>
                <td class="col-desc">Tekanan Air</td>
                <td class="col-unit">Kg/Cm²</td>
                <td class="col-std">2 - 2.5</td>
                <td class="col-res text-blue">{{ $show('tekanan_air') }}</td>
            </tr>

            {{-- 3. PEMANASAN AWAL --}}
            <tr>
                <td class="col-no text-bold">3</td>
                <td colspan="4" class="section-title">PEMANASAN AWAL</td>
            </tr>
            <tr>
                <td class="col-no"></td>
                <td class="col-desc">Suhu Air</td>
                <td class="col-unit">°C</td>
                <td class="col-std">100 - 110</td>
                <td class="col-res text-blue">{{ $show('suhu_air_awal') }}</td>
            </tr>
            <tr>
                <td class="col-no"></td>
                <td class="col-desc">Tekanan</td>
                <td class="col-unit">Mpa</td>
                <td class="col-std">0.26</td>
                <td class="col-res text-blue">{{ $show('tekanan_awal') }}</td>
            </tr>
            <tr>
                <td class="col-no"></td>
                <td class="col-desc">Waktu Mulai - Selesai</td>
                <td class="col-unit">WIB</td>
                <td class="col-std">1.5 - 2.5 mnt</td>
                <td class="col-res text-blue">
                    {{ $fmtTime('waktu_mulai_awal') }} - {{ $fmtTime('waktu_selesai_awal') }}
                </td>
            </tr>

            {{-- 4. PROSES PEMANASAN --}}
            <tr>
                <td class="col-no text-bold">4</td>
                <td colspan="4" class="section-title">PROSES PEMANASAN</td>
            </tr>
            <tr>
                <td class="col-no"></td>
                <td class="col-desc">Suhu Air</td>
                <td class="col-unit">°C</td>
                <td class="col-std">119 - 121.2</td>
                <td class="col-res text-blue">{{ $show('suhu_air_proses') }}</td>
            </tr>
            <tr>
                <td class="col-no"></td>
                <td class="col-desc">Tekanan</td>
                <td class="col-unit">Mpa</td>
                <td class="col-std">0.26</td>
                <td class="col-res text-blue">{{ $show('tekanan_proses') }}</td>
            </tr>
            <tr>
                <td class="col-no"></td>
                <td class="col-desc">Waktu Mulai - Selesai</td>
                <td class="col-unit">WIB</td>
                <td class="col-std">8 - 10 mnt</td>
                <td class="col-res text-blue">
                    {{ $fmtTime('waktu_mulai_proses') }} - {{ $fmtTime('waktu_selesai_proses') }}
                </td>
            </tr>

            {{-- 5. STERILISASI --}}
            <tr>
                <td class="col-no text-bold">5</td>
                <td colspan="4" class="section-title">STERILISASI</td>
            </tr>
            <tr>
                <td class="col-no"></td>
                <td class="col-desc">Suhu Air</td>
                <td class="col-unit">°C</td>
                <td class="col-std">121.2</td>
                <td class="col-res text-blue">{{ $show('suhu_air_sterilisasi') }}</td>
            </tr>
            <tr>
                <td class="col-no"></td>
                <td class="col-desc">Thermometer Retort</td>
                <td class="col-unit">°C</td>
                <td class="col-std">121.2</td>
                <td class="col-res text-blue">{{ $show('thermometer_retort') }}</td>
            </tr>
            <tr>
                <td class="col-no"></td>
                <td class="col-desc">Tekanan</td>
                <td class="col-unit">Mpa</td>
                <td class="col-std">0.26</td>
                <td class="col-res text-blue">{{ $show('tekanan_sterilisasi') }}</td>
            </tr>
            <tr>
                <td class="col-no"></td>
                <td class="col-desc">Waktu Mulai - Selesai</td>
                <td class="col-unit">WIB</td>
                <td class="col-std">12 - 16 mnt</td>
                <td class="col-res text-blue">
                    {{ $fmtTime('waktu_mulai_sterilisasi') }} - {{ $fmtTime('waktu_selesai_sterilisasi') }}
                </td>
            </tr>

            {{-- 6. PENDINGINAN AWAL --}}
            <tr>
                <td class="col-no text-bold">6</td>
                <td colspan="4" class="section-title">PENDINGINAN AWAL</td>
            </tr>
            <tr>
                <td class="col-no"></td>
                <td class="col-desc">Suhu Air</td>
                <td class="col-unit">°C</td>
                <td class="col-std">30 - 35</td>
                <td class="col-res text-blue">{{ $show('suhu_air_pendinginan_awal') }}</td>
            </tr>
            <tr>
                <td class="col-no"></td>
                <td class="col-desc">Tekanan</td>
                <td class="col-unit">Mpa</td>
                <td class="col-std">1.2 - 2.6</td>
                <td class="col-res text-blue">{{ $show('tekanan_pendinginan_awal') }}</td>
            </tr>
            <tr>
                <td class="col-no"></td>
                <td class="col-desc">Waktu (Durasi)</td>
                <td class="col-unit">WIB</td>
                <td class="col-std">3 - 6 mnt</td>
                <td class="col-res text-blue">
                    {{ $fmtTime('waktu_mulai_pendinginan_awal') }} - {{ $fmtTime('waktu_selesai_pendinginan_awal') }}
                </td>
            </tr>

            {{-- 7. PENDINGINAN --}}
            <tr>
                <td class="col-no text-bold">7</td>
                <td colspan="4" class="section-title">PENDINGINAN</td>
            </tr>
            <tr>
                <td class="col-no"></td>
                <td class="col-desc">Suhu Air</td>
                <td class="col-unit">°C</td>
                <td class="col-std">50 ± 3</td>
                <td class="col-res text-blue">{{ $show('suhu_air_pendinginan') }}</td>
            </tr>
            <tr>
                <td class="col-no"></td>
                <td class="col-desc">Tekanan</td>
                <td class="col-unit">Mpa</td>
                <td class="col-std">0.26</td>
                <td class="col-res text-blue">{{ $show('tekanan_pendinginan') }}</td>
            </tr>
            <tr>
                <td class="col-no"></td>
                <td class="col-desc">Waktu (Durasi)</td>
                <td class="col-unit">WIB</td>
                <td class="col-std">5 mnt</td>
                <td class="col-res text-blue">
                    {{ $fmtTime('waktu_mulai_pendinginan') }} - {{ $fmtTime('waktu_selesai_pendinginan') }}
                </td>
            </tr>

            {{-- 8. PROSES AKHIR --}}
            <tr>
                <td class="col-no text-bold">8</td>
                <td colspan="4" class="section-title">PROSES AKHIR</td>
            </tr>
            <tr>
                <td class="col-no"></td>
                <td class="col-desc">Suhu Air</td>
                <td class="col-unit">°C</td>
                <td class="col-std">36 - 42</td>
                <td class="col-res text-blue">{{ $show('suhu_air_akhir') }}</td>
            </tr>
            <tr>
                <td class="col-no"></td>
                <td class="col-desc">Tekanan</td>
                <td class="col-unit">Mpa</td>
                <td class="col-std">0</td>
                <td class="col-res text-blue">{{ $show('tekanan_akhir') }}</td>
            </tr>
            <tr>
                <td class="col-no"></td>
                <td class="col-desc">Waktu (Durasi)</td>
                <td class="col-unit">WIB</td>
                <td class="col-std">2 - 3 mnt</td>
                <td class="col-res text-blue">
                    {{ $fmtTime('waktu_mulai_akhir') }} - {{ $fmtTime('waktu_selesai_akhir') }}
                </td>
            </tr>

            {{-- 9. TOTAL WAKTU --}}
            <tr>
                <td class="col-no text-bold">9</td>
                <td colspan="4" class="section-title">TOTAL WAKTU PROSES</td>
            </tr>
            <tr>
                <td class="col-no"></td>
                <td class="col-desc">Total Waktu</td>
                <td class="col-unit">menit</td>
                <td class="col-std">36.5 - 42.5</td>
                <td class="col-res text-blue">
                    {{ $fmtTime('waktu_mulai_total') }} - {{ $fmtTime('waktu_selesai_total') }}
                </td>
            </tr>

            {{-- 10. HASIL --}}
            <tr>
                <td class="col-no text-bold">10</td>
                <td colspan="4" class="section-title">HASIL PEMASAKAN</td>
            </tr>
            <tr>
                <td class="col-no"></td>
                <td class="col-desc">Suhu Varian Akhir</td>
                <td class="col-unit">°C</td>
                <td class="col-std">48 ± 2</td>
                <td class="col-res text-blue">{{ $show('suhu_produk_akhir') }}</td>
            </tr>
            <tr>
                <td class="col-no"></td>
                <td class="col-desc">Panjang</td>
                <td class="col-unit">cm</td>
                <td class="col-std">9 - 10.5</td>
                <td class="col-res text-blue">{{ $show('panjang') }}</td>
            </tr>
            <tr>
                <td class="col-no"></td>
                <td class="col-desc">Diameter</td>
                <td class="col-unit">cm</td>
                <td class="col-std">13.5 - 14.5</td>
                <td class="col-res text-blue">{{ $show('diameter') }}</td>
            </tr>
            <tr>
                <td class="col-no"></td>
                <td class="col-desc">Rasa / Warna / Aroma</td>
                <td class="col-unit">1 - 3</td>
                <td class="col-std">Min. 2</td>
                <td class="col-res text-blue">
                    R:{{ $show('rasa') }} / W:{{ $show('warna') }} / A:{{ $show('aroma') }}
                </td>
            </tr>
            
            {{-- 11. REJECT --}}
            <tr>
                <td class="col-no text-bold">11</td>
                <td colspan="2" class="section-title">TOTAL REJECT</td>
                <td colspan="2" class="col-res text-blue text-center">{{ $item->total_reject ?? '0' }} Kg</td>
            </tr>
        </tbody>
    </table>

    {{-- FOOTER / TANDA TANGAN --}}
    <table class="sign-table" nobr="true">
        <tr>
            <td width="60%" style="text-align: left; vertical-align: top; height: 60px;">
                <strong>Catatan:</strong><br>
                {{ $item->catatan ?? '-' }}
            </td>
            <td width="20%">
                PARAF Produksi<br><br><br>
                {{ $item->nama_produksi }}
            </td>
            <td width="20%">
                PARAF QC<br><br><br>
                {{ $item->username }}
            </td>
        </tr>
        <tr>
            <td colspan="3" align="center" style="border:none;">
                <br>
                Disetujui oleh,<br>
                <strong>QC SPV</strong>
                <br><br>
                ( ___________________ )
            </td>
        </tr>
    </table>
    
    <div style="font-size: 8px; text-align: right; margin-top: 5px;">
        Kode Dokumen: QT 10/01
    </div>

    @if(!$loop->last)
        <div class="page-break"></div>
    @endif

@endforeach

</body>
</html>