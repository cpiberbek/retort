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
            font-size: 6.5px;
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

    $firstItem = $items->first();
    $date = $firstItem ? \Carbon\Carbon::parse($firstItem->setup_kedatangan)->format('d-m-Y') : '';

    $mappingKondisi = [
        'Bersih' => 1,
        'Kering' => 2,
        'Tidak Bocor' => 3,
        'Tidak Berdebu' => 4,
        'Tidak Basah' => 5,
        'Bebas Hama' => 6,
        'Bebas Noda (Karat, cat, tinta)' => 7,
        'Bebas Bekas oli di lantai/dinding' => 8,
        'Tidak ada produk non halal' => 9,
    ];

    $satuanMap = \DB::table('master_raw_materials')
        ->pluck('satuan', 'nama_bahan_baku');

    $rows = [];
    foreach ($items as $item) {
        $kondisiMobil = $item->kondisi_mobil;
        foreach ($mappingKondisi as $nama => $nomor) {
            $kondisiMobil = str_replace($nama, $nomor, $kondisiMobil);
        }
        $kondisiMobil = preg_replace('/\s*,\s*/', ', ', $kondisiMobil);

        foreach ($item->productDetails as $detail) {
            $rows[] = ['item' => $item, 'detail' => $detail, 'kondisi' => $kondisiMobil];
        }
    }

    $pages = count($rows) > 0 ? array_chunk($rows, 15) : [[]];
@endphp

{{-- HEADER --}}
<div style="margin-left:-30px; margin-bottom:0;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0;">
        <tr>
            {{-- <td width="55" style="padding:0;">
                <img src="{{ public_path('assets/img/Logo CPI.png') }}" width="50">
            </td> --}}
            <td style="padding:0; line-height:1;">
                <span style="font-size:14pt;"><b>PT Charoen Pokphand Indonesia</b></span><br>
                <span style="font-size:14pt;"><b>Food Division</b></span>
            </td>
        </tr>
    </table>
</div>

<h2 class="title" style="margin:0; padding:0;">
    PEMERIKSAAN INPUT BAHAN BAKU
</h2>
<br>
<br>

<table width="100%" class="tbl-header">
    <tr>
        <td width="15%">Hari / Tanggal</td>
        <td width="85%">: {{ $date }}</td>
    </tr>
</table>

<br>

