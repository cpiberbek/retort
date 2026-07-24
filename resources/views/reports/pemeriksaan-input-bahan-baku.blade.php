<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page {
            margin: 12px;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8px;
            color: #000;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        .title {
            font-size: 13px;
            font-weight: bold;
            text-align: center;
            margin: 1px 0 10px;
        }

        .small {
            font-size: 8px;
        }

        .center {
            text-align: center;
            vertical-align: middle;
        }

        .right {
            text-align: right;
        }

        .tbl-header td {
            padding: 3px;
            font-size: 8px;
        }

        .tbl-header td {
            padding: 0;
            line-height: 1;
        }

        .tbl-main {
            table-layout: fixed;
        }

        .tbl-main,
        .tbl-main td,
        .tbl-main th {
            border: 0.8px solid #000;
        }

        .tbl-main th {
            background: #d9e2f3;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
            padding: 4px 2px;
            line-height: 1.15;
        }

        .tbl-main td {
            padding: 3px 2px;
            vertical-align: middle;
            word-wrap: break-word;
        }

        .tbl-main tr {
            height: 18px;
        }

        .note td {
            vertical-align: top;
            line-height: 1.4;
        }

        .signature td {
            padding-top: 25px;
        }
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 8px;
            color: #000;
        }
    </style>
</head>

<body>

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
    PEMERIKSAAN INPUT BAHAN BAKU
</h2>
<br>
<br>

@php
$firstItem = $items->first();
$date = $firstItem ? \Carbon\Carbon::parse($firstItem->setup_kedatangan)->format('d-m-Y') : '';
@endphp
<table width="100%" class="tbl-header">
    <tr>
        <td width="15%">Hari / Tanggal</td>
        <td width="85%">: {{ $date }}</td>
    </tr>
</table>

<br>

{{-- TABEL UTAMA --}}
<table width="100%" class="tbl-main small">
    <tr>
    	<th rowspan="3" class="center"><b>No.</b></th>
        <th rowspan="3" class="center"><b>Nama Varian</b></th>
        <th rowspan="3" class="center"><b>Supplier</b></th>
        <th colspan="2" class="center"><b>Tanggal</b></th>
        <th rowspan="3" class="center"><b>Jumlah Barang</b></th>
        <th rowspan="3" class="center"><b>Jumlah Sampel</b></th>
        <th rowspan="3" class="center"><b>Jumlah Reject</b></th>
        <th colspan="4" class="center"><b>Kondisi Fisik*</b></th>
        <th rowspan="3" class="center"><b>K.A / FFA</b></th>
        <th rowspan="3" class="center"><b>Logo Halal</b></th>
        <th rowspan="3" class="center"><b>Negara Asal Dibuatnya Varian Dan Nama Produsennya</b></th>
        <th colspan="3" class="center"><b>Dokumen</b></th>
        <th colspan="3" class="center"><b>Transporter</b></th>
        <th rowspan="3" class="center"><b>DO / PO</b></th>
        <th rowspan="3" class="center"><b>Ket***</b></th>
    </tr>
    <tr>
    	<th rowspan="2" class="center"><b>Lot<br>/Kode<br>/Batch</b></th>
        <th rowspan="2" class="center"><b>Expire Date</b></th>
        <th rowspan="2" class="center"><b>WARNA</b></th>
        <th rowspan="2" class="center"><b>KOTORAN</b></th>
        <th rowspan="2" class="center"><b>AROMA</b></th>
        <th rowspan="2" class="center"><b>KEMASAN</b></th>
        <th colspan="2" class="center"><b>Halal</b></th>
        <th rowspan="2" class="center"><b>COA</b></th>
        <th rowspan="2" class="center"><b>Nopol Mobil</b></th>
        <th rowspan="2" class="center"><b>Suhu Mobil</b></th>
        <th rowspan="2" class="center"><b>Kondisi Mobil**</b></th>
    </tr>
    <tr>
        <th class="center"><b>BERLAKU</b></th>
        <th class="center"><b>TIDAK</b></th>
    </tr>

    @php
    $no = 1;
    @endphp
    @foreach($items as $item)
        @foreach($item->productDetails as $detail)
        <tr>
            <td class="center">{{ $no++ }}</td>
            <td>{{ $item->bahan_baku }}</td>
            <td>{{ $item->supplier }}</td>
            <td>{{ $detail->kode_batch }}</td>
            <td>{{ \Carbon\Carbon::parse($detail->exp)->format('d-m-Y') }}</td>
            <td class="center">{{ $detail->jumlah }}</td>
            <td class="center">{{ $detail->jumlah_sampel }}</td>
            <td class="center">{{ $detail->jumlah_reject }}</td>
            <td class="center">{{ $item->mobil_check_kotoran ? 'V' : '' }}</td>
            <td class="center">{{ $item->mobil_check_aroma ? 'V' : '' }}</td>
            <td class="center">{{ $item->mobil_check_warna ? 'V' : '' }}</td>
            <td class="center">{{ $item->mobil_check_kemasan ? 'V' : '' }}</td>
            <td></td>
            <td class="center">{{ $item->analisa_logo_halal ? 'V' : '' }}</td>
            <td>{{ $item->analisa_negara_asal }} / {{ $item->analisa_produsen }}</td>
            <td></td>
            <td></td>
            <td></td>
            <td>{{ $item->dokumen_halal_berlaku ? 'V' : '' }}</td>
            <td>{{ $item->nopol_mobil }}</td>
            <td>{{ $item->suhu_mobil }}</td>
            <td>{{ $item->kondisi_mobil }}</td>
            <td>{{ $item->no_segel }}</td>
            <td>{{ $item->do_po }}</td>
            <td>{{ $item->keterangan }}</td>
        </tr>
        @endforeach
    @endforeach

</table>

<br>
<br>

{{-- KETERANGAN --}}
<table width="100%" class="note small">
    <tr>
        <td width="70%">
            <strong>Keterangan :</strong><br>
            * V = sesuai spesifikasi / standar<br>
            ** 1 = bersih &nbsp; 2 = kotor &nbsp; 3 = bau &nbsp; 4 = bocor<br>
            &nbsp;&nbsp;&nbsp;&nbsp;5 = basah &nbsp; 6 = kering &nbsp; 7 = bebas hama<br>
            *** 1 = Jika raw meat dilakukan pengujian suhu varian<br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;2 = Pengisian nomor segel<br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;3 = Pengisian nama supir<br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;4 = Pengisian bahan baku alergen / non alergen
        </td>

        <td width="30%">
            Diperiksa oleh :
            <u>{{ \App\Models\User::where('uuid', $item->created_by_uuid)->value('name') ?? '-' }}</u>

            &nbsp;&nbsp;&nbsp;&nbsp;

            Disetujui oleh :
            <u>{{ \App\Models\User::where('uuid', $item->verified_by_spv_uuid)->value('name') ?? '-' }}</u>
        </td>
    </tr>
</table>

<br><br>



</body>
</html>
