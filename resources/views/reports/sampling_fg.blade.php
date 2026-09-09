<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Sampling Finish Good</title>
    <style>
        body {
            font-family: times;
            font-size: 8pt;
            line-height: 1.1;
        }

        table,
        tr,
        td,
        th {
            font-family: times;
            font-size: 8pt;
        }

        .tbl-data {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .tbl-data th {
            border: 1px solid #000;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
            padding: 3px 1px;
        }

        .tbl-data td {
            border: 1px solid #000;
            vertical-align: middle;
            padding: 2px 1px;
            text-align: center;
            word-wrap: break-word;
        }

        .text-left {
            text-align: left !important;
            padding-left: 3px !important;
        }

        .bg-ok {
            color: #006400;
            font-weight: bold;
        }

        .bg-rev {
            color: #8B0000;
            font-weight: bold;
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
                    <span style="font-size:12pt;"><b>PT Charoen</b></span><br>
                    <span style="font-size:12pt;"><b>Pokphand Indonesia</b></span><br>
                    <span style="font-size:12pt;"><b>Food Division</b></span>
                </td>
            </tr>
        </table>
    </div>

    <table width="100%" border="0" cellpadding="3" cellspacing="0">
        <tr>
            <td width="18%"></td>
            <td width="64%" align="center" style="font-size:12pt;">
                <b>PEMERIKSAAN PROSES SAMPLING FINISH GOOD</b>
            </td>
            <td width="18%"></td>
        </tr>
    </table>

    <table width="100%" cellpadding="1" cellspacing="0" style="margin-bottom:5px;">
        <tr>
            <td width="15%">
                <strong>
                    Hari/Tgl :
                    {{
                        $request->date
                            ? \Carbon\Carbon::parse($request->date)->locale('id')->translatedFormat('l, d-m-Y')
                            : ''
                    }}
                </strong>
            </td>
            <td width="35%"></td>
            <td width="15%">
                <strong>
                    Shift:
                    {{ $request->shift ? $request->shift : 'Semua Shift' }}
                </strong>
            </td>
            <td width="35%"></td>
        </tr>
    </table>

    <table class="tbl-data" nobr="true">
        <thead>
            <tr>
                <th rowspan="2">Palet</th>
                <th rowspan="2">Nama Produk</th>
                <th rowspan="2">Kode Produksi</th>
                <th rowspan="2">Tanggal<br>Expired</th>

                <th colspan="4">
                    Pemeriksaan Proses Cartoning
                </th>

                <th rowspan="2">
                    Isi produk<br>per box
                </th>

                <th rowspan="2">
                    Jumlah<br>(Box)
                </th>

                <th colspan="3">
                    Status Produk
                </th>

                <th rowspan="2">
                    Keterangan
                </th>
            </tr>

            <tr>
                <th>Waktu</th>
                <th>Kalibrasi</th>
                <th>Berat Produk<br>per Box</th>
                <th>Keterangan</th>

                <th>Release</th>
                <th>Reject</th>
                <th>Hold</th>
            </tr>
        </thead>

        <tbody>
            @forelse($items as $index => $item)
                <tr nobr="true">

                    <td>
                        {{ $item->palet ?? '-' }}
                    </td>

                    <td class="text-left">
                        {{ $item->nama_produk ?? '-' }}
                    </td>

                    <td>
                        {{
                            \Illuminate\Support\Str::isUuid($item->kode_produksi)
                                ? (
                                    \App\Models\Mincing::where(
                                        'uuid',
                                        $item->kode_produksi
                                    )->value('kode_produksi')
                                    ?? $item->kode_produksi
                                )
                                : ($item->kode_produksi ?? '-')
                        }}
                    </td>

                    <td>
                        {{ $item->exp_date ? \Carbon\Carbon::parse($item->exp_date)->format('d-m-y') : '-' }}
                    </td>

                    <td>
                        {{ $item->pukul ? \Carbon\Carbon::parse($item->pukul)->format('H:i') : '-' }}
                    </td>

                    <td style="font-family:zapfdingbats;">
                        {{ $item->kalibrasi == 'Sesuai' ? '4' : '8' }}
                    </td>

                    <td>
                        {{ $item->berat_produk ?? '-' }}
                    </td>

                    <td style="font-size:7px;">
                        {{ $item->keterangan ?? '-' }}
                    </td>

                    <td>
                        {{ $item->isi_per_box ?? '-' }}
                    </td>

                    <td>
                        {{ $item->jumlah_box ?? '-' }}
                    </td>

                    <td>
                        {{ $item->release ?? '-' }}
                    </td>

                    <td>
                        {{ $item->reject ?? '-' }}
                    </td>

                    <td>
                        {{ $item->hold ?? '-' }}
                    </td>

                    <td class="text-left" style="font-size:7px;">
                        {{ $item->catatan ?? '-' }}
                    </td>

                </tr>
            @empty
                <tr>
                    <td colspan="14" style="padding:10px;">
                        Data tidak ditemukan.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @php
        $namaInspector = $items->pluck('username')->filter()->unique()->implode(', ');

        $allApproved = $items->every(function ($item) {
            return !empty($item->nama_spv);
        });

        $namaSpv = $allApproved
            ? $items->pluck('nama_spv')->filter()->unique()->first()
            : 'Belum Semua Entry Disetujui Oleh SPV';
    @endphp

    <table width="100%" style="margin-top:15px; page-break-inside:avoid;">
        <tr>
            <td width="75%"></td>
            <td width="25%" align="right" style="font-style:italic;">
                {{ $noDokumen }}
            </td>
        </tr>

        <tr>
            <td width="10%"></td>

            <td width="20%" align="center">
                <div style="font-size:8px;">
                    Diperiksa Oleh,
                </div>

                <br><br><br>

                <div style="font-size:8px; margin-top:5px;">
                    (<u>{{ $namaInspector ?: '-' }}</u>)
                </div>

                <div style="font-size:8px;">
                    QC
                </div>
            </td>

            <td width="40%"></td>

            <td width="20%" align="center">
                <div style="font-size:8px;">
                    Disetujui Oleh,
                </div>

                <br><br><br>

                <div style="font-size:8px; margin-top:5px;">
                    (<u>{{ $namaSpv }}</u>)
                </div>

                <div style="font-size:8px;">
                    QC Supervisor
                </div>
            </td>

            <td width="10%"></td>
        </tr>
    </table>

</body>
</html>