<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">

    <style>
        body {
            font-size: 6px;
        }

        table {
            border-collapse: collapse;
        }

        .title {
            font-size: 11px;
            font-weight: bold;
            text-align: center;
        }

        .small {
            font-size: 6px;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .tbl-main,
        .tbl-main th,
        .tbl-main td {
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

@php
    /*
     * Ambil semua item dari seluruh inspection
     */
    $items = collect();

    foreach ($inspections as $inspection) {
        if ($inspection->items && $inspection->items->count() > 0) {
            foreach ($inspection->items as $item) {
                $items->push($item);
            }
        }
    }

    /*
     * Maksimal 20 entry per halaman
     *
     * 5  data  = 5
     * 10 data  = 10
     * 20 data  = 20
     * 21 data  = 20 + 1
     * 40 data  = 20 + 20
     * 41 data  = 20 + 20 + 1
     */
    $pages = $items->chunk(20);

    /*
     * Tanggal dan shift
     */
    $firstInspection = $inspections->first();

    $date = $firstInspection
        ? \Carbon\Carbon::parse($firstInspection->inspection_date)->format('d-m-Y')
        : '';

    $shift = $firstInspection
        ? $firstInspection->shift
        : '';

    /*
     * Jumlah halaman
     */
    $totalPages = max($pages->count(), 1);
@endphp


{{-- HEADER --}}

<table width="100%" style="border-collapse:collapse;border:1px solid #000;">
    <tr>

        {{-- LOGO --}}
        <td width="25%" style="border:1px solid #000;text-align:center;padding:5px;">
            <img src="{{ public_path('assets/img/Logofd.png') }}" width="50">
        </td>

        {{-- JUDUL --}}
        <td width="50%" style="border:1px solid #000;text-align:center;font-size:18px;padding:5px;">
            <b>FORM</b><br>
            <b>PEMERIKSAAN PACKAGING</b>
        </td>

        {{-- INFORMASI DOKUMEN --}}
        <td width="25%" style="border:1px solid #000;font-size:10px;padding:0;">

            <table width="100%" style="border-collapse:collapse;">

                <tr>
                    <td width="45%"
                        style="border-right:1px solid #000;border-bottom:1px solid #000;padding:3px;">
                        No. Dokumen
                    </td>

                    <td style="border-bottom:1px solid #000;padding:3px;">
                        : {{ $noDokumen }}
                    </td>
                </tr>

                <tr>
                    <td style="border-right:1px solid #000;border-bottom:1px solid #000;padding:3px;">
                        Revisi
                    </td>

                    <td style="border-bottom:1px solid #000;padding:3px;">
                        : {{ (int) substr(strrchr($noDokumen, '/'), 1) }}
                    </td>
                </tr>

                <tr>
                    <td style="border-right:1px solid #000;border-bottom:1px solid #000;padding:3px;">
                        Tanggal Efektif
                    </td>

                    <td style="border-bottom:1px solid #000;padding:3px;">
                        : 01-04-2016
                    </td>
                </tr>

                <tr>
                    <td style="border-right:1px solid #000;padding:3px;">
                        Halaman
                    </td>

                    <td style="padding:3px;">
                        : 1 dari {{ $totalPages }}
                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>

<br>
<br>


{{-- TANGGAL DAN SHIFT --}}

<table width="100%" class="tbl-header">
    <tr>
        <td>
            Hari / Tanggal: {{ $date }}
        </td>

        <td>
            Shift: {{ $shift }}
        </td>
    </tr>
</table>

<br>


{{-- TABEL DATA --}}

@if($pages->count() > 0)

    @foreach($pages as $pageIndex => $pageItems)

        <table width="100%" class="tbl-main small">

            {{-- HEADER BARIS 1 --}}
            <tr>
                <th rowspan="2" class="center">
                    No
                </th>

                <th rowspan="2" class="center">
                    Jenis Packaging
                </th>

                <th rowspan="2" class="center">
                    Supplier
                </th>

                <th rowspan="2" class="center">
                    Lot / Batch
                </th>

                <th colspan="5" class="center">
                    Kondisi Packaging*
                </th>

                <th rowspan="2" class="center">
                    Jumlah Barang
                </th>

                <th rowspan="2" class="center">
                    Jumlah Sampel
                </th>

                <th rowspan="2" class="center">
                    Jumlah Reject
                </th>

                <th colspan="2" class="center">
                    Penerimaan
                </th>

                <th rowspan="2" class="center">
                    No. Pol
                </th>

                <th rowspan="2" class="center">
                    Kondisi Kendaraan**
                </th>

                <th rowspan="2" class="center">
                    DO/PO/OP
                </th>

                <th rowspan="2" class="center">
                    Keterangan***
                </th>
            </tr>


            {{-- HEADER BARIS 2 --}}
            <tr>
                <th class="center">
                    Design
                </th>

                <th class="center">
                    Sambungan / Sealing
                </th>

                <th class="center">
                    Warna
                </th>

                <th class="center">
                    Dimensi
                </th>

                <th class="center">
                    Berat
                </th>

                <th class="center">
                    OK
                </th>

                <th class="center">
                    Tolak
                </th>
            </tr>


            {{-- DATA --}}

            @foreach($pageItems as $index => $item)

                @php
                    $no = ($pageIndex * 20) + $index + 1;
                @endphp

                <tr>

                    <td class="center">
                        {{ $no }}
                    </td>

                    <td>
                        {{ $item->packaging_type ?? '' }}
                    </td>

                    <td>
                        {{ $item->supplier ?? '' }}
                    </td>

                    <td>
                        {{ $item->lot_batch ?? '' }}
                    </td>

                    <td class="center">
                        {{ $item->condition_design ?? '' }}
                    </td>

                    <td class="center">
                        {{ $item->condition_sealing ?? '' }}
                    </td>

                    <td class="center">
                        {{ $item->condition_color ?? '' }}
                    </td>

                    <td>
                        {{ $item->condition_dimension ?? '' }}
                    </td>

                    <td>
                        {{ $item->condition_weight ?? '' }}
                    </td>

                    <td class="center">
                        {{ $item->quantity_goods ?? '' }}
                    </td>

                    <td class="center">
                        {{ $item->quantity_sample ?? '' }}
                    </td>

                    <td class="center">
                        {{ $item->quantity_reject ?? '' }}
                    </td>

                    <td class="center">
                        {{ $item->acceptance_status == 'OK' ? 'V' : '' }}
                    </td>

                    <td class="center">
                        {{ $item->acceptance_status == 'Tolak' ? 'V' : '' }}
                    </td>

                    <td>
                        {{ $item->no_pol ?? '' }}
                    </td>

                    <td>
                        {{ $item->vehicle_condition ?? '' }}
                    </td>

                    <td>
                        {{ $item->pbb_op ?? '' }}
                    </td>

                    <td>
                        {{ $item->notes ?? '' }}
                    </td>

                </tr>

            @endforeach

        </table>


        {{-- KETERANGAN DAN TANDA TANGAN HANYA DI HALAMAN TERAKHIR --}}

        @if($loop->last)

            <br>

            <table width="100%" class="small">
                <tr>

                    <td width="50%">

                        <strong>Note *Kondisi Packaging :</strong><br>

                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <strong>Design</strong> :
                        warna print karton,toples sesuai standar & tidak luntur
                        <br>

                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <strong>Sambungan/Sealing</strong> :
                        lem pada karton & sealing plastik cukup kuat (sesuai standar)
                        <br>

                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <strong>Warna</strong> :
                        warna dasar karton, toples, etiket sesuai standar
                        <br>

                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <strong>Dimensi</strong> :
                        ukuran kemasan (panjang,lebar,tinggi,ketebalan), flute
                        <br>

                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <strong>Berat</strong> :
                        berat karton, toples sesuai standar (gr)
                        <br>

                        **&nbsp;&nbsp;
                        1 = bersih
                        &nbsp;&nbsp;
                        2 = kering
                        &nbsp;&nbsp;
                        3 = tidak bocor
                        &nbsp;&nbsp;
                        4 = tidak berdebu
                        <br>

                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        5 = tidak basah
                        &nbsp;&nbsp;
                        6 = bebas hama
                        &nbsp;&nbsp;
                        7 = bebas noda (karat, cat, tinta)
                        <br>

                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        8 = bebas bekas oli di lantai/dinding
                        &nbsp;&nbsp;
                        9 = tidak ada produk non halal
                        <br>

                        *** 1 = Pengisian nomor segel (apabila ada)
                        <br>

                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        2 = Pengisian nama supir

                    </td>


                    {{-- TANDA TANGAN --}}

                    <td width="50%">

                        <br>
                        <br>
                        <br>
                        <br>

                        <table width="100%" class="small">
                            <tr>

                                <td width="50%" class="center">
                                    Diperiksa oleh,
                                    <br><br><br>
                                    ( ___________________ )
                                    <br>
                                    QC
                                </td>

                                <td width="50%" class="center">
                                    Diverifikasi oleh,
                                    <br><br><br>
                                    ( ___________________ )
                                    <br>
                                    SPV QC
                                </td>

                            </tr>
                        </table>

                    </td>

                </tr>
            </table>

        @endif


        {{-- PINDAH HALAMAN JIKA MASIH ADA DATA --}}

        @if(!$loop->last)
            <div style="page-break-after: always;"></div>
        @endif

    @endforeach

@else

    {{-- HEADER TABEL SAAT TIDAK ADA DATA --}}

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
            <th rowspan="2" class="center">DO/PO/OP</th>
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

    </table>

@endif

</body>
</html>