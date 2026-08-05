<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-size: 9px; }
        .title { font-size: 12px; font-weight: bold; text-align: center; }
        table { border-collapse: collapse; }

        .tbl-header td {
            font-size: 9px;
            padding: 2px 0;
        }

        .tbl-main, 
        .tbl-main th, 
        .tbl-main td {
            border: 0.3px solid #000;
        }

        .tbl-main th {
            text-align: center;
            vertical-align: middle;
            font-size: 8px;
        }

        .center { text-align: center; }
        .sign { text-align: center; }
        .small { font-size: 8px; }
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

<table width="100%" style="border-collapse:collapse;border:1px solid #000;">
    <tr>
        <td width="25%" style="border:1px solid #000;text-align:center;padding:5px;">
            <img src="{{ public_path('assets/img/Logofd.png') }}" width="50">
        </td>

        <td width="50%" style="border:1px solid #000;padding:0;text-align:center;vertical-align:middle;">
            <table width="100%" style="border-collapse:collapse;">
                <tr>
                    <td style="border-bottom:1px solid #000;padding:5px;font-size:19px;">
                        <b>FORM</b>
                    </td>
                </tr>
                <tr>
                    <td style="padding:5px;font-size:18px;">
                        <b>DATA RELEASE PACKING</b>
                    </td>
                </tr>
            </table>
        </td>

        <td width="25%" style="border:1px solid #000;padding:0;font-size:10px;">
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
                        : 01-04-2016
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


<br><br>

@php
    $dateFilter = request('date')
        ? \Carbon\Carbon::parse(request('date'))->format('d-m-Y')
        : 'All Dates';

    $jenisKemasanFilter = request('jenis_kemasan') ?: 'Semua Jenis Kemasan';
@endphp

<table width="100%" class="tbl-header">
    <tr>
        <td>Hari/Tgl : {{ $dateFilter }}</td>
        <td>Jenis Kemasan : {{ $jenisKemasanFilter }}</td>
    </tr>
</table>

<br>

<table width="100%" class="tbl-main small">
    <thead>
        <tr>
            <th class="center" width="4%">No.</th>
            <th class="center" width="24%">Nama Varian</th>
            <th class="center" width="24%">Kode Batch</th>
            <th class="center" width="14%">Best Before</th>
            <th class="center" width="10%">No. Palet</th>
            <th class="center" width="14%">Jumlah Release</th>
            <th class="center" width="10%">Paraf QC</th>
        </tr>
    </thead>

    <tbody>
        @forelse($release_packings as $packing)
        <tr>
            <td class="center" width="4%">
                {{ ($pageIndex * 10) + $loop->iteration }}
            </td>
            <td class="center" width="24%">{{ $packing->nama_produk ?? '-' }}</td>
            <td class="center" width="24%">{{ $packing->kode_produksi ?? '-' }}</td>
            <td class="center" width="14%">
                {{ $packing->expired_date ? \Carbon\Carbon::parse($packing->expired_date)->format('d-m-Y') : '-' }}
            </td>
            <td class="center" width="10%">{{ $packing->no_palet ?? '-' }}</td>
            <td class="center" width="14%">{{ $packing->release ?? '-' }}</td>
            <td class="center" width="10%">{{ $packing->username ?? '-' }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="7" class="center">Tidak ada data release packing</td>
        </tr>
        @endforelse
    </tbody>
</table>
<br>

@if($lastPage)
<table width="100%" style="border-collapse:collapse; border:0.3px solid #000;" class="small">
    <tr>
        <td style="border:0.3px solid #000; padding:5px;">
            @php
                $keterangan = $release_packings
                    ->filter(fn($item) => trim($item->keterangan ?? '') !== '')
                    ->values()
                    ->map(fn($item, $index) => '  -Data ke-' . ($index + 1) . ': ' . $item->keterangan)
                    ->unique()
                    ->implode('<br>');
            @endphp

            <b>Catatan :</b><br>{!! $keterangan ?: '-' !!}
        </td>
    </tr>
</table>
<br>

@php
    $namaSpv = $release_packings->every(fn($item) => !empty($item->nama_spv))
        ? $release_packings->first()->nama_spv
        : null;
@endphp
<br><br>
<table width="100%" class="small">
    <tr>
        <td width="70%"></td>

        <td width="30%" class="sign">
            Diverifikasi oleh,
            <br><br><br><br><br>

            @if($namaSpv)
                (<u>{{ $namaSpv }}</u>)
            @else
                (<u>Belum Semua Entry Disetujui Oleh SPV</u>)
            @endif

            <br>
            QC SPV
        </td>
    </tr>
</table>
@endif

</body>
</html>