{{-- TABEL UTAMA --}}
@php $no = 1; @endphp
@foreach($pages as $pageRows)

    <table width="100%" class="tbl-main small" cellpadding="0" cellspacing="0">
        <tr>
            <th rowspan="3" width="2.5%"><b>No.</b></th>
            <th rowspan="3" width="7%"><b>Nama Produk</b></th>
            <th rowspan="3" width="5.5%"><b>Supplier</b></th>
            <th colspan="2" width="10%"><b>Tanggal</b></th>
            <th rowspan="3" width="4%"><b>Jumlah<br>Barang</b></th>
            <th rowspan="3" width="4%"><b>Jumlah<br>Sampel</b></th>
            <th rowspan="3" width="4%"><b>Jumlah<br>Reject</b></th>
            <th colspan="4" width="15%"><b>Kondisi Fisik*</b></th>
            <th rowspan="3" width="3.5%"><b>K.A /<br>FFA</b></th>
            <th rowspan="3" width="3%"><b>Logo<br>Halal</b></th>
            <th rowspan="3" width="7.5%"><b>Negara Asal Dibuatnya Varian Dan Nama Produsennya</b></th>
            <th colspan="3" width="10%"><b>Dokumen</b></th>
            <th colspan="3" width="15.5%"><b>Transporter</b></th>
            <th rowspan="3" width="4%"><b>DO / PO</b></th>
            <th rowspan="3" width="4.5%"><b>Ket***</b></th>
        </tr>
        <tr>
            <th rowspan="2" width="5.5%"><b>Lot<br>/Kode<br>/Batch</b></th>
            <th rowspan="2" width="4.5%"><b>Expire<br>Date</b></th>
            <th rowspan="2" width="3.5%"><b>WARNA</b></th>
            <th rowspan="2" width="4%"><b>KOTORAN</b></th>
            <th rowspan="2" width="3.5%"><b>AROMA</b></th>
            <th rowspan="2" width="4%"><b>KEMASAN</b></th>
            <th colspan="2" width="7%"><b>Halal</b></th>
            <th rowspan="2" width="3%"><b>COA</b></th>
            <th rowspan="2" width="5%"><b>Nopol<br>Mobil</b></th>
            <th rowspan="2" width="3.5%"><b>Suhu<br>Mobil</b></th>
            <th rowspan="2" width="7%"><b>Kondisi<br>Mobil**</b></th>
        </tr>
        <tr>
            <th width="4%"><b>BERLAKU</b></th>
            <th width="3%"><b>TIDAK</b></th>
        </tr>

        @foreach($pageRows as $row)
            @php
                $item   = $row['item'];
                $detail = $row['detail'];
            @endphp
        <tr>
            <td width="2.5%" align="center">{{ $no++ }}</td>
            <td width="7%"   align="center">{{ $item->bahan_baku }} ({{ $satuanMap[$item->bahan_baku] ?? '' }})</td>
            <td width="5.5%" align="center">{{ $item->supplier }}</td>
            <td width="5.5%" align="center">{{ $detail->kode_batch }}</td>
            <td width="4.5%" align="center">{{ \Carbon\Carbon::parse($detail->exp)->format('d-m-Y') }}</td>
            <td width="4%"   align="center">{{ $detail->jumlah }}</td>
            <td width="4%"   align="center">{{ $detail->jumlah_sampel }}</td>
            <td width="4%"   align="center">{{ $detail->jumlah_reject }}</td>
            <td width="3.5%" align="center">{{ $item->mobil_check_warna ? 'V' : 'X' }}</td>
            <td width="4%"   align="center">{{ $item->mobil_check_kotoran ? 'V' : 'X' }}</td>
            <td width="3.5%" align="center">{{ $item->mobil_check_aroma ? 'V' : 'X' }}</td>
            <td width="4%"   align="center">{{ $item->mobil_check_kemasan ? 'V' : 'X' }}</td>
            <td width="3.5%" align="center">{{ $item->analisa_ka_ffa }}</td>
            <td width="3%"   align="center">{{ $item->analisa_logo_halal ? 'V' : 'X' }}</td>
            <td width="7.5%" align="center">{{ $item->analisa_negara_asal }} / {{ $item->analisa_produsen }}</td>
            <td width="4%"   align="center">{{ $item->dokumen_halal_berlaku ? 'V' : '' }}</td>
            <td width="3%"   align="center">{{ !$item->dokumen_halal_berlaku ? 'X' : '' }}</td>
            <td width="3%"   align="center">{{ $item->dokumen_coa_file ? 'V' : 'X' }}</td>
            <td width="5%"   align="center">{{ $item->nopol_mobil }}</td>
            <td width="3.5%" align="center">{{ $item->suhu_mobil }}</td>
            <td width="7%"   align="center">{{ $row['kondisi'] }}</td>
            <td width="4%"   align="center">{{ $item->do_po }}</td>
            <td width="4.5%" align="center">{{ $item->keterangan }}<br>Suhu Mobil:<br>{{ $item->suhu_daging }}°C</td>
        </tr>
        @endforeach
    </table>

    <div style="margin-top:5px; text-align:right;">
        <i>{{ $noDokumen ?? '-' }}</i>
    </div>

    @if($loop->last)

        <br>
        <br>

        <table width="100%" class="note small" nobr="true">
            <tr>
                <td width="60%">
                    <strong>Keterangan :</strong><br>
                    * V = sesuai spesifikasi / standar<br>
                    ** 1 = bersih &nbsp; 2 = kering &nbsp; 3 = tidak bocor &nbsp; 4 = tidak berdebu<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;5 = tidak basah &nbsp; 6 = bebas hama &nbsp; 7 = bebas noda (karat, cat, tinta)<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;8 = bebas bekas oli di lantai/dinding &nbsp; 9 = tidak ada produk non halal<br>
                    *** 1 = Jika raw meat dilakukan pengujian suhu varian<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;2 = Pengisian nomor segel<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;3 = Pengisian nama supir<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;4 = Pengisian bahan baku alergen / non alergen
                </td>

                <td width="20%" align="center" style="vertical-align: top;">
                    Diperiksa oleh :<br>
                    @if($namaPemeriksa)
                        <br>
                        <img src="data:image/png;base64,{{ $qrPemeriksa }}" width="60" height="60"><br>
                        <u>{{ $namaPemeriksa }}</u><br>
                        QC
                    @endif
                </td>

                <td width="20%" align="center" style="vertical-align: top;">
                    Disetujui oleh :<br>
                    @if($namaSpv)
                        <br>
                        <img src="data:image/png;base64,{{ $qrSpv }}" width="60" height="60"><br>
                        <u>{{ $namaSpv }}</u><br>
                        SPV QC
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