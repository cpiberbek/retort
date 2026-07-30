<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Data No. Lot Wire</title>
    <style>
        body {
            font-family: helvetica, sans-serif;
            font-size: 8px;
            line-height: 1.1; /* Line height dirapatkan sedikit */
        }
        
        /* HEADER */
        .company-header {
            width: 100%;
            border-bottom: 2px solid #000;
            margin-bottom: 10px;
        }
        .company-name { font-size: 10px; font-weight: bold; }
        .report-title { font-size: 12px; font-weight: bold; text-align: center; text-transform: uppercase; }

        /* TABEL UTAMA */
        table.tbl-data {
            width: 100%;
            border-collapse: collapse;
        }
        table.tbl-data th {
            background-color: #e6e6e6;
            border: 1px solid #000;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
            padding: 5px 2px;
        }
        table.tbl-data td {
            border: 1px solid #000;
            vertical-align: middle; /* Pastikan semua kolom sejajar tengah secara vertikal */
            padding: 4px 2px; /* Padding standar untuk kolom biasa */
        }

        /* KHUSUS KOLOM DETAIL WIRE (NESTED) */
        /* Kita hilangkan padding di TD pembungkus agar tabel di dalamnya bisa full width */
        td.td-nested {
            padding: 0 !important; 
            margin: 0 !important;
            vertical-align: top; /* Align top agar rapi jika isinya panjang */
        }

        /* TABEL DI DALAM KOLOM DETAIL */
        table.nested-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
            margin: 0;
        }
        table.nested-table td {
            border: none; 
            border-bottom: 1px solid #ccc; /* Garis pemisah antar item lebih tipis */
            padding: 3px 4px; /* Padding dalam item agar teks tidak nempel garis */
            vertical-align: top;
            text-align: left;
        }
        /* Hapus border bawah untuk baris terakhir di nested table */
        table.nested-table tr:last-child td {
            border-bottom: none;
        }

        .mesin-label {
            font-weight: bold;
            font-size: 7px;
            color: #000;
            background-color: #f9f9f9; /* Sedikit background beda untuk nama mesin */
        }

        /* Utility */
        .text-center { text-align: center !important; }
        .text-left { text-align: left !important; }
        .bg-ok { color: #006400; font-weight: bold; }
        .bg-rev { color: #8B0000; font-weight: bold; }

          /* tnr */
        body,
        table,
        tr,
        td,
        th {
            font-family: times;
            font-size: 9pt;
        }
    </style>
</head>

<body>

@php
$grids = [];

foreach ($items as $item) {
    $dataWire = json_decode($item->data_wire, true);

    if (!is_array($dataWire)) {
        continue;
    }

    foreach ($dataWire as $mesin) {
        $grids[] = [
            'tanggal'  => $item->date,
            'shift'    => $item->shift,
            'supplier' => $item->nama_supplier ?? '-',
            'produk'   => $item->nama_produk ?? '-',
            'catatan'  => $item->catatan ?? '-',
            'status'   => $item->status_spv ?? '-',
            'mesin'    => $mesin['mesin'] ?? '-',
            'detail'   => $mesin['detail'] ?? [],
        ];
    }
}

$totalPage = ceil(count($grids) / 9);
@endphp

@foreach(array_chunk($grids, 9) as $pageIndex => $rows)

<table width="100%" style="border-collapse:collapse;border:1px solid #000;">
    <tr>
        <td width="25%" style="border:1px solid #000;text-align:center;padding:5px;">
            <img src="{{ public_path('assets/img/Logofd.png') }}" width="50">
        </td>

        <td width="50%" style="border:1px solid #000;text-align:center;font-size:18px;padding:5px;">
            <b>FORM</b><br>
            <b>VERIFIKASI MAGNET TRAP</b>
        </td>

        <td width="25%" style="border:1px solid #000;font-size:10px;padding:0;">
            <table width="100%" style="border-collapse:collapse;">
                <tr>
                    <td width="45%" style="border-right:1px solid #000;border-bottom:1px solid #000;padding:3px;">
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
                        : 18-12-2023
                    </td>
                </tr>

                <tr>
                    <td style="border-right:1px solid #000;padding:3px;">
                        Halaman
                    </td>
                    <td style="padding:3px;">
                        : {{ $pageIndex + 1 }} dari {{ $totalPage }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<table width="100%" cellspacing="0" cellpadding="3" border="1">
    <tr>
        <td width="50%">
            <b>Hari / Tgl</b> :
            {{ $request->date ? \Carbon\Carbon::parse($request->date)->format('d-m-Y') : 'SEMUA' }}
        </td>

        <td width="50%">
            <b>Supplier Wire</b> :
            {{ count($grids) ? $grids[0]['supplier'] : '-' }}
        </td>
    </tr>
</table>

<br>

<table width="100%" border="0" cellspacing="2" cellpadding="0">

    @foreach(array_chunk($rows, 3) as $row)

<tr>

    @foreach($row as $grid)

    <td width="33.33%" valign="top">

        <table width="100%" border="1" cellspacing="0" cellpadding="3">

            <tr>
                <td colspan="2" align="center">
                    <b>MESIN : {{ $grid['mesin'] }}</b>
                </td>
            </tr>

            <tr>
                <td width="35%">
                    Tanggal
                </td>
                <td width="65%">
                    {{ \Carbon\Carbon::parse($grid['tanggal'])->format('d-m-Y') }}
                </td>
            </tr>

            <tr>
                <td>
                    Shift
                </td>
                <td>
                    {{ $grid['shift'] }}
                </td>
            </tr>

            {{-- <tr>
                <td>
                    Supplier
                </td>
                <td>
                    {{ $grid['supplier'] }}
                </td>
            </tr> --}}

            <tr>
                <td>
                    Produk
                </td>
                <td>
                    {{ $grid['produk'] }}
                </td>
            </tr>

            <tr>
                <td colspan="2">
                    <b>Detail Wire</b>
                </td>
            </tr>

            @if(!empty($grid['detail']) && is_array($grid['detail']))

                @foreach($grid['detail'] as $dtl)

                <tr>
                    <td width="35%">
                        <strong>Lot:</strong> 
                    </td>

                    <td width="65%">
                       <strong>{{ $dtl['no_lot'] ?? '' }}</strong>
                    </td>
                </tr>

                <tr>
                    <td width="35%">
                        Start - End:
                    </td>

                    <td width="65%">
                        {{ $dtl['start'] ?? '?' }} - {{ $dtl['end'] ?? '?' }}
                    </td>
                </tr>

                @endforeach

            @endif

            <tr>
                <td>
                    Catatan
                </td>

                <td>
                    {{ $grid['catatan'] }}
                </td>
            </tr>

        </table>

    </td>

    @endforeach

    @for($i = count($row); $i < 3; $i++)
        <td width="33.33%"></td>
    @endfor

</tr>

@endforeach

</table>

<br>

@if(!$loop->last)
    <div style="page-break-after: always;"></div>
@endif

@endforeach

<br><br><br>

@php
$ttd = $items->first();
@endphp

<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td width="60%"></td>

        <td width="40%">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td width="50%" align="center">
                        Diperiksa oleh
                        <br><br><br>
                        ( <u>{{ $ttd->username_updated ?? $ttd->username }}</u> )
                        <br>
                        QC
                    </td>

                    <td width="50%" align="center">
                        Disetujui oleh
                        <br><br><br>
                        ( <u>{{ $ttd->nama_spv ?? '' }}</u> )
                        <br>
                        QC SPV
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

</body>
    
</html>