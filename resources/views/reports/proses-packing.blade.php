<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 7px;
            color: #000;
        }

        table {
            border-collapse: collapse;
        }

        .title {
            font-size: 13px;
            font-weight: bold;
            text-align: center;
        }

        .tbl-header td {
            padding: 0;
            font-size: 8px;
            line-height: 1;
        }

        .tbl-main th,
        .tbl-main td {
            padding: 3px 1px;
            text-align: center;
            vertical-align: middle;
            border: 0.8px solid #000;
        }

        .tbl-main th {
            background: #d9e2f3;
            font-weight: bold;
            font-size: 6px;
            line-height: 1.15;
        }

        .tbl-main td {
            font-size: 6.5px;
            line-height: 1.2;
        }

        .note td {
            vertical-align: top;
            line-height: 1.4;
            font-size: 7px;
        }
    </style>
</head>

<body>

@php
    $namaPemeriksa = (!empty($pemeriksa) && $pemeriksa !== '-') ? $pemeriksa : '';
    $namaSpv       = ($semuaDisetujui && !empty($spv) && $spv !== '-') ? $spv : '';

    $makeQr = function ($nama, $tanggal) {
        if (empty($nama)) {
            return '';
        }
        $teks = 'Ditandatangani secara digital oleh ' . $nama;
        if (!empty($tanggal)) {
            $teks .= ' pada ' . $tanggal;
        }
        $barcode = new \TCPDF2DBarcode($teks, 'QRCODE,H');
        return base64_encode($barcode->getBarcodePngData(4, 4, [0, 0, 0]));
    };

    $qrPemeriksa = $makeQr($namaPemeriksa, $tglPemeriksa ?? '');
    $qrSpv       = $makeQr($namaSpv, $tglSpv ?? '');

    $firstPacking = $packings->first();

    $dateFilter = request('date')
        ? \Carbon\Carbon::parse(request('date'))->format('d-m-Y')
        : ($firstPacking?->date
            ? \Carbon\Carbon::parse($firstPacking->date)->format('d-m-Y')
            : '');

    $shiftFilter      = $firstPacking?->shift ?? request('shift') ?? '';
    $namaProdukFilter = request('nama_produk') ?? $firstPacking?->nama_produk ?? '';

    $rows = [];
    foreach ($packings as $packing) {
        $dataKemasan = $packing->data_kemasan;

        if (is_string($dataKemasan)) {
            $dataKemasan = json_decode($dataKemasan, true);
        }

        $kemasan = is_array($dataKemasan) ? ($dataKemasan[0] ?? []) : [];

        $flat = function ($value) {
            if (is_array($value)) {
                $value = implode(', ', $value);
            }
            return ($value === null || $value === '') ? '-' : $value;
        };

        $tglKedatangan = $flat($kemasan['tgl_kedatangan'] ?? '-');

        if ($tglKedatangan !== '-') {
            try {
                $tglKedatangan = \Carbon\Carbon::parse($tglKedatangan)->format('d-m-Y');
            } catch (\Throwable $e) {
                $tglKedatangan = '-';
            }
        }

        $suhu = is_array($packing->suhu)
            ? implode(', ', $packing->suhu)
            : ($packing->suhu ?? '-');

        $rows[] = [
            'p'       => $packing,
            'jenis'   => $flat($kemasan['jenis_kemasan'] ?? '-'),
            'lot'     => $flat($kemasan['no_lot_kemasan'] ?? '-'),
            'tgl'     => $tglKedatangan,
            'supplier'=> $flat($kemasan['nama_supplier'] ?? '-'),
            'suhu'    => $suhu,
        ];
    }

    $pages = count($rows) > 0 ? array_chunk($rows, 10) : [[]];
@endphp

