<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 8px;
        }

        .title {
            font-size: 12px;
            font-weight: bold;
            text-align: center;
        }

        table {
            border-collapse: collapse;
        }

        .tbl-header td {
            padding: 2px;
            font-size: 8px;
        }

        .tbl-main,
        .tbl-main th,
        .tbl-main td {
            border: 0.3px solid #000;
        }

        .tbl-main th {
            font-size: 7px;
            text-align: center;
            vertical-align: middle;
        }

        .tbl-main td {
            vertical-align: middle;
        }

        .center {
            text-align: center;
        }

        .small {
            font-size: 7px;
        }
    </style>
</head>

<body>

<div style="margin:0; padding:0; line-height:1;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0; padding:0;">
        <tr>
            <td width="55" style="padding:0;">
                <img src="{{ public_path('assets/img/Logo CPI.png') }}" width="50">
            </td>
            <td style="padding:0;">
                <span style="font-size:14pt;"><b>PT Charoen</b></span><br>
                <span style="font-size:14pt;"><b>Pokphand Indonesia</b></span><br>
                <span style="font-size:14pt;"><b>Food Division</b></span>
            </td>
        </tr>
    </table>
</div>

<h2 class="title">PEMERIKSAAN PROSES PACKING</h2>

@php
    $firstPacking = $packings->first();

    $dateFilter = request('date')
        ? \Carbon\Carbon::parse(request('date'))->format('d-m-Y')
        : ($firstPacking?->date
            ? \Carbon\Carbon::parse($firstPacking->date)->format('d-m-Y')
            : '________________');

    $shiftFilter = $firstPacking?->shift ?? request('shift') ?? '________________';

    $namaProdukFilter = request('nama_produk')
        ?? $firstPacking?->nama_produk
        ?? '________________';
@endphp

<table width="100%" class="tbl-header">
    <tr>
        <td width="32%">
            Hari / Tgl : {{ $dateFilter }}
        </td>
        <td width="36%">
            Shift : {{ $shiftFilter }}
        </td>
        <td width="32%" align="right">
            Nama Varian: {{ $namaProdukFilter }}
        </td>
    </tr>
</table>

<table width="100%" class="tbl-main small">
    <tr>
        <th rowspan="2" class="center">Waktu</th>
        <th rowspan="2" class="center">Kalibrasi</th>
        <th rowspan="2" class="center">QR Code</th>
        <th colspan="2" class="center">Kode Produk</th>
        <th colspan="2" class="center">Shrinkle Tunnel</th>
        <th rowspan="2" class="center">Kondisi Segel /<br>Seal</th>
        <th rowspan="2" class="center">Jumlah produk<br>per pack (pcs)</th>
        <th rowspan="2" class="center">Berat produk<br>per pcs (gr)</th>
        <th rowspan="2" class="center">Berat<br>Produk Per<br>Pack (gr)</th>
        <th colspan="4" class="center">Data Kemasan</th>
        <th colspan="2" class="center">Paraf</th>
        <th rowspan="2" class="center">Keterangan</th>
    </tr>

    <tr>
        <th class="center">Printing Label</th>
        <th class="center">Toples</th>
        <th class="center">Suhu</th>
        <th class="center">Speed<br>Conveyor</th>
        <th class="center">Jenis<br>Kemasan</th>
        <th class="center">No. Lot<br>Kemasan</th>
        <th class="center">Tgl<br>Kedatangan</th>
        <th class="center">Nama<br>Supplier</th>
        <th class="center">QC</th>
        <th class="center">Produksi</th>
    </tr>

    @forelse($packings as $packing)
        @php
    $dataKemasan = $packing->data_kemasan;

    if (is_string($dataKemasan)) {
        $dataKemasan = json_decode($dataKemasan, true);
    }

    $kemasan = is_array($dataKemasan)
        ? ($dataKemasan[0] ?? [])
        : [];

    $jenisKemasan = $kemasan['jenis_kemasan'] ?? '-';
    $noLot = $kemasan['no_lot_kemasan'] ?? '-';
    $tglKedatangan = $kemasan['tgl_kedatangan'] ?? '-';
    $namaSupplier = $kemasan['nama_supplier'] ?? '-';

    if (is_array($jenisKemasan)) {
        $jenisKemasan = implode(', ', $jenisKemasan);
    }

    if (is_array($noLot)) {
        $noLot = implode(', ', $noLot);
    }

    if (is_array($tglKedatangan)) {
        $tglKedatangan = implode(', ', $tglKedatangan);
    }

    if (is_array($namaSupplier)) {
        $namaSupplier = implode(', ', $namaSupplier);
    }

    if ($tglKedatangan !== '-' && $tglKedatangan !== '') {
        try {
            $tglKedatangan = \Carbon\Carbon::parse($tglKedatangan)->format('d-m-Y');
        } catch (\Throwable $e) {
            $tglKedatangan = '-';
        }
    }
