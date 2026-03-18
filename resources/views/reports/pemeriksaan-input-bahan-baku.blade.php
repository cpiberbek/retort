<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-size: 7px; }
        table { border-collapse: collapse; }

        .title {
            font-size: 11px;
            font-weight: bold;
            text-align: center;
        }

        .small { font-size: 7px; }
        .center { text-align: center; }
        .right { text-align: right; }

        .tbl-main, .tbl-main th, .tbl-main td {
            border: 0.3px solid #000;
        }

        .tbl-main th {
            background-color: #cfddee;
            text-align: center;
            vertical-align: middle;
            font-size: 7px;
        }

        .tbl-header td {
            padding: 2px;
            font-size: 8px;
        }
    </style>
</head>

<body>

{{-- HEADER --}}

<table width="100%">
    <tr>
        <td class="small" width="40%">
            PT Charoen Pokphand Indonesia<br>
            Food Division
        </td>
        
    </tr>
</table>
<h2 class="title">PEMERIKSAAN INPUT BAHAN BAKU</h2>
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
    	<th rowspan="3" class="center">No.</th>
        <th rowspan="3" class="center">Nama Varian</th>
        <th rowspan="3" class="center">Supplier</th>
        <th colspan="2" class="center">Tanggal	</th>
        <th rowspan="3" class="center">Jumlah Barang</th>
        <th rowspan="3" class="center">Jumlah Sampel</th>
        <th rowspan="3" class="center">Jumlah Reject</th>
        <th colspan="4" class="center">Kondisi Fisik*</th>
        <th rowspan="3" class="center">K.A / FFA</th>
        <th rowspan="3" class="center">Logo Halal</th>
        <th rowspan="3" class="center">Negara Asal Dibuatnya Varian Dan Nama Produsennya</th>
        <th colspan="3" class="center">Dokumen</th>
        <th colspan="3" class="center">Transporter</th>
        <th rowspan="3" class="center">DO / PO</th>
        <th rowspan="3" class="center">Ket***</th>
    </tr>
    <tr>
    	<th rowspan="2" class="center">Lot/Kode/ Batch</th>
        <th rowspan="2" class="center">Expire Date</th>
        <th rowspan="2" class="center">WARNA</th>
        <th rowspan="2" class="center">KOTORAN</th>
        <th rowspan="2" class="center">AROMA</th>
        <th rowspan="2" class="center">KEMASAN</th>
        <th colspan="2" class="center">Halal</th>
        <th rowspan="2" class="center">COA</th>
        <th rowspan="2" class="center">Nopol Mobil</th>
        <th rowspan="2" class="center">Suhu Mobil</th>
        <th rowspan="2" class="center">Kondisi mobil**</th>
    </tr>
    <tr>
        <th class="center">BERLAKU</th>
        <th class="center">TIDAK</th>
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
<table width="100%" class="small">
    <tr>
        <td width="50%">
            <strong>Keterangan :</strong><br>
            * V = sesuai spesifikasi / standar<br>
            ** 1 = bersih &nbsp;&nbsp; 2 = kotor &nbsp;&nbsp; 3 = bau &nbsp;&nbsp; 4 = bocor<br>
            &nbsp;&nbsp;&nbsp;&nbsp;5 = basah &nbsp;&nbsp; 6 = kering &nbsp;&nbsp; 7 = bebas hama<br>
            *** 1 = Jika raw meat dilakukan pengujian suhu varian<br>
            2 = Pengisian nomor segel<br>
            3 = Pengisian nama supir<br>
            4 = Pengisian bahan baku alergen / non alergen
        </td>
        <td width="50%">
            <table width="100%" class="small">
                <tr>
                    <td width="50%">Diperiksa oleh : ______________________</td>
                    <td width="50%" class="right">Disetujui oleh : ______________________</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<br><br>



</body>
</html>
