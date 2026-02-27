<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Stuffing</title>
    <style>
        body {
            font-family: helvetica, sans-serif;
            font-size: 8px;
        }
        
        /* HEADER COMPANY */
        .company-header {
            width: 100%;
            border-bottom: 2px solid #000;
            margin-bottom: 10px;
        }
        .company-name {
            font-size: 10px;
            font-weight: bold;
        }
        .report-title {
            font-size: 12px;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            line-height: 1.5;
        }

        /* TABLE DATA STYLING */
        table.tbl-data {
            width: 100%;
            border-collapse: collapse; /* Wajib agar border nyambung */
        }
        
        /* Header Tabel */
        table.tbl-data th {
            background-color: #e3e3e3;
            border: 1px solid #000;
            font-weight: bold;
            text-align: center;
            vertical-align: middle; /* Tengah Vertikal */
            padding: 5px 0;
            line-height: 1.2;
        }

        /* Isi Tabel */
        table.tbl-data td {
            border: 1px solid #000;
            text-align: center;
            vertical-align: middle; /* KUNCI AGAR SEJAJAR */
            padding: 4px 2px;
            line-height: 1.2; /* Jarak antar baris teks */
        }

        /* Utility */
        .text-left { text-align: left !important; padding-left: 4px !important; }
        .text-bold { font-weight: bold; }
        
        /* Status Label */
        .status-ok { color: #006400; font-weight: bold; }
        .status-rev { color: #8B0000; font-weight: bold; }
    </style>
</head>
<body>

    {{-- 1. HEADER HALAMAN (Menggunakan Tabel agar rapi) --}}
    <table class="company-header" cellpadding="2">
        <tr>
            <td width="30%" class="company-name">
                PT Charoen Pokphand Indonesia<br>
                <span style="font-weight: normal; font-size: 8px;">Food Division</span>
            </td>
            <td width="40%" class="report-title">
                LAPORAN PEMERIKSAAN<br>STUFFING SOSIS RETORT
            </td>
            <td width="30%" style="text-align: right; font-size: 8px;">
                <strong>Dicetak:</strong> {{ date('d-m-Y H:i') }}<br>
                <strong>Oleh:</strong> {{ Auth::user()->username ?? 'System' }}
            </td>
        </tr>
    </table>

    {{-- 2. INFORMASI FILTER --}}
    <table width="100%" cellpadding="2" style="margin-bottom: 5px; font-size: 8px;">
        <tr>
            <td width="15%"><strong>Filter Tanggal</strong></td>
            <td width="35%">: {{ $request->date ? \Carbon\Carbon::parse($request->date)->format('d-m-Y') : 'SEMUA' }}</td>
            <td width="15%"><strong>Filter Shift</strong></td>
            <td width="35%">: {{ $request->shift ? $request->shift : 'SEMUA' }}</td>
        </tr>
    </table>

    {{-- 3. TABEL DATA UTAMA --}}
    {{-- Penting: Definisi width ditaruh di TH. Total harus 100% --}}
    <table class="tbl-data" nobr="true">
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="7%">Date</th>
                <th width="3%">Shift</th>
                <th width="16%">Nama Produk</th> {{-- Lebar diperbesar agar muat --}}
                <th width="9%">Kode<br>Produksi</th>
                <th width="6%">Mesin</th>
                <th width="5%">Jam</th>
                
                <th width="5%">Suhu<br>(°C)</th>
                <th width="5%">Speed</th>
                <th width="5%">Pjg<br>(cm)</th>
                <th width="5%">Berat<br>(gr)</th>
                <th width="4%">Seal</th>
                <th width="4%">Klip</th>
                
                <th width="10%">Catatan</th>
                <th width="4%">QC</th>
                <th width="5%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $index => $item)
            {{-- nobr="true" mencegah baris terpotong halaman --}}
            <tr nobr="true">
                <td width="3%">{{ $index + 1 }}</td>
                <td width="7%">{{ \Carbon\Carbon::parse($item->date)->format('d-m-y') }}</td>
                <td width="3%">{{ $item->shift }}</td>
                
                {{-- Text align Left untuk Produk --}}
                <td width="16%" class="text-left">{{ $item->nama_produk }}</td>
                
                <td width="9%">{{ $item->kode_produksi }}</td>
                <td width="6%">{{ $item->kode_mesin }}</td>
                <td width="5%">{{ \Carbon\Carbon::parse($item->jam_mulai)->format('H:i') }}</td>
                
                {{-- Menghapus .00 jika angka bulat --}}
                <td width="5%">{{ $item->suhu ? (float)$item->suhu : '-' }}</td>
                <td width="5%">{{ $item->kecepatan_stuffing ? (float)$item->kecepatan_stuffing : '-' }}</td>
                <td width="5%">{{ $item->panjang_pcs ? (float)$item->panjang_pcs : '-' }}</td>
                <td width="5%">{{ $item->berat_pcs ? (float)$item->berat_pcs : '-' }}</td>
                
                {{-- Simbol Centang (ZapfDingbats) --}}
                <td width="4%" >{{ !empty($item->kekuatan_seal) ? '4' : '-' }}</td>
                <td width="4%">{{ $item->diameter_klip ?? '-' }}</td>
                
                <td width="10%" class="text-left">{{ $item->catatan ?? '-' }}</td>
                <td width="4%">{{ $item->username }}</td>
                <td width="5%">
                    @if($item->status_spv == 1) <span class="status-ok">OK</span>
                    @elseif($item->status_spv == 2) <span class="status-rev">REV</span>
                    @else - @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="17" style="padding: 10px; text-align: center;">Data tidak ditemukan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- 4. FOOTER TANDA TANGAN (Menggunakan Tabel Stabil) --}}
    {{-- page-break-inside: avoid mencegah tanda tangan terpotong --}}
    <table width="100%" style="margin-top: 20px; page-break-inside: avoid;">
        <tr>
            <td width="70%">
                <div style="font-size: 7px; font-style: italic;">
                   
                </div>
            </td>
            <td width="30%" style="text-align: center;">
                <div style="font-size: 9px;">Disetujui Oleh,</div>
                <div style="font-size: 9px; margin-bottom: 40px;">QC Supervisor</div>
                <br><br><br>
                <div style="border-bottom: 1px solid #000; width: 80%; margin: 0 auto;"></div>
                {{-- Garis Tanda Tangan --}}
            </td>
        </tr>
    </table>

</body>
</html>