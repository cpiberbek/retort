<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
    body { font-size: 9px; }
    table { border-collapse: collapse; }

    .title { font-weight: bold; font-size: 12px; text-align: center; }
    .small { font-size: 8px; }
    .center { text-align: center; }
    .right { text-align: right; }

    .box, .box td, .box th {
        border: 0.3px solid #000;
    }

    .box td {
        padding: 4px;
        vertical-align: top;
    }

    .header td {
        border: 0.3px solid #000;
        padding: 4px;
        vertical-align: middle;
    }

    .sign {
        text-align: center;
    }
    .no-border {
    border: none !important;
    }
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
{{-- BODY --}}

<table width="101%" style="border-collapse:collapse;border:1px solid #000;">
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
                        <b>KONTROL LABELISASI KARTON</b>
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
<table width="101%" class="box">
    <tr>
        <th width="10%" class="center">No.</th>
        <th width="50%" class="center">Bukti Kode</th>
        <th width="40%" colspan="2" class="center">Detail</th>
    </tr>

    @foreach($kartons as $karton)
    <tr>
    <td class="center" rowspan="9" style="vertical-align:middle;">
        {{ $loop->iteration }}
    </td>

    <td rowspan="9" class="no-border center" style="vertical-align:middle; text-align:center;">
        @php
            $path = $karton->kode_karton
                ? public_path(str_replace('public/', 'storage/', $karton->kode_karton))
                : null;
        @endphp

        @if($path && file_exists($path))
            <img src="{{ $path }}" style="width:180px; height:auto;">
        @else
            <div style="font-size:10px; color:#666;">
                Belum ada dokumentasi<br>Kode Karton
            </div>
        @endif
    </td>

    <td colspan="2" class="center">
        <strong>Nama Produk</strong>
    </td>
    </tr>

    <tr>
        <td colspan="2" class="center">
            {{ $karton->nama_produk ?? '-' }}
        </td>
    </tr>

    <tr>
        <td colspan="2" class="center">
            <strong>Start-Finish</strong>
        </td>
    </tr>

    <tr>
        <td colspan="2" class="center">
            {{ \Carbon\Carbon::parse($karton->waktu_mulai)->format('H:i') }}
            -
            {{ \Carbon\Carbon::parse($karton->waktu_selesai)->format('H:i') }}
        </td>
    </tr>

    <tr>
        <td>Supplier</td>
        <td>{{ $karton->nama_supplier ?? '-' }}</td>
    </tr>

    <tr>
        <td>No. Lot Karton</td>
        <td>{{ $karton->no_lot ?? '-' }}</td>
    </tr>

    <tr>
        <td>Paraf Operator</td>
        <td>{{ $karton->nama_operator ?? '-' }}</td>
    </tr>

    <tr>
        <td>Paraf QC</td>
        <td>{{ $karton->username ?? '-' }}</td>
    </tr>

    <tr>
        <td>Paraf Koordinator</td>
        <td>{{ $karton->nama_koordinator ?? '-' }}</td>
    </tr>

    @endforeach
</table>

@if($pageIndex + 1 == $totalPage)
    @php
        $namaSpv = $kartons->every(fn($item) => !empty($item->nama_spv))
            ? $kartons->first()->nama_spv
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
</html>
