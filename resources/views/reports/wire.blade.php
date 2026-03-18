<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Data No. Lot Wire</title>
    <style>
        body {
            font-family: helvetica, sans-serif;
            font-size: 8px;
            line-height: 1.1; /* Line height dirapatkan sedikit */
        }
        
        /* HEADER */
        .company-header {
            width: 100%;
            border-bottom: 2px solid #000;
            margin-bottom: 10px;
        }
        .company-name { font-size: 10px; font-weight: bold; }
        .report-title { font-size: 12px; font-weight: bold; text-align: center; text-transform: uppercase; }

        /* TABEL UTAMA */
        table.tbl-data {
            width: 100%;
            border-collapse: collapse;
        }
        table.tbl-data th {
            background-color: #e6e6e6;
            border: 1px solid #000;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
            padding: 5px 2px;
        }
        table.tbl-data td {
            border: 1px solid #000;
            vertical-align: middle; /* Pastikan semua kolom sejajar tengah secara vertikal */
            padding: 4px 2px; /* Padding standar untuk kolom biasa */
        }

        /* KHUSUS KOLOM DETAIL WIRE (NESTED) */
        /* Kita hilangkan padding di TD pembungkus agar tabel di dalamnya bisa full width */
        td.td-nested {
            padding: 0 !important; 
            margin: 0 !important;
            vertical-align: top; /* Align top agar rapi jika isinya panjang */
        }

        /* TABEL DI DALAM KOLOM DETAIL */
        table.nested-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
            margin: 0;
        }
        table.nested-table td {
            border: none; 
            border-bottom: 1px solid #ccc; /* Garis pemisah antar item lebih tipis */
            padding: 3px 4px; /* Padding dalam item agar teks tidak nempel garis */
            vertical-align: top;
            text-align: left;
        }
        /* Hapus border bawah untuk baris terakhir di nested table */
        table.nested-table tr:last-child td {
            border-bottom: none;
        }

        .mesin-label {
            font-weight: bold;
            font-size: 7px;
            color: #000;
            background-color: #f9f9f9; /* Sedikit background beda untuk nama mesin */
        }

        /* Utility */
        .text-center { text-align: center !important; }
        .text-left { text-align: left !important; }
        .bg-ok { color: #006400; font-weight: bold; }
        .bg-rev { color: #8B0000; font-weight: bold; }
    </style>
</head>
<body>

    {{-- HEADER --}}
    <table class="company-header" cellpadding="2">
        <tr>
            <td width="30%" class="company-name">
                PT Charoen Pokphand Indonesia<br>
                <span style="font-weight: normal; font-size: 8px;">Food Division</span>
            </td>
            <td width="40%" class="report-title">LAPORAN DATA NO. LOT WIRE</td>
            <td width="30%" style="text-align: right; font-size: 8px;">
                <strong>Dicetak:</strong> {{ date('d-m-Y H:i') }}<br>
                <strong>Oleh:</strong> {{ Auth::user()->username ?? 'System' }}
            </td>
        </tr>
    </table>

    {{-- FILTER INFO --}}
    <table width="100%" cellpadding="2" style="margin-bottom: 8px; font-size: 8px;">
        <tr>
            <td width="15%"><strong>Filter Tanggal</strong></td>
            <td width="35%">: {{ $request->date ? \Carbon\Carbon::parse($request->date)->format('d-m-Y') : 'SEMUA' }}</td>
            <td width="15%"><strong>Filter Shift</strong></td>
            <td width="35%">: {{ $request->shift ? $request->shift : 'SEMUA' }}</td>
        </tr>
    </table>

    {{-- TABEL DATA --}}
    <table class="tbl-data" nobr="true">
        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="8%">Date</th>
                <th width="4%">Shf</th>
                <th width="15%">Nama Varian</th>
                <th width="15%">Supplier</th>
                <th width="35%">Detail Wire (Mesin : Start-End / No. Lot)</th>
                <th width="12%">Catatan</th>
                <th width="7%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $index => $item)
            <tr nobr="true">
                <td width="4%" class="text-center">{{ $index + 1 }}</td>
                <td width="8%" class="text-center">{{ \Carbon\Carbon::parse($item->date)->format('d-m-y') }}</td>
                <td width="4%" class="text-center">{{ $item->shift }}</td>
                <td width="15%" class="text-left">{{ $item->nama_produk }}</td>
                <td width="15%" class="text-left">{{ $item->nama_supplier }}</td>
                
                {{-- KOLOM NESTED --}}
                {{-- Class 'td-nested' menghilangkan padding wrapper agar tabel dalam full --}}
                <td width="35%" class="td-nested">
                    @php $dataWire = json_decode($item->data_wire, true); @endphp
                    
                    @if(!empty($dataWire) && is_array($dataWire))
                        <table class="nested-table" cellpadding="0" cellspacing="0">
                        @foreach($dataWire as $mesin)
                            {{-- Header Mesin --}}
                            <tr>
                                <td colspan="2" class="mesin-label">
                                    <u>Mesin: {{ $mesin['mesin'] ?? '-' }}</u>
                                </td>
                            </tr>
                            
                            {{-- Isi Detail --}}
                            @if(!empty($mesin['detail']) && is_array($mesin['detail']))
                                @foreach($mesin['detail'] as $dtl)
                                <tr>
                                    {{-- Atur lebar kolom dalam nested table agar rapi --}}
                                    <td width="35%" style="padding-left: 10px;">
                                        {{ $dtl['start'] ?? '' }} - {{ $dtl['end'] ?? '' }}
                                    </td>
                                    <td width="65%">
                                        <strong>Lot:</strong> {{ $dtl['no_lot'] ?? '' }}
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr><td colspan="2" style="font-style:italic; padding-left:10px;">- Kosong -</td></tr>
                            @endif
                        @endforeach
                        </table>
                    @else
                        <div style="padding: 5px; text-align: center;">-</div>
                    @endif
                </td>

                <td width="12%" class="text-left">{{ $item->catatan ?? '-' }}</td>
                <td width="7%" class="text-center">
                    @if($item->status_spv == 1) <span class="bg-ok">VERIFIED</span>
                    @elseif($item->status_spv == 2) <span class="bg-rev">REVISI</span>
                    @else - @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center" style="padding: 10px;">Data tidak ditemukan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- FOOTER TANDA TANGAN --}}
    <table width="100%" style="margin-top: 20px; page-break-inside: avoid;">
        <tr>
            <td width="70%">
                <div style="font-size: 7px; font-style: italic;"></div>
            </td>
            <td width="30%" align="center">
                <div style="font-size: 9px;">Disetujui Oleh,</div>
                <div style="font-size: 9px; margin-bottom: 40px;">QC Supervisor</div>
                <div style="border-bottom: 1px solid #000; width: 80%;"></div>
            </td>
        </tr>
    </table>

</body>
</html>