@endphp

        <tr>
            <td class="center">
                {{ $packing->waktu ? \Carbon\Carbon::parse($packing->waktu)->format('H:i') : '-' }}
            </td>

            <td class="center">
                {{ $packing->kalibrasi === 'Ok'
                    ? 'V'
                    : ($packing->kalibrasi === 'Tidak Ok' ? 'X' : '-') }}
            </td>

            <td class="center">
                @if($packing->qrcode)
                    <img src="{{ public_path($packing->qrcode) }}" width="60">
                @else
                    -
                @endif
            </td>

            <td class="center">
                @if($packing->kode_printing)
                    <img src="{{ public_path($packing->kode_printing) }}" width="60">
                @else
                    -
                @endif
            </td>

            <td class="center">
                {{ \App\Models\Mincing::where('uuid', $packing->kode_toples)->value('kode_produksi') ?? $packing->kode_toples ?? '-' }}
            </td>

            <td class="center">
                {{ is_array($packing->suhu)
                    ? implode(', ', $packing->suhu)
                    : ($packing->suhu ?? '-') }}
            </td>

            <td class="center">
                {{ $packing->speed ?? '-' }}
            </td>

            <td class="center">
                {{ $packing->kondisi_segel ?? '-' }}
            </td>

            <td class="center">
                {{ $packing->jumlah_produk ?? '-' }}
            </td>

            <td class="center">
                {{ $packing->berat_pcs ?? '-' }}
            </td>

            <td class="center">
                {{ $packing->berat_pack ?? '-' }}
            </td>

            <td class="center">
                {{ $jenisKemasan }}
            </td>

            <td class="center">
                {{ $noLot }}
            </td>

            <td class="center">
                {{ $tglKedatangan ?: '-' }}
            </td>

            <td class="center">
                {{ $namaSupplier }}
            </td>

            <td class="center">
                {{ \App\Models\User::where('username', $packing->username)->value('name') ?? $packing->username ?? '-' }}
            </td>

            <td class="center">
                {{ \App\Models\User::where('username', $packing->nama_produksi)->value('name') ?? $packing->nama_produksi ?? '-' }}
            </td>

            <td class="center">
                {{ $packing->keterangan ?? '-' }}
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="18" class="center">
                Tidak ada data packing
            </td>
        </tr>
    @endforelse
</table>

<div style="margin-top:5px; text-align:right; font-style:italic;">
    {{ $noDokumen ?? '-' }}
</div>

<br>

<table width="100%" class="small">
    <tr>
        <td width="50%">
            Ket :<br>
            OK : V<br>
            Tidak OK : X
        </td>

        <td width="50%" align="center">
            Disetujui Oleh
            <br><br><br><br>
            ({{ $firstPacking?->nama_spv ?? '-' }})<br>
            QC SPV
        </td>
    </tr>
</table>

</body>
</html>
