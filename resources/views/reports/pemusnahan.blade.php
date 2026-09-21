<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pemusnahan</title>

    <style>
        body {
            font-family: helvetica, sans-serif;
            font-size: 8px;
            margin: 0;
            padding: 0;
        }

        .header {
            width: 100%;
            margin-bottom: 2px;
        }

        .company-header {
            width: 100%;
            margin-bottom: 2px;
        }
        .company-name { font-size: 10px; font-weight: bold; }
        .report-title {
            font-size: 12px;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
        }

        .company {
            font-size: 8px;
            font-weight: bold;
            font-style: italic;
            line-height: 1.2;
        }

        .division {
            font-size: 8px;
            font-weight: normal;
        }

        .title {
            font-size: 13px;
            font-weight: bold;
            text-align: center;
        }

        .tanggal {
            width: 100%;
            font-size: 8px;
            margin-bottom: 2px;
        }

        .tbl-data {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .tbl-data th,
        .tbl-data td {
            font-size: 8px;
            vertical-align: middle;
            padding: 3px 3px;
            text-align: center;
            border: 0.5px solid #000;
        }

        .tbl-data th {
            font-size: 8px;
            font-weight: normal;
            text-align: center;
            vertical-align: middle;
            padding: 3px 2px;
        }

        .tbl-data td {
            font-size: 8px;
            vertical-align: middle;
            padding: 3px 3px;
        }

        .center {
            text-align: center;
        }

        .left {
            text-align: left;
        }

        .signature {
            width: 100%;
            margin-top: 4px;
            page-break-inside: avoid;
        }

        .signature td {
            text-align: center;
            vertical-align: top;
            font-size: 8px;
        }

        .qt {
            text-align: right;
            font-size: 8px;
            font-style: italic;
            margin-top: 1px;
        }
    </style>
</head>

<body>

    <table class="company-header" cellpadding="2">
        <tr>
            <td width="30%" class="company-name">
                PT Charoen Pokphand Indonesia<br>
                <span style="font-weight: normal; font-size: 8px;">Food Division</span>
            </td>
            <td width="40%" class="report-title">PEMUSNAHAN BARANG / PRODUK</td>
            <td width="30%" style="text-align: right; font-size: 8px;">
            </td>
        </tr>
    </table>

    <br><br>
    {{-- TANGGAL --}}
    <table class="tanggal" cellpadding="0" cellspacing="0">
        <tr>
            <td width="15%">
                HARI/TANGGAL :
            </td>

            <td width="85%">
                {{ $request->date
                    ? \Carbon\Carbon::parse($request->date)->format('d-m-Y')
                    : '-' }}
            </td>
        </tr>
    </table>

    {{-- DATA --}}
    <table class="tbl-data" cellpadding="0" cellspacing="0" nobr="true">
        <thead>
            <tr>
                <th width="6%">No.</th>
                <th width="18%">Nama Produk</th>
                <th width="18%">Kode Produksi</th>
                <th width="13%">Kode Exp.</th>
                <th width="25%">Analisa Masalah</th>
                <th width="20%">Keterangan</th>
            </tr>
        </thead>

        <tbody>

            @forelse($items as $index => $item)

                @php
                    $kodeProduksi = \App\Models\Mincing::where(
                        'uuid',
                        $item->kode_produksi
                    )->value('kode_produksi');
                @endphp

                <tr nobr="true">

                    {{-- NO --}}
                    <td width="6%" class="center">
                        {{ $index + 1 }}
                    </td>

                    {{-- NAMA PRODUK --}}
                    <td width="18%">
                        {{ $item->nama_produk ?? '-' }}
                    </td>

                    {{-- KODE PRODUKSI --}}
                    <td width="18%" class="center">
                        {{ $kodeProduksi ?? '-' }}
                    </td>

                    {{-- KODE EXP --}}
                    <td width="13%" class="center">
                        @if($item->expired_date)
                            {{ \Carbon\Carbon::parse($item->expired_date)->format('d-m-Y') }}
                        @else
                            -
                        @endif
                    </td>

                    {{-- ANALISA MASALAH --}}
                    <td width="25%">
                        {{ $item->analisa ?? '-' }}
                    </td>

                    {{-- KETERANGAN --}}
                    <td width="20%">
                        {{ $item->keterangan ?? '-' }}
                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="6" class="center" style="padding: 8px;">
                        Data tidak ditemukan.
                    </td>
                </tr>

            @endforelse

        </tbody>
    </table>

    {{-- NOMOR DOKUMEN --}}
    <div class="qt">
        QT 35 / 00
    </div>

    {{-- STATUS SPV --}}
    @php
        $semuaDisetujui = $items->isNotEmpty()
            && $items->every(fn($item) => (int) $item->status_spv === 1);

        $usernameQc = $items->first()->username ?? '-';

        $namaSpv = $semuaDisetujui
            ? ($items->first()->nama_spv ?? '-')
            : 'Belum semua entry disetujui SPV';
    @endphp

    {{-- TANDA TANGAN --}}
    <table class="signature" cellpadding="0" cellspacing="0">
        <tr>

            {{-- QC --}}
            <td width="50%">
                Diperiksa oleh,
                <br><br><br><br>

                <u>{{ $usernameQc }}</u>
                <br>
                QC
            </td>

            {{-- SPV --}}
            <td width="50%">
                Disetujui oleh,
                <br><br><br><br>

                <u>{{ $namaSpv }}</u>
                <br>
                QC SPV
            </td>

        </tr>
    </table>

</body>
</html>