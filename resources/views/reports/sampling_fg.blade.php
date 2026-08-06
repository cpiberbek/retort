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
        /* tnr */
        body,
        table,
        tr,
        td,
        th {
            font-family: times;
            font-size: 8pt;
        }
    </style>
</head>
<body>

    <div style="margin-left:-30px;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td width="55">
                    <img src="{{ public_path('assets/img/Logo CPI.png') }}" width="50">
                </td>
                <td>
                    <span style="font-size:12pt;"><b>PT Charoen </b></span><br>
                    <span style="font-size:12pt;"><b>Pokphand Indonesia</b></span><br>
                    <span style="font-size:12pt;"><b>Food Division</b></span>
                </td>
            </tr>
        </table>
    </div>

    <table width="100%" border="0" cellpadding="3" cellspacing="0">
        <tr>
            <td width="18%">
            </td>
            <td width="64%" align="center" style="font-size:12pt;"><b>PEMERIKSAAN STUFFING SOSIS RETORT</b></td>
            <td width="18%"></td>
        </tr>
    </table>

    <table width="100%" cellpadding="1" style="margin-bottom: 5px; font-size: 7px;">
        <tr>
            <td width="15%"><strong>Filter Tanggal: {{ $request->date ? \Carbon\Carbon::parse($request->date)->format('d-m-Y') : 'SEMUA' }}</strong></td>
            <td width="35%"></td>
            <td width="15%"><strong>Filter Shift: {{ $request->shift ? $request->shift : 'Semua Shift' }}</strong></td>
            <td width="35%"></td>
        </tr>
    </table>

    {{-- TABEL DATA --}}
    {{-- Total Width harus 100%. Saya hitung estimasinya agar muat. --}}
    <table class="tbl-data" nobr="true">
        <thead>
            <tr>
                {{-- <th width="3%" rowspan="2">No</th>
                <th width="6%" rowspan="2">Tgl</th> --}}
                {{-- <th width="3%" rowspan="2">Shf</th> --}}
                <th width="4%" rowspan="2">Palet</th>
                <th width="20%" rowspan="2">Nama Produk</th>
                <th width="20%" rowspan="2">Kode<br>Produksi</th>
                <th width="6%" rowspan="2">Tanggal<br>Expired</th>
                
                {{-- Group: Pemeriksaan Cartoning --}}
                <th width="24%" colspan="4">Pemeriksaan Cartoning</th>
                
                <th width="8%" rowspan="2">Isi Produk<br>per Box</th>
                <th width="6%" rowspan="2">Jumlah<br>(Box)</th>
                
                {{-- Group: Status Varian --}}
                <th width="12%" colspan="3">Status Produk</th>
                
                {{-- <th width="6%" rowspan="2">Item<br>Mutu</th>
                <th width="7%" rowspan="2">Catatan</th>
                <th width="4%" rowspan="2">QC</th>
                <th width="3%" rowspan="2">Koord</th>
                <th width="4%" rowspan="2">Status</th> --}}
            </tr>
            <tr>
                {{-- Sub Header Cartoning --}}
                <th width="6%">Waktu</th>
                <th width="6%">Kalib</th>
                <th width="6%">Berat</th>
                <th width="6%">Keterangan</th>
                
                {{-- Sub Header Status --}}
                <th width="4%">Release</th>
                <th width="4%">Reject</th>
                <th width="4%">Hold</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $index => $item)
            <tr nobr="true">
                {{-- <td width="3%">{{ $index + 1 }}</td>
                <td width="6%">{{ \Carbon\Carbon::parse($item->date)->format('d-m-y') }}</td> --}}
                {{-- <td width="3%">{{ $item->shift }}</td> --}}
                <td width="4%">{{ $item->palet }}</td>
                <td width="20%" class="text-left">{{ $item->nama_produk }}</td>
                <td width="20%">
                    {{ \Illuminate\Support\Str::isUuid($item->kode_produksi)
                        ? (\App\Models\Mincing::where('uuid', $item->kode_produksi)->value('kode_produksi') ?? $item->kode_produksi)
                        : $item->kode_produksi }}
                </td>
                <td width="6%">{{ \Carbon\Carbon::parse($item->exp_date)->format('d-m-y') }}</td>
                
                {{-- Cartoning Data --}}
                <td width="6%">{{ \Carbon\Carbon::parse($item->pukul)->format('H:i') }}</td>
                <td width="6%" style="font-family: zapfdingbats;">
                    {{ $item->kalibrasi == 'Sesuai' ? '4' : '8' }}
                </td>
                <td width="6%">{{ $item->berat_produk }}</td>
                <td width="6%" style="font-size: 6px;">{{ $item->keterangan }}</td>
                
                <td width="8%">{{ $item->isi_per_box }}</td>
                <td width="6%">{{ $item->jumlah_box }}</td>
                
                {{-- Status Varian --}}
                <td width="4%">{{ $item->release }}</td>
                <td width="4%">{{ $item->reject }}</td>
                <td width="4%">{{ $item->hold }}</td>
                
                {{-- <td width="6%" style="font-size: 6px;">{{ $item->item_mutu }}</td>
                <td width="7%" class="text-left" style="font-size: 6px;">{{ $item->catatan }}</td>
                <td width="4%">{{ $item->username }}</td>
                <td width="3%">{{ $item->nama_koordinator }}</td>
                <td width="4%">
                    @if($item->status_spv == 1) <span class="bg-ok">OK</span>
                    @elseif($item->status_spv == 2) <span class="bg-rev">REV</span>
                    @else - @endif
                </td> --}}
            </tr>
            @empty
            <tr>
                <td colspan="22" style="padding: 10px;">Data tidak ditemukan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Footer Sign --}}
    @php
        $namaInspector = $items->pluck('username')->filter()->unique()->implode(', ');

        $allApproved = $items->every(function ($item) {
            return !empty($item->nama_spv);
        });

        $namaSpv = $allApproved
            ? $items->pluck('nama_spv')->filter()->unique()->first()
            : 'Belum Semua Entry Disetujui Oleh SPV';
    @endphp

    {{-- Footer Sign --}}
    <table width="100%" style="margin-top: 15px; page-break-inside: avoid;">
        <tr>
            <td width="10%"></td>

            <td width="20%" align="center">
                <div style="font-size: 8px;">Diperiksa Oleh,</div>
                <br><br><br>
                <div style="font-size: 8px; margin-top: 5px;">
                    (<u>{{ $namaInspector }}</u>)
                </div>
                <div style="font-size: 8px;">
                    QC
                </div>
            </td>

            <td width="40%"></td>

            <td width="20%" align="center">
                <div style="font-size: 8px;">Disetujui Oleh,</div>
                <br><br><br>
                <div style="font-size: 8px; margin-top: 5px;">
                    (<u>{{ $namaSpv }}</u>)
                </div>
                <div style="font-size: 8px;">
                    QC Supervisor
                </div>
            </td>

            <td width="10%"></td>
        </tr>
    </table>

</body>
</html>