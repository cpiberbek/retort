<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pemusnahan</title>
    <style>
        body { font-family: helvetica, sans-serif; font-size: 9pt; }
        
        .company-header {
            width: 100%;
            border-bottom: 2px solid #000;
            margin-bottom: 10px;
        }
        .company-name { font-size: 10px; font-weight: bold; }
        .report-title {
            font-size: 12px;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
        }

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
            vertical-align: middle;
            padding: 4px;
        }

        .text-center { text-align: center !important; }
        .text-left { text-align: left !important; }
        .bg-ok { color: #006400; font-weight: bold; }
        .bg-rev { color: #8B0000; font-weight: bold; }
    </style>
</head>
<body>

    <table class="company-header" cellpadding="2">
        <tr>
            <td width="30%" class="company-name">
                PT Charoen Pokphand Indonesia<br>
                <span style="font-weight: normal; font-size: 8px;">Food Division</span>
            </td>
            <td width="40%" class="report-title">PEMUSNAHAN BARANG / VARIAN</td>
            <td width="30%" style="text-align: right; font-size: 8px;">
                <strong>Dicetak:</strong> {{ date('d-m-Y H:i') }}<br>
                <strong>Oleh:</strong> {{ Auth::user()->username ?? 'System' }}
            </td>
        </tr>
    </table>

    <table width="100%" cellpadding="2" style="margin-bottom: 8px; font-size: 8px;">
        <tr>
            <td width="15%"><strong>Filter Tanggal</strong></td>
            <td width="35%">: {{ $request->date ? \Carbon\Carbon::parse($request->date)->format('d-m-Y') : 'SEMUA' }}</td>
        </tr>
    </table>

    <table class="tbl-data" nobr="true">
        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="8%">Date</th>
                <th width="15%">Nama Varian</th>
                <th width="10%">Kode Batch</th>
                <th width="8%">Expired Date</th>
                <th width="17%">Analisa</th>
                <th width="17%">Keterangan</th>
                <th width="7%">QC</th>
                <th width="7%">SPV</th>
                <th width="7%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $index => $item)
            <tr nobr="true">
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }}</td>
                <td>{{ $item->nama_produk }}</td>
                <td class="text-center">{{ $item->kode_produksi }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($item->expired_date)->format('d-m-Y') }}</td>
                <td>{{ $item->analisa ?? '-' }}</td>
                <td>{{ $item->keterangan ?? '-' }}</td>
                <td class="text-center">{{ $item->username }}</td>
                <td class="text-center">{{ $item->nama_spv ?? '-' }}</td>
                <td class="text-center">
                    @if($item->status_spv == 1) <span class="bg-ok">VERIF</span>
                    @elseif($item->status_spv == 2) <span class="bg-rev">REV</span>
                    @else - @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="10" class="text-center" style="padding: 10px;">Data tidak ditemukan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Footer Tanda Tangan --}}
    <table width="100%" style="margin-top: 20px; page-break-inside: avoid;">
        <tr>
            <td width="70%"></td>
            <td width="30%" align="center">
                <div style="font-size: 9px;">Disetujui Oleh,<br>QC Supervisor</div>
                <br><br><br>
                <div style="border-bottom: 1px solid #000; width: 80%;"></div>
            </td>
        </tr>
    </table>

</body>
</html>