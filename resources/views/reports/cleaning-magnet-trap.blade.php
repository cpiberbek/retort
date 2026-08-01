<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-size: 9px; }
        .title { font-size: 12px; font-weight: bold; text-align: center; }
        table { border-collapse: collapse; }

        .tbl-header td {
            font-size: 11px;
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

{{-- HEADER LOGO + TITLE --}}
<table width="100%" style="border-collapse:collapse;border:1px solid #000;table-layout:fixed;">
    <tr>
        <td width="25%" style="border:1px solid #000;text-align:center;padding:5px;">
            <img src="{{ public_path('assets/img/Logofd.png') }}" width="50">
        </td>

        <td width="50%" style="border:1px solid #000;text-align:center;font-size:18px;padding:5px;">
            <b>FORM</b><br>
            <b>VERIFIKASI MAGNET TRAP</b>
        </td>

        <td width="25%" style="border:1px solid #000;font-size:10px;padding:0;vertical-align:top;">

            <table width="100%" style="border-collapse:collapse;height:100%;">
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

<h2 class="title">CHECKLIST CLEANING MAGNET TRAP</h2>
<br>
<br>

@php
    $dateFilter = request('date') ? \Carbon\Carbon::parse(request('date'))->format('d-m-Y') : 'All Dates';
@endphp

<table width="100%" class="tbl-header">
</table>

<table width="100%" class="tbl-main small" style="border-collapse:collapse;">

    <tr>
        <th class="center" rowspan="3" style="padding:0;">No.</th>
        <th class="center" rowspan="3" style="padding:0;">Pukul</th>

        <th class="center" colspan="6" style="padding:0;">
           Tanggal: {{ $dateFilter }}
        </th>
    </tr>

    <tr>
        <th class="center" rowspan="2" style="padding:0;">Batch Ke-</th>
        <th class="center" rowspan="2" style="padding:0;">Jumlah Temuan</th>
        <th class="center" rowspan="2" style="padding:0;">Keterangan</th>

        <th class="center" colspan="3" style="padding:0;">
            PARAF
        </th>
    </tr>

    <tr>
        <th class="center" style="padding:0;">QC</th>
        <th class="center" style="padding:0;">Prod</th>
        <th class="center" style="padding:0;">Eng</th>
    </tr>

    @forelse($magnetTraps as $index => $item)

    @php
        $kode_batch_text = \App\Models\Mincing::where('uuid', $item->kode_batch)->value('kode_produksi') ?? $item->kode_batch;
    @endphp

    <tr>
        <td class="center" style="padding:0;">{{ $index + 1 }}</td>

        <td class="center" style="padding:0;">
            {{ $item->pukul ? \Carbon\Carbon::parse($item->pukul)->format('H:i') : '-' }}
        </td>

        <td class="center" style="padding:0;">
            {{ $kode_batch_text }}
        </td>

        <td class="center" style="padding:0;">
            {{ $item->jumlah_temuan ?? '-' }}
        </td>

        <td class="center" style="padding:0;">
            {{ Str::limit($item->keterangan ?? '-', 30) }}
        </td>

        <td class="center" style="padding:0;">
            {{ $item->username ?? '-' }}
        </td>

        <td class="center" style="padding:0;">
            {{ $item->produksi->name ?? $item->produksi_id ?? '-' }}
        </td>

        <td class="center" style="padding:0;">
            {{ $item->engineer->name ?? $item->engineer_id ?? '-' }}
        </td>
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
    </tr>
    @endfor

    @endforelse

</table>
<br>
{{-- <table width="100%">
    <tr>
        <td width="75%"></td>
        <td width="25%" align="right" style="font-style: italic;">
            {{ $noDokumen }}
        </td>
    </tr>
</table> --}}
<br>

<table width="100%" class="small">
    <tr>
        <td align="right">
            {{-- SIGN --}}
            <table width="100%">
                <tr>
                    <td width="70%"></td>
                    <td width="30%" class="sign">
                        Disetujui oleh,<br><br><br><br>
                       <u>({{ \App\Models\User::where('uuid', $item->verified_by_spv_uuid)->value('name') ?? '___________________' }})</u><br>
                        QC SPV
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>



</body>
</html>
