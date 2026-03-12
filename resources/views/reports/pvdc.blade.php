<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan PVDC</title>
    <style>
        @page { margin: 10px 20px; } /* Margin halaman minimal */
        
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 9px;
            color: #333;
        }

        /* HEADER DOKUMEN */
        .header-table {
            width: 100%;
            margin-bottom: 10px;
            border-bottom: 2px solid #000;
        }
        .judul-laporan {
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
        }

        /* INFO FILTER */
        .info-table {
            width: 100%;
            font-size: 9px;
            margin-bottom: 5px;
        }
        .info-table td {
            padding: 1px 0; /* Jarak antar baris info rapat */
        }

        /* TABEL UTAMA - KUNCI PRESISI DI SINI */
        .tbl-data {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed; /* WAJIB: Agar kolom tidak melar sendiri */
        }

        .tbl-data th, 
        .tbl-data td {
            border: 1px solid #000;
            padding: 4px 2px; /* Atas/Bawah 4px, Kiri/Kanan 2px */
            vertical-align: middle; /* Teks selalu di tengah vertikal */
            word-wrap: break-word; /* Teks panjang akan turun ke bawah, tidak melebar */
            overflow: hidden; /* Mencegah konten keluar garis */
        }

        .tbl-data th {
            background-color: #ffffff;
            font-weight: bold;
            text-align: center;
            height: 20px; /* Tinggi header fix */
        }

        .tbl-data td {
            text-align: center;
            height: 15px; /* Tinggi baris data minimal */
        }

        /* Helper alignment */
        .text-left { text-align: left !important; padding-left: 4px !important; }
        .text-center { text-align: center !important; }
        
        /* Footer */
        .footer-wrapper {
            width: 100%;
            margin-top: 20px;
            page-break-inside: avoid;
        }
    </style>
</head>
<body>

    {{-- 1. HEADER --}}
    <table class="header-table" cellpadding="0" cellspacing="0">
        <tr>
            <td width="30%" style="vertical-align: top;">
                <strong>PT Charoen Pokphand Indonesia</strong><br>
                <span style="font-size: 8px;">Food Division - Plant Ngoro</span>
            </td>
            <td width="40%" class="judul-laporan">
                LAPORAN HARIAN<br>PEMERIKSAAN NO. LOT PVDC
            </td>
            <td width="30%" style="text-align: right; font-size: 8px; vertical-align: top;">
                <strong>Dicetak:</strong> {{ date('d-m-Y H:i') }}<br>
                <strong>Oleh:</strong> {{ Auth::user()->username ?? 'superadmin' }}
            </td>
        </tr>
    </table>

    {{-- 2. INFO FILTER --}}
    <table class="info-table">
        <tr>
            <td width="15%"><strong>Tanggal Laporan</strong></td>
            <td width="35%">: {{ $request->date ? \Carbon\Carbon::parse($request->date)->format('d-m-Y') : 'SEMUA TANGGAL' }}</td>
            <td width="15%"><strong>Filter Varian</strong></td>
            <td width="35%">: {{ $request->nama_produk ?? 'SEMUA VARIAN' }}</td>
        </tr>
        <tr>
            <td><strong>Shift</strong></td>
            <td>: {{ $request->shift ? $request->shift : 'SEMUA SHIFT' }}</td>
            <td><strong>Pencarian</strong></td>
            <td>: {{ $request->search ?? '-' }}</td>
        </tr>
    </table>

    {{-- 3. TABEL DATA --}}
    {{-- Menggunakan border=1 di HTML juga membantu kompatibilitas PDF reader lama --}}
    <table class="tbl-data" border="1" cellspacing="0" cellpadding="0">
        {{-- COLGROUP: INI KUNCI AGAR GARIS LURUS PRESISI --}}
        {{-- Total Width = 100% --}}
        <colgroup>
            <col style="width: 4%">  <col style="width: 8%">  <col style="width: 5%">  <col style="width: 23%"> <col style="width: 6%">  <col style="width: 10%"> <col style="width: 12%"> <col style="width: 6%">  <col style="width: 20%"> <col style="width: 6%">  </colgroup>

        <thead>
            <tr>
                <th>No</th>
                <th>Tgl</th>
                <th>Shift</th>
                <th>Varian</th>
                <th>Mesin</th>
                <th>Batch</th>
                <th>No. Lot</th>
                <th>Jam</th>
                <th>Catatan</th>
                <th>QC</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($item->date)->format('d-m-y') }}</td>
                <td>{{ $item->shift }}</td>
                <td class="text-left">{{ substr($item->nama_produk, 0, 35) }}</td>
                <td>{{ $item->kode_mesin }}</td>
                <td>{{ $item->kode_produksi }}</td>
                <td>{{ $item->no_lot }}</td>
                <td>{{ $item->jam_mulai }}</td>
                <td class="text-left">{{ $item->catatan }}</td>
                <td>
                    @if($item->status_spv == 1) OK
                    @elseif($item->status_spv == 2) REV
                    @else - @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="10" style="padding: 15px; text-align: center;">Data tidak ditemukan pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- 4. TANDA TANGAN --}}
    <table class="footer-wrapper">
        <tr>
            <td width="75%"></td> <td width="25%" style="text-align: center;">
                <div style="margin-bottom: 50px;">Disetujui Oleh,</div>
                <div style="font-weight: bold; border-bottom: 1px solid #000; padding-bottom: 2px;">SPV / QC Head</div>
            </td>
        </tr>
    </table>

</body>
</html>