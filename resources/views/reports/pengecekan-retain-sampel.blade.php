<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-size: 8px; }
        table { border-collapse: collapse; }

        .title {
            font-size: 12px;
            font-weight: bold;
            text-align: center;
        }

        .small { font-size: 7px; }
        .center { text-align: center; }
        .right { text-align: right; }
        .sign { text-align: center; }

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

{{-- HEADER --}}

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
                        <b>PENGECEKAN RETAIN SAMPEL</b>
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
                        : 27-09-2017
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

<br>
<br>

@php
$firstRetain = $retains->first();
$date = $firstRetain ? \Carbon\Carbon::parse($firstRetain->tanggal)->format('d-m-Y') : '';
@endphp

<table width="100%" class="tbl-header">
    <tr>
        <td width="15%">Hari / Tanggal: {{ $date }}</td>
        <td width="85%"></td>
    </tr>
</table>

<br>

<table width="100%" class="tbl-main small">
    <tr>
        <th rowspan="2" class="center">Kode Batch</th>
        <th rowspan="2" class="center">Varian</th>
        <th rowspan="2" class="center">Panjang<br>(cm)</th>
        <th rowspan="2" class="center">Diameter<br>(cm)</th>
        <th colspan="4" class="center">Sensori</th>
        <th colspan="5" class="center">Temuan</th>
        <th colspan="3" class="center">Parameter Lab</th>
        <th rowspan="2" class="center">Keterangan</th>
    </tr>

    <tr>
        <th class="center">Rasa</th>
        <th class="center">Warna</th>
        <th class="center">Aroma</th>
        <th class="center">Texture</th>

        <th class="center">Jamur</th>
        <th class="center">Lendir</th>
        <th class="center">Pinehole</th>
        <th class="center">Kejepit</th>
        <th class="center">Seal<br>halus / lepas</th>

        <th class="center">Kadar Garam</th>
        <th class="center">Kadar Air</th>
        <th class="center">Mikro</th>
    </tr>

    @foreach($retains as $retain)
        @if($retain->items && $retain->items->count() > 0)

            @foreach($retain->items as $item)
            <tr>
                <td>{{ $item->kode_produksi ?? '' }}</td>
                <td>{{ $item->varian ?? '' }}</td>
                <td>{{ $item->panjang ?? '' }}</td>
                <td>{{ $item->diameter ?? '' }}</td>

                <td>{{ $item->sensori_rasa ?? '' }}</td>
                <td>{{ $item->sensori_warna ?? '' }}</td>
                <td>{{ $item->sensori_aroma ?? '' }}</td>
                <td>{{ $item->sensori_texture ?? '' }}</td>

                <td>{{ $item->temuan_jamur ? 'V' : '' }}</td>
                <td>{{ $item->temuan_lendir ? 'V' : '' }}</td>
                <td>{{ $item->temuan_pinehole ? 'V' : '' }}</td>
                <td>{{ $item->temuan_kejepit ? 'V' : '' }}</td>
                <td>{{ $item->temuan_seal ? 'V' : '' }}</td>

                <td>{{ $item->lab_garam ?? '' }}</td>
                <td>{{ $item->lab_air ?? '' }}</td>
                <td>{{ $item->lab_mikro ?? '' }}</td>

                <td>{{ $retain->keterangan ?? '' }}</td>
            </tr>
            @endforeach

        @endif
    @endforeach

</table>


@if($lastPage)

@php
    $namaSpv = null;

    if ($retains->isNotEmpty() && $retains->every(fn($retain) => !empty($retain->verified_by))) {
        $namaSpv = \App\Models\User::where('uuid', $retains->first()->verified_by)->value('name');
    }
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
