<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Sampling Finish Good</title>
    <style>
        body { font-family: helvetica, sans-serif; font-size: 7px; line-height: 1.1; }
        
        /* HEADER */
        .company-header { width: 100%; border-bottom: 2px solid #000; margin-bottom: 5px; }
        .company-name { font-size: 9px; font-weight: bold; }
        .report-title { font-size: 11px; font-weight: bold; text-align: center; text-transform: uppercase; }

        /* TABEL */
        table.tbl-data { width: 100%; border-collapse: collapse; }
        table.tbl-data th {
            background-color: #e6e6e6; border: 1px solid #000;
            font-weight: bold; text-align: center; vertical-align: middle; padding: 3px 1px;
        }
        table.tbl-data td {
            border: 1px solid #000; vertical-align: middle; padding: 2px 1px; text-align: center;
        }

        /* Utility */
        .text-left { text-align: left !important; padding-left: 3px !important; }
        .bg-ok { color: #006400; font-weight: bold; }
        .bg-rev { color: #8B0000; font-weight: bold; }
    </style>
</head>
<body>

    <table class="company-header" cellpadding="2">
        <tr>
            <td width="30%" class="company-name">
                PT Charoen Pokphand Indonesia<br>
                <span style="font-weight: normal; font-size: 7px;">Food Division</span>
            </td>
            <td width="40%" class="report-title">LAPORAN SAMPLING FINISH GOOD</td>
            <td width="30%" style="text-align: right; font-size: 7px;">
                <strong>Dicetak:</strong> {{ date('d-m-Y H:i') }}<br>
                <strong>Oleh:</strong> {{ Auth::user()->username ?? 'System' }}
            </td>
        </tr>
    </table>

    <table width="100%" cellpadding="1" style="margin-bottom: 5px; font-size: 7px;">
        <tr>
            <td width="15%"><strong>Filter Tanggal</strong></td>
            <td width="35%">: {{ $request->date ? \Carbon\Carbon::parse($request->date)->format('d-m-Y') : 'SEMUA' }}</td>
            <td width="15%"><strong>Filter Shift</strong></td>
            <td width="35%">: {{ $request->shift ? $request->shift : 'SEMUA' }}</td>
        </tr>
    </table>

    {{-- TABEL DATA --}}
    {{-- Total Width harus 100%. Saya hitung estimasinya agar muat. --}}
    <table class="tbl-data" nobr="true">
        <thead>
            <tr>
                <th width="3%" rowspan="2">No</th>
                <th width="6%" rowspan="2">Tgl</th>
                <th width="3%" rowspan="2">Shf</th>
                <th width="4%" rowspan="2">Plt</th>
                <th width="10%" rowspan="2">Varian</th>
                <th width="7%" rowspan="2">Kode<br>Batch</th>
                <th width="6%" rowspan="2">Exp</th>
                
                {{-- Group: Pemeriksaan Cartoning --}}
                <th width="16%" colspan="4">Pemeriksaan Cartoning</th>
                
                <th width="5%" rowspan="2">Isi<br>/Box</th>
                <th width="4%" rowspan="2">Jml<br>Box</th>
                
                {{-- Group: Status Varian --}}
                <th width="12%" colspan="3">Status Varian</th>
                
                <th width="6%" rowspan="2">Item<br>Mutu</th>
                <th width="7%" rowspan="2">Catatan</th>
                <th width="4%" rowspan="2">QC</th>
                <th width="3%" rowspan="2">Koord</th>
                <th width="4%" rowspan="2">Status</th>
            </tr>
            <tr>
                {{-- Sub Header Cartoning --}}
                <th width="4%">Jam</th>
                <th width="4%">Kalib</th>
                <th width="4%">Berat</th>
                <th width="4%">Ket</th>
                
                {{-- Sub Header Status --}}
                <th width="4%">Rls</th>
                <th width="4%">Rjc</th>
                <th width="4%">Hld</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $index => $item)
            <tr nobr="true">
                <td width="3%">{{ $index + 1 }}</td>
                <td width="6%">{{ \Carbon\Carbon::parse($item->date)->format('d-m-y') }}</td>
                <td width="3%">{{ $item->shift }}</td>
                <td width="4%">{{ $item->palet }}</td>
                <td width="10%" class="text-left">{{ $item->nama_produk }}</td>
                <td width="7%">{{ $item->kode_produksi }}</td>
                <td width="6%">{{ \Carbon\Carbon::parse($item->exp_date)->format('d-m-y') }}</td>
                
                {{-- Cartoning Data --}}
                <td width="4%">{{ \Carbon\Carbon::parse($item->pukul)->format('H:i') }}</td>
                <td width="4%" style="font-family: zapfdingbats;">
                    {{ $item->kalibrasi == 'Sesuai' ? '4' : '8' }}
                </td>
                <td width="4%">{{ $item->berat_produk }}</td>
                <td width="4%" style="font-size: 6px;">{{ $item->keterangan }}</td>
                
                <td width="5%">{{ $item->isi_per_box }}</td>
                <td width="4%">{{ $item->jumlah_box }}</td>
                
                {{-- Status Varian --}}
                <td width="4%">{{ $item->release }}</td>
                <td width="4%">{{ $item->reject }}</td>
                <td width="4%">{{ $item->hold }}</td>
                
                <td width="6%" style="font-size: 6px;">{{ $item->item_mutu }}</td>
                <td width="7%" class="text-left" style="font-size: 6px;">{{ $item->catatan }}</td>
                <td width="4%">{{ $item->username }}</td>
                <td width="3%">{{ $item->nama_koordinator }}</td>
                <td width="4%">
                    @if($item->status_spv == 1) <span class="bg-ok">OK</span>
                    @elseif($item->status_spv == 2) <span class="bg-rev">REV</span>
                    @else - @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="22" style="padding: 10px;">Data tidak ditemukan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Footer Sign --}}
    <table width="100%" style="margin-top: 15px; page-break-inside: avoid;">
        <tr>
            <td width="70%">
                <div style="font-size: 6px; font-style: italic;">Form Sampling FG - Generated by System</div>
            </td>
            <td width="30%" align="center">
                <div style="font-size: 8px;">Disetujui Oleh,<br>QC Supervisor</div>
                <br><br><br>
                <div style="border-bottom: 1px solid #000; width: 80%;"></div>
            </td>
        </tr>
    </table>

</body>
</html>