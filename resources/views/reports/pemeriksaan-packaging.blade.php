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

        .small {
            font-size: 7px;
        }

        .center {
            text-align: center;
            vertical-align: middle;
        }

        .tbl-header td {
            padding: 0;
            font-size: 8px;
            line-height: 1;
        }

        .tbl-main,
        .tbl-main th,
        .tbl-main td {
            padding: 3px 2px;
            text-align: center;
            vertical-align: middle;
            border: 0.8px solid #000;
        }

        .tbl-main th {
            background: #d9e2f3;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
            padding: 3px 2px;
            line-height: 1.15;
        }

        .tbl-main td {
            padding: 3px 2px;
            vertical-align: middle;
        }

        .note td {
            vertical-align: top;
            line-height: 1.4;
        }
    </style>
</head>

<body>

@php
    $namaPemeriksa = (!empty($pemeriksa) && $pemeriksa !== '-') ? $pemeriksa : '';
    $namaSpv       = (!empty($spv) && $spv !== '-') ? $spv : '';

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

    $items = $inspections->flatMap(fn($inspection) => $inspection->items ?? collect());

    $pages = $items->count() > 0 ? $items->chunk(20) : collect([collect()]);

    $firstInspection = $inspections->first();
    $date  = $firstInspection ? \Carbon\Carbon::parse($firstInspection->inspection_date)->format('d-m-Y') : '';
    $shift = $firstInspection ? $firstInspection->shift : '';
@endphp

{{-- HEADER --}}
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
    PEMERIKSAAN PACKAGING
</h2>
<br>
<br>

<table width="100%" class="tbl-header">
    <tr>
        <td width="50%">Hari / Tanggal : {{ $date }}</td>
        <td width="50%">Shift : {{ $shift }}</td>
    </tr>
</table>

<br>

{{-- TABEL UTAMA --}}
@foreach($pages as $pageIndex => $pageItems)

    <table width="100%" class="tbl-main small" cellpadding="0" cellspacing="0">
        <tr>
            <th rowspan="2" width="3%"><b>No</b></th>
            <th rowspan="2" width="8%"><b>Jenis Packaging</b></th>
            <th rowspan="2" width="7%"><b>Supplier</b></th>
            <th rowspan="2" width="7%"><b>Lot / Batch</b></th>
            <th colspan="5" width="25%"><b>Kondisi Packaging*</b></th>
            <th rowspan="2" width="5%"><b>Jumlah Barang</b></th>
            <th rowspan="2" width="5%"><b>Jumlah Sampel</b></th>
            <th rowspan="2" width="5%"><b>Jumlah Reject</b></th>
            <th colspan="2" width="7%"><b>Penerimaan</b></th>
            <th rowspan="2" width="6%"><b>No. Pol</b></th>
            <th rowspan="2" width="8%"><b>Kondisi Kendaraan**</b></th>
            <th rowspan="2" width="6%"><b>DO/PO/OP</b></th>
            <th rowspan="2" width="8%"><b>Keterangan***</b></th>
        </tr>
        <tr>
            <th width="4.5%"><b>Design</b></th>
            <th width="5.5%"><b>Sambungan / Sealing</b></th>
            <th width="4.5%"><b>Warna</b></th>
            <th width="6%"><b>Dimensi</b></th>
            <th width="4.5%"><b>Berat</b></th>
            <th width="3.5%"><b>OK</b></th>
            <th width="3.5%"><b>Tolak</b></th>
        </tr>

        @foreach($pageItems as $index => $item)
        <tr>
            <td width="3%"   class="center">{{ ($pageIndex * 20) + $index + 1 }}</td>
            <td width="8%">{{ $item->packaging_type }}</td>
            <td width="7%">{{ $item->supplier }}</td>
            <td width="7%">{{ $item->lot_batch }}</td>
            <td width="4.5%" class="center">{{ $item->condition_design }}</td>
            <td width="5.5%" class="center">{{ $item->condition_sealing }}</td>
            <td width="4.5%" class="center">{{ $item->condition_color }}</td>
            <td width="6%">{{ $item->condition_dimension }}</td>
            <td width="4.5%">{{ $item->condition_weight }}</td>
            <td width="5%"   class="center">{{ $item->quantity_goods }}</td>
            <td width="5%"   class="center">{{ $item->quantity_sample }}</td>
            <td width="5%"   class="center">{{ $item->quantity_reject }}</td>
            <td width="3.5%" class="center">{{ $item->acceptance_status == 'OK' ? 'V' : '' }}</td>
            <td width="3.5%" class="center">{{ $item->acceptance_status == 'Tolak' ? 'V' : '' }}</td>
            <td width="6%">{{ $item->no_pol }}</td>
            <td width="8%">{{ $item->vehicle_condition }}</td>
            <td width="6%">{{ $item->pbb_op }}</td>
            <td width="8%">{{ $item->notes }}</td>
        </tr>
        @endforeach
    </table>

    <div style="margin-top:5px; text-align:right;">
        <i>{{ $noDokumen ?? '-' }}</i>
    </div>

    @if($loop->last)

        <br>
        <br>

        {{-- KETERANGAN + TANDA TANGAN --}}
        <table width="100%" class="note small" nobr="true">
            <tr>
                <td width="60%">
                    <strong>Note *Kondisi Packaging :</strong><br>
                    &nbsp;&nbsp;&nbsp;&nbsp;<strong>Design</strong> : warna print karton, toples sesuai standar &amp; tidak luntur<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;<strong>Sambungan/Sealing</strong> : lem pada karton &amp; sealing plastik cukup kuat (sesuai standar)<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;<strong>Warna</strong> : warna dasar karton, toples, etiket sesuai standar<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;<strong>Dimensi</strong> : ukuran kemasan (panjang, lebar, tinggi, ketebalan), flute<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;<strong>Berat</strong> : berat karton, toples sesuai standar (gr)<br>
                    ** 1 = bersih &nbsp; 2 = kering &nbsp; 3 = tidak bocor &nbsp; 4 = tidak berdebu<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;5 = tidak basah &nbsp; 6 = bebas hama &nbsp; 7 = bebas noda (karat, cat, tinta)<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;8 = bebas bekas oli di lantai/dinding &nbsp; 9 = tidak ada produk non halal<br>
                    *** 1 = Pengisian nomor segel (apabila ada)<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;2 = Pengisian nama supir
                </td>

                {{-- Dibuat oleh (kiri) --}}
                <td width="20%" align="center" style="vertical-align: top;">
                    Dibuat oleh :<br>
                    @if($namaPemeriksa)
                        <u>{{ $namaPemeriksa }}</u><br>
                        <br>
                        <img src="data:image/png;base64,{{ $qrPemeriksa }}" width="60" height="60"><br>
                        QC
                    @endif
                </td>

                {{-- Diverifikasi oleh (kanan) --}}
                <td width="20%" align="center" style="vertical-align: top;">
                    Diverifikasi oleh :<br>
                    @if($namaSpv)
                        <u>{{ $namaSpv }}</u>
                        <br><br>
                        <img src="data:image/png;base64,{{ $qrSpv }}" width="60" height="60"><br>
                        SPV QC
                    @elseif(!$semuaDisetujui && $inspections->count() > 0)
                        <i>Belum semua entry disetujui</i>
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