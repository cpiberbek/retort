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

        /* tnr */
        body,
        table,
        tr,
        td,
        th {
            font-family: times;
            font-size: 6pt;
        }
    </style>
</head>
<body>

    {{-- 1. HEADER --}}
    <div style="margin-left:-30px;">
        <table width="100%" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td width="32">
                    <img src="{{ public_path('assets/img/Logo CPI.png') }}" width="30">
                </td>
                <td>
                    <span style="font-size:9pt;"><b>PT Charoen </b></span><br>
                    <span style="font-size:9pt;"><b>Pokphand Indonesia</b></span><br>
                    <span style="font-size:9pt;"><b>Food Division</b></span>
                </td>
            </tr>
        </table>
    </div>

    {{-- 2. INFO SEBELUM TABEL --}}
    @php
        $tanggal = $items->pluck('date')
            ->unique()
            ->map(fn($item) => \Carbon\Carbon::parse($item)->translatedFormat('l, d-m-Y'))
            ->implode(', ');

        $shift = $items->pluck('shift')
            ->unique()
            ->implode(', ');

        $produkExpired = $items
            ->unique(function ($item) {
                return $item->nama_produk . $item->tgl_expired;
            })
            ->map(function ($item) {
                return '[' . $item->nama_produk . ', ' . \Carbon\Carbon::parse($item->tgl_expired)->format('d-m-Y') . ']';
            })
            ->implode(', ');

        $tglKedatangan = $items
            ->pluck('tgl_kedatangan')
            ->unique()
            ->map(fn($item) => \Carbon\Carbon::parse($item)->format('d-m-Y'))
            ->implode(', ');
    @endphp

    <table width="100%" cellspacing="0" cellpadding="0">
        <tr>
            <td width="33%">
                <strong>Hari/Tanggal</strong> : {{ $tanggal ?: '-' }}
            </td>

           <td width="20%">
                <strong>Shift</strong> : {{ $items->pluck('shift')->unique()->implode(', ') ?: '-' }}
            </td>

            <td width="34%">
                <strong>Nama Produk</strong> : {{ $items->pluck('nama_produk')->unique()->implode(', ') ?: '-' }}
            </td>
        </tr>

        <tr>
            <td colspan="2">
                <strong>Tanggal kedatangan PVDC</strong> : {{ $tglKedatangan ?: '-' }}
            </td>

            <td>
                <strong>Tanggal expired</strong> : {{ $items->first()->tgl_expired ? \Carbon\Carbon::parse($items->first()->tgl_expired)->format('d-m-Y') : '-' }}
            </td>
        </tr>
    </table>
    {{-- 3. TABEL DATA --}}
    {{-- Menggunakan border=1 di HTML juga membantu kompatibilitas PDF reader lama --}}
    @php
        $mesinGrid = $items->groupBy('kode_mesin')->values()->chunk(3);
    @endphp

    <table width="100%" cellspacing="0" cellpadding="3">
    @foreach($mesinGrid as $row)

    <tr>
    @foreach($row as $mesin => $data)

    <td width="33.33%" valign="top">

        <table width="100%" border="1" cellspacing="0" cellpadding="3">
            <tr>
                <td colspan="3" align="center">
                    <b>Mesin : {{ $data->first()->kode_mesin }}</b>
                </td>
            </tr>

            <tr>
                <th>Batch</th>
                <th>No. Lot</th>
                <th>Waktu</th>
            </tr>

            @foreach($data as $item)
            <tr>
                <td>{{ $item->kode_produksi }}</td>
                <td>{{ $item->no_lot }}</td>
                <td>{{ $item->jam_mulai }}</td>
            </tr>
            @endforeach

        </table>

    </td>

    @endforeach

    @for($i = $row->count(); $i < 3; $i++)
    <td width="33.33%"></td>
    @endfor

    </tr>

    @endforeach
    </table>

<table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-top:1px solid #000;">
    <tr>
        <td width="75%"></td>
        <td width="25%" align="right" style="font-style: italic;">
            {{ $noDokumen }}
        </td>
    </tr>
</table>

    {{-- 4. TANDA TANGAN --}}
    @php
        $approved = $items->every(function ($item) {
            return $item->status_spv == 1;
        });

        $namaSpv = $approved
            ? $items->pluck('nama_spv')->unique()->implode(', ')
            : 'Masih ada dokumen yang belum di-Approve SPV';
    @endphp

    <table class="footer-wrapper">
        <tr>
            <td width="75%"></td>

            <td width="25%" style="text-align: center;">
                <div style="margin-bottom: 50px;">Disetujui Oleh,</div>

                <div style="font-weight: bold; display:inline-block; border-bottom:1px solid #000; padding-bottom:2px;">
                    {{ $namaSpv ?: 'SPV / QC Head' }}
                </div>
            </td>
        </tr>
    </table>
</body>
</html>