@foreach($pages as $pageRows)

    <div style="margin-left:-30px; margin-bottom:0;">
        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0;">
            <tr>
                <td width="55" style="padding:0;">
                    <img src="{{ public_path('assets/img/Logo CPI.png') }}" width="50">
                </td>
                <td style="padding:0; line-height:1;">
                    <span style="font-size:14pt;"><b>PT Charoen</b></span><br>
                    <span style="font-size:14pt;"><b>Pokphand Indonesia</b></span><br>
                    <span style="font-size:14pt;"><b>Food Division</b></span>
                </td>
            </tr>
        </table>
    </div>

    <h2 class="title" style="margin:0; padding:0;">
        PEMERIKSAAN PROSES PACKING
    </h2>
    <br>
    <br>

    <table width="100%" class="tbl-header" cellpadding="0" cellspacing="0">
        <tr>
            <td width="33%" align="left">Hari / Tgl : {{ $dateFilter }}</td>
            <td width="33%" align="center">Shift : {{ $shiftFilter }}</td>
            <td width="34%" align="right">Nama Varian : {{ $namaProdukFilter }}</td>
        </tr>
    </table>

    <br>

    <table width="100%" class="tbl-main" cellpadding="0" cellspacing="0">
        <tr>
            <th rowspan="2" width="4%"><b>Waktu</b></th>
            <th rowspan="2" width="4%"><b>Kalibrasi</b></th>
            <th rowspan="2" width="6%"><b>QR Code</b></th>
            <th colspan="2" width="12%"><b>Kode Produk</b></th>
            <th colspan="2" width="8.5%"><b>Shrinkle Tunnel</b></th>
            <th rowspan="2" width="5.5%"><b>Kondisi Segel / Seal</b></th>
            <th rowspan="2" width="5%"><b>Jumlah produk<br>per pack (pcs)</b></th>
            <th rowspan="2" width="4.5%"><b>Berat produk<br>per pcs (gr)</b></th>
            <th rowspan="2" width="4.5%"><b>Berat Produk<br>Per Pack (gr)</b></th>
            <th colspan="4" width="25.5%"><b>Data Kemasan</b></th>
            <th colspan="2" width="12%"><b>Paraf</b></th>
            <th rowspan="2" width="8.5%"><b>Keterangan</b></th>
        </tr>
        <tr>
            <th width="6%"><b>Printing Label</b></th>
            <th width="6%"><b>Toples</b></th>
            <th width="4%"><b>Suhu</b></th>
            <th width="4.5%"><b>Speed<br>Conveyor</b></th>
            <th width="6.5%"><b>Jenis<br>Kemasan</b></th>
            <th width="6.5%"><b>No. Lot<br>Kemasan</b></th>
            <th width="6%"><b>Tgl<br>Kedatangan</b></th>
            <th width="6.5%"><b>Nama<br>Supplier</b></th>
            <th width="6%"><b>QC</b></th>
            <th width="6%"><b>Produksi</b></th>
        </tr>

        @forelse($pageRows as $row)
            @php $packing = $row['p']; @endphp
        <tr>
            <td width="4%" align="center">{{ $packing->waktu ? \Carbon\Carbon::parse($packing->waktu)->format('H:i') : '-' }}</td>
            <td width="4%" align="center">{{ $packing->kalibrasi === 'Ok' ? 'V' : ($packing->kalibrasi === 'Tidak Ok' ? 'X' : '-') }}</td>
            <td width="6%" align="center">
                @if($packing->qrcode && file_exists(public_path($packing->qrcode)))
                    <img src="{{ public_path($packing->qrcode) }}" width="35">
                @else
                    -
                @endif
            </td>
            <td width="6%" align="center">
                @if($packing->kode_printing && file_exists(public_path($packing->kode_printing)))
                    <img src="{{ public_path($packing->kode_printing) }}" width="35">
                @else
                    -
                @endif
            </td>
            <td width="6%" align="center">{{ $kodeTopleMap[$packing->kode_toples] ?? $packing->kode_toples ?? '-' }}</td>
            <td width="4%" align="center">{{ $row['suhu'] }}</td>
            <td width="4.5%" align="center">{{ $packing->speed ?? '-' }}</td>
            <td width="5.5%" align="center">{{ $packing->kondisi_segel ?? '-' }}</td>
            <td width="5%" align="center">{{ $packing->jumlah_produk ?? '-' }}</td>
            <td width="4.5%" align="center">{{ $packing->berat_pcs ?? '-' }}</td>
            <td width="4.5%" align="center">{{ $packing->berat_pack ?? '-' }}</td>
            <td width="6.5%" align="center">{{ $row['jenis'] }}</td>
            <td width="6.5%" align="center">{{ $row['lot'] }}</td>
            <td width="6%" align="center">{{ $row['tgl'] }}</td>
            <td width="6.5%" align="center">{{ $row['supplier'] }}</td>
            <td width="6%" align="center">{{ $userMap[$packing->username] ?? $packing->username ?? '-' }}</td>
            <td width="6%" align="center">{{ $userMap[$packing->nama_produksi] ?? $packing->nama_produksi ?? '-' }}</td>
            <td width="8.5%" align="center">{{ $packing->keterangan ?? '-' }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="18" align="center">Tidak ada data packing</td>
        </tr>
        @endforelse
    </table>

    <div style="margin-top:5px; text-align:right;">
        <i>{{ $noDokumen ?? '-' }}</i>
    </div>

    @if($loop->last)

        <br>
        <br>

        <table width="100%" class="note" cellpadding="0" cellspacing="0" nobr="true">
            <tr>
                <td width="60%">
                    <strong>Ket :</strong><br>
                    OK : V<br>
                    Tidak OK : X
                </td>

                <td width="20%" align="center" style="vertical-align: top;">
                    Dibuat oleh :<br>
                    @if($namaPemeriksa)
                        <u>{{ $namaPemeriksa }}</u><br>
                        <br>
                        <img src="data:image/png;base64,{{ $qrPemeriksa }}" width="60" height="60"><br>
                        QC
                    @endif
                </td>

                <td width="20%" align="center" style="vertical-align: top;">
                    Disetujui oleh :<br>
                    @if($namaSpv)
                        <u>{{ $namaSpv }}</u><br>
                        <br>
                        <img src="data:image/png;base64,{{ $qrSpv }}" width="60" height="60"><br>
                        QC SPV
                    @elseif($packings->count() > 0)
                        <i>Belum semua entry disetujui</i><br>
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                        QC SPV
                    @endif
                </td>
            </tr>
        </table>

    @else
        <br pagebreak="true" />
    @endif

@endforeach

</body>
</html>