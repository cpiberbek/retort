<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Washing - Drying</title>
    <style>
        body { font-family: helvetica, sans-serif; font-size: 6px; line-height: 1.1; }
        
        /* Header */
        .company-header { width: 100%; border-bottom: 2px solid #000; margin-bottom: 5px; }
        .company-name { font-size: 9px; font-weight: bold; }
        .report-title { font-size: 11px; font-weight: bold; text-align: center; text-transform: uppercase; }

        /* Tabel Data */
        table.tbl-data { width: 100%; border-collapse: collapse; }
        table.tbl-data th {
            background-color: #e6e6e6; border: 1px solid #000;
            font-weight: bold; text-align: center; vertical-align: middle; padding: 2px 1px;
        }
        table.tbl-data td {
            border: 1px solid #000; vertical-align: middle; padding: 2px 1px; text-align: center;
        }

        /* Utility */
        .text-left { text-align: left !important; padding-left: 2px !important; }
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
            <td width="40%" class="report-title">PEMERIKSAAN WASHING - DRYING</td>
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

    <table class="tbl-data" nobr="true">
        <thead>
            <tr>
                <th width="2%" rowspan="2">No</th>
                <th width="5%" rowspan="2">Tgl</th>
                <th width="2%" rowspan="2">Shf</th>
                <th width="8%" rowspan="2">Varian</th>
                <th width="5%" rowspan="2">Kode</th>
                <th width="3%" rowspan="2">Jam</th>
                
                {{-- Group: Fisik --}}
                <th width="6%" colspan="2">Dimensi (mm)</th>
                <th width="14%" colspan="6">Visual Check</th>
                
                {{-- Group: PC Kleer --}}
                <th width="13%" colspan="5">Kondisi PC Kleer</th>
                
                {{-- Group: Pottasium --}}
                <th width="11%" colspan="4">Pottasium Sorbate</th>
                
                {{-- Group: Mesin --}}
                <th width="3%" rowspan="2">Suhu<br>Htr</th>
                <th width="8%" colspan="4">Speed Conveyor</th>
                
                <th width="8%" rowspan="2">Catatan</th>
                <th width="6%" rowspan="2">Check</th>
                <th width="3%" rowspan="2">QC</th>
                <th width="3%" rowspan="2">Sts</th>
            </tr>
            <tr>
                {{-- Sub Header Dimensi --}}
                <th width="3%">Pjg</th>
                <th width="3%">Dia</th>
                
                {{-- Sub Header Visual --}}
                <th width="2%">Air</th>
                <th width="2%">Lkt</th>
                <th width="2%">Sisa</th>
                <th width="3%">Bcr</th>
                <th width="3%">Seal</th>
                <th width="2%">Prt</th>
                
                {{-- Sub Header PC Kleer --}}
                <th width="3%">Kons</th>
                <th width="2%">T1</th>
                <th width="2%">T2</th>
                <th width="2%">pH</th>
                <th width="4%">Air</th>
                
                {{-- Sub Header Pottasium --}}
                <th width="3%">Kons</th>
                <th width="2%">T</th>
                <th width="2%">pH</th>
                <th width="4%">Air</th>
                
                {{-- Sub Header Speed --}}
                <th width="2%">1</th>
                <th width="2%">2</th>
                <th width="2%">3</th>
                <th width="2%">4</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $index => $item)
            <tr nobr="true">
                <td>{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($item->date)->format('d-m') }}</td>
                <td>{{ $item->shift }}</td>
                <td class="text-left">{{ $item->nama_produk }}</td>
                <td>{{ $item->kode_produksi }}</td>
                <td>{{ \Carbon\Carbon::parse($item->pukul)->format('H:i') }}</td>
                
                {{-- Dimensi --}}
                <td>{{ $item->panjang_produk ?? '-' }}</td>
                <td>{{ $item->diameter_produk ?? '-' }}</td>
                
                {{-- Visual Check (Simbol) --}}
                <td style="font-family: zapfdingbats;">{{ !empty($item->airtrap) ? '4' : '-' }}</td>
                <td style="font-family: zapfdingbats;">{{ !empty($item->lengket) ? '4' : '-' }}</td>
                <td style="font-family: zapfdingbats;">{{ !empty($item->sisa_adonan) ? '4' : '-' }}</td>
                <td style="font-family: zapfdingbats;">{{ !empty($item->kebocoran) ? '4' : '-' }}</td>
                <td style="font-family: zapfdingbats;">{{ !empty($item->kekuatan_seal) ? '4' : '-' }}</td>
                <td style="font-family: zapfdingbats;">{{ !empty($item->print_kode) ? '4' : '-' }}</td>
                
                {{-- PC Kleer --}}
                <td>{{ $item->konsentrasi_pckleer ?? '-' }}</td>
                <td>{{ $item->suhu_pckleer_1 ?? '-' }}</td>
                <td>{{ $item->suhu_pckleer_2 ?? '-' }}</td>
                <td>{{ $item->ph_pckleer ?? '-' }}</td>
                <td>{{ $item->kondisi_air_pckleer ?? '-' }}</td>
                
                {{-- Pottasium --}}
                <td>{{ $item->konsentrasi_pottasium ?? '-' }}</td>
                <td>{{ $item->suhu_pottasium ?? '-' }}</td>
                <td>{{ $item->ph_pottasium ?? '-' }}</td>
                <td>{{ $item->kondisi_pottasium ?? '-' }}</td>
                
                {{-- Mesin --}}
                <td>{{ $item->suhu_heater ?? '-' }}</td>
                <td>{{ $item->speed_1 ?? '-' }}</td>
                <td>{{ $item->speed_2 ?? '-' }}</td>
                <td>{{ $item->speed_3 ?? '-' }}</td>
                <td>{{ $item->speed_4 ?? '-' }}</td>
                
                <td class="text-left" style="font-size: 5px;">{{ $item->catatan ?? '-' }}</td>
                
                {{-- Check Logic (Simplifikasi tampilan) --}}
                <td style="font-size: 5px;">
                    @if($item->konsentrasi_pckleer >= 0.7 && $item->konsentrasi_pckleer <= 1.0) OK @else - @endif
                </td>

                <td>{{ $item->username }}</td>
                <td>
                    @if($item->status_spv == 1) <span class="bg-ok">OK</span>
                    @elseif($item->status_spv == 2) <span class="bg-rev">REV</span>
                    @else - @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="30" style="padding: 10px;">Data tidak ditemukan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    {{-- Info Standar (Footer Tabel) --}}
    <table width="100%" style="font-size: 5px; margin-top: 2px;">
        <tr>
            <td width="10%"><strong>Standar:</strong></td>
            <td width="30%">Suhu PC Kleer: 46±3 °C</td>
            <td width="30%">Suhu Heater: 125 - 135 °C</td>
            <td width="30%" align="right">QT 17 / 00</td>
        </tr>
        <tr>
            <td></td>
            <td>Kons. PC Kleer: 0.7% (ayam); 1% (sapi); 0.8% (cuci)</td>
            <td colspan="2">Kons. Pottasium Sorbate: 0.15%</td>
        </tr>
    </table>

    {{-- Tanda Tangan --}}
    <table width="100%" style="margin-top: 15px; page-break-inside: avoid;">
        <tr>
            <td width="70%"></td>
            <td width="30%" align="center">
                <div style="font-size: 8px;">Disetujui Oleh,<br>QC Supervisor</div>
                <br><br><br>
                <div style="border-bottom: 1px solid #000; width: 80%;"></div>
            </td>
        </tr>
    </table>

</body>
</html>