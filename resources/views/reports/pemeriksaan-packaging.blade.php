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
<h2 class="title">PEMERIKSAAN PACKAGING</h2>
<br>
<br>


@php
$firstInspection = $inspections->first();
$date = $firstInspection ? \Carbon\Carbon::parse($firstInspection->inspection_date)->format('d-m-Y') : '';
$shift = $firstInspection ? $firstInspection->shift : '';
@endphp
<table width="100%" class="tbl-header">
    <tr>
        <td>Hari / Tanggal: {{ $date }}</td>
        <td>Shift: {{ $shift }}</td>
    </tr>
</table>

<br>

{{-- TABEL UTAMA --}}
<table width="100%" class="tbl-main small">
    <tr>
        <th rowspan="2" class="center">No</th>
        <th rowspan="2" class="center">Jenis Packaging</th>
        <th rowspan="2" class="center">Supplier</th>
        <th rowspan="2" class="center">Lot / Batch</th>
        <th colspan="5" class="center">Kondisi Packaging*</th>
        <th rowspan="2" class="center">Jumlah Barang</th>
        <th rowspan="2" class="center">Jumlah Sampel</th>
        <th rowspan="2" class="center">Jumlah Reject</th>
        <th colspan="2" class="center">Penerimaan</th>
        <th rowspan="2" class="center">No. Pol</th>
        <th rowspan="2" class="center">Kondisi Kendaraan**</th>
        <th rowspan="2" class="center">PBB / OP</th>
        <th rowspan="2" class="center">Keterangan***</th>
    </tr>

    <tr>
        <th class="center">Design</th>
        <th class="center">Sambungan / Sealing</th>
        <th class="center">Warna</th>
        <th class="center">Dimensi</th>
        <th class="center">Berat</th>
        <th class="center">OK</th>
        <th class="center">Tolak</th>
    </tr>

    @php
    $no = 1;
    @endphp
    @if($inspections->count() > 0)
        @foreach($inspections as $inspection)
            @if($inspection->items && $inspection->items->count() > 0)
                @foreach($inspection->items as $item)
                <tr>
                    <td class="center">{{ $no++ }}</td>
                    <td>{{ $item->packaging_type ?? '' }}</td>
                    <td>{{ $item->supplier ?? '' }}</td>
                    <td>{{ $item->lot_batch ?? '' }}</td>
                    <td class="center">{{ $item->condition_design ?? '' }}</td>
                    <td class="center">{{ $item->condition_sealing ?? '' }}</td>
                    <td class="center">{{ $item->condition_color ?? '' }}</td>
                    <td>{{ $item->condition_dimension ?? '' }}</td>
                    <td>{{ $item->condition_weight ?? '' }}</td>
                    <td class="center">{{ $item->quantity_goods ?? '' }}</td>
                    <td class="center">{{ $item->quantity_sample ?? '' }}</td>
                    <td class="center">{{ $item->quantity_reject ?? '' }}</td>
                    <td class="center">{{ $item->acceptance_status == 'OK' ? 'V' : '' }}</td>
                    <td class="center">{{ $item->acceptance_status == 'Tolak' ? 'V' : '' }}</td>
                    <td>{{ $item->no_pol ?? '' }}</td>
                    <td>{{ $item->vehicle_condition ?? '' }}</td>
                    <td>{{ $item->pbb_op ?? '' }}</td>
                    <td>{{ $item->notes ?? '' }}</td>
                </tr>
                @endforeach
            @endif
        @endforeach
    @endif

    @if($no <= 20)
    @for($i = $no; $i <= 20; $i++)
    <tr>
        <td class="center">{{ $i }}</td>
        @for($j=1; $j<=17; $j++)
            <td></td>
        @endfor
    </tr>
    @endfor
    @endif
</table>

<br>

{{-- KETERANGAN --}}
<table width="100%" class="small">
    <tr>
        <td width="50%">
            <strong>Note *Kondisi Packaging :</strong><br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>Design</strong> : warna print karton,toples  sesuai standar & tidak luntur<br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>Sambungan/Sealing</strong> : lem pada karton & sealing plastik cukup kuat (sesuai standar)<br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>Warna</strong> : warna dasar karton, toples, etiket sesuai standar<br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>Dimensi</strong> : ukuran kemasan (panjang,lebar,tinggi,ketebalan), flute<br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>Berat</strong> : berat karton, toples sesuai standar (gr)<br>
            **&nbsp;&nbsp; 1 = bersih &nbsp;&nbsp; 2 = kotor &nbsp;&nbsp; 3 = bau &nbsp;&nbsp; 4 = bocor<br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;5 = basah &nbsp;&nbsp; 6 = kering &nbsp;&nbsp; 7 = bebas hama<br>
            *** 1 = Pengisian nomor segel (apabila ada)<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;2 = Pengisian nama supir
        </td>
        <td width="50%">
            <br><br>
            <br><br>
            <table width="100%" class="small">
                <tr>
                    <td width="50%" class="center">
                        Diperiksa oleh,<br><br><br>
                        ( ___________________ )<br>
                        QC
                    </td>
                    <td width="50%" class="center">
                        Diverifikasi oleh,<br><br><br>
                        ( ___________________ )<br>
                        SPV QC
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>





</body>
</html>
