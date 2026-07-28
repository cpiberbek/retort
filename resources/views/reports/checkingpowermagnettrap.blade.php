<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-size: 9px; }
        .title { font-size: 12px; font-weight:bold; text-align:center; }
        table { border-collapse:collapse; }

        .tbl-header td {
            font-size:9px;
            padding:2px 0;
        }

        .tbl-main,
        .tbl-main th,
        .tbl-main td {
            border:0.3px solid #000;
            text-align:center;
            vertical-align:middle;
            padding:2px;
        }

        .tbl-main th {
            font-size:8px;
        }

        .center {
            text-align:center;
        }

        .small {
            font-size:8px;
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
                        : 27/12/2021
                    </td>
                </tr>

                <tr>
                    <td style="border-right:1px solid #000;padding:3px;">
                        Halaman
                    </td>
                    <td style="padding:3px;">
                        : 1 dari 1
                    </td>
                </tr>
            </table>

        </td>
    </tr>
</table>

<br>

<h2 class="title">CHECKLIST CLEANING MAGNET TRAP</h2>

<br>

<table width="100%" class="tbl-main small">

    <tr>
        <th rowspan="3">NO</th>
        <th colspan="11" style="text-align:left;">
            Periode : {{ $month ? \Carbon\Carbon::parse($month)->locale('id')->translatedFormat('F Y') : '-' }}
        </th>
    </tr>

    <tr>
        <th rowspan="2">TANGGAL</th>

        <th colspan="3">
            KEKUATAN MEDAN MAGNET (GAUSS)<br>
            MAGNET KE-
        </th>

        <th colspan="2">
            <span style="color:red">*</span> PARAMETER SETTINGAN
        </th>

        <th rowspan="2">
            <span style="color:red">*</span> KONDISI<br>
            MAGNET<br>
            TRAP
        </th>

        <th rowspan="2">
            <span style="color:red">*</span> KETERANGAN
        </th>

        <th colspan="3">
            PETUGAS
        </th>
    </tr>

    <tr>
        <th>1</th>
        <th>2</th>
        <th>3</th>

        <th>SESUAI</th>
        <th>TIDAK SESUAI</th>

        <th>QC</th>
        <th>PROD</th>
        <th>ENG</th>
    </tr>

    @forelse($magnetTraps as $index => $item)
    <tr>
        <td>{{ $index + 1 }}</td>
        <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}</td>
        <td>{{ $item->kekuatan_median_1 ?? '-' }}</td>
        <td>{{ $item->kekuatan_median_2 ?? '-' }}</td>
        <td>{{ $item->kekuatan_median_3 ?? '-' }}</td>
        <td>{{ $item->parameter_sesuai == 1 ? 'V' : '-' }}</td>
        <td>{{ $item->parameter_sesuai == 0 ? 'X' : '-' }}</td>
        <td>{{ $item->kondisi_magnet_trap ?? '-' }}</td>
        <td>{{ Str::limit($item->keterangan ?? '-', 30) }}</td>
        <td>{{ $item->petugas_qc ?? '-' }}</td>
        <td>-</td>
        <td>{{ $item->petugas_eng ?? '-' }}</td>
    </tr>
    @empty
    @for($i = 1; $i <= 6; $i++)
    <tr>
        <td>{{ $i }}</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    @endfor
    @endforelse

</table>

<br>

<table width="100%" class="small" style="border-collapse:collapse;border:1px solid #000;">

<tr>

<td width="60%" style="border:1px solid #000;padding:5px;text-align:left;">

<b><span style="color:red">*</span> Keterangan Pengisian data :</b><br>

<u>- Kolom Parameter Settingan :</u><br>
Beri tanda V untuk kondisi baik<br>
Beri tanda X untuk kondisi tidak sesuai<br><br>

<u>- Kolom Kondisi Magnet trap diisi dengan keadaan Visual Magnet tersebut</u><br>
(contoh : magnet/slongsong gembel, penyok atau dalam keadaan baik,
sempurna, dll)<br><br>

<u>- Kolom Keterangan Mengacu pada Standart Kekuatan Medan Magnet :</u><br>
>= 8000 Gauss maka Magnet masih bisa digunakan<br>
< 8000 Gauss maka Magnet harus dilakukan penggantian

</td>


<td width="40%" style="border:1px solid #000;text-align:center;">

Disetujui oleh,<br><br><br><br>

@php
    $allVerified = $magnetTraps->every(fn($item) => $item->status_spv == 1);
    $firstVerified = $allVerified ? $magnetTraps->first() : null;
@endphp

<u>(
{{ $firstVerified ? \App\Models\User::where('uuid', $firstVerified->verified_by)->value('name') : 'Belum Semua Entry di periode ini terverifikasi' }}
)</u><br>
QC SPV

</td>

</tr>

</table>

</body>
</html>