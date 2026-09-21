<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">

    <style>
        body {
            font-family: "Times New Roman", serif;
            font-size: 5.5px;
            margin: 0;
            padding: 0;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 1px;
            vertical-align: middle;
            text-align: center;
            line-height: 1;
        }

        .title {
            font-size: 9px;
            font-weight: bold;
            border: none;
            padding: 1px;
        }

        .header-info td {
            border: none;
            padding: 0;
            font-size: 6px;
            text-align: center;
        }

        .header-main th {
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
            padding: 1px;
            line-height: 1;
        }

        .signature {
            margin-top: 5px;
        }

        .signature td {
            border: none;
            height: 40px;
            vertical-align: bottom;
            text-align: center;
        }

        .signature-name {
            font-weight: bold;
        }
    </style>
</head>

<body>
<table style="width: 100%; border-collapse: collapse;">

    <tr>
        <td
            width="5%"
            rowspan="4"
            style="
                border: 1px solid #000;
                text-align: center;
                vertical-align: middle;
            "
        >
            <img
                src="{{ public_path('assets/img/Logofd.png') }}"
                width="35"
                height="35"
            >
        </td>

        <td
            width="80%"
            rowspan="4"
            class="title"
            style="
                border: 1px solid #000;
                text-align: center;
                vertical-align: middle;
                font-weight: bold;
            "
        >
            FORM
            <br><br>
            PEMERIKSAAN PERSONAL HYGIENE DAN KESEHATAN KARYAWAN
        </td>

        <td
            width="7.5%"
            style="
                border: 1px solid #000;
                font-size: 6px;
                height: 13px;
                text-align: left;
                vertical-align: middle;
            "
        >
            No. Dokumen
        </td>

        <td
            width="7.5%"
            style="
                border: 1px solid #000;
                font-size: 6px;
                height: 13px;
                text-align: left;
                vertical-align: middle;
            "
        >
            : {{ $noDokumen ?? '-' }}
        </td>
    </tr>

    <tr>
        <td
            style="
                border: 1px solid #000;
                font-size: 6px;
                height: 13px;
                text-align: left;
                vertical-align: middle;
            "
        >
            Revisi
        </td>

        <td
            style="
                border: 1px solid #000;
                font-size: 6px;
                height: 13px;
                text-align: left;
                vertical-align: middle;
            "
        >
            : {{ $revisi ?? 0 }}
        </td>
    </tr>

    <tr>
        <td
            style="
                border: 1px solid #000;
                font-size: 6px;
                height: 13px;
                text-align: left;
                vertical-align: middle;
            "
        >
            Tanggal Efektif
        </td>

        <td
            style="
                border: 1px solid #000;
                font-size: 6px;
                height: 13px;
                text-align: left;
                vertical-align: middle;
            "
        >
            : 17-07-2017
        </td>
    </tr>

    <tr>
        <td
            style="
                border: 1px solid #000;
                font-size: 6px;
                height: 13px;
                text-align: left;
                vertical-align: middle;
            "
        >
            Halaman
        </td>

        <td
            style="
                border: 1px solid #000;
                font-size: 6px;
                height: 13px;
                text-align: left;
                vertical-align: middle;
            "
        >
            : 1 dari 1
        </td>
    </tr>

</table>
<br>
<br>

<table style="width: 100%; border-collapse: collapse;">
    <tr>
        <td width="8%" style="border: none; font-weight: bold; text-align: left;">
            Hari / Tanggal
        </td>

        <td width="32%" style="border: none; text-align: left;">
            : {{ \Carbon\Carbon::parse($date)->format('d F Y') }}
        </td>

        <td width="7%" style="border: none; font-weight: bold; text-align: left;">
            Area
        </td>

        <td width="53%" style="border: none; text-align: left;">
            : {{ strtoupper($atribut) }}
        </td>
    </tr>
</table>

<br>
<br>


<table>

    <tr class="header-main">

        <th rowspan="2" width="2%">
            No
        </th>

        <th rowspan="2" width="5%">
            Nama
        </th>

        <th rowspan="2" width="4%">
            Waktu
        </th>

        <th colspan="23">
            PERSONAL HYGIENE
        </th>

        <th rowspan="2" width="3%">
            TOTAL<br>Skor
        </th>

        <th rowspan="2" width="2%">
            Keterangan
        </th>

        <th rowspan="2" width="4%">
            QC
        </th>

        <th rowspan="2" width="4%">
            Prod
        </th>

    </tr>

    <tr class="header-main">

        <th>Anting</th>
        <th>Kalung</th>
        <th>Cincin</th>
        <th>Jam<br>Tangan</th>
        <th>Peniti</th>
        <th>Bros</th>
        <th>Payet</th>
        <th>Softlens</th>
        <th>Eyelashes</th>
        <th>Seragam</th>
        <th>Boot</th>
        <th>Masker</th>
        <th>Ciput/<br>Hairnet</th>
        <th>Kuku</th>
        <th>Parfum</th>
        <th>Make Up</th>
        <th>Diare</th>
        <th>Demam</th>
        <th>Luka<br>Bakar</th>
        <th>Batuk</th>
        <th>Radang</th>
        <th>Influenza</th>
        <th>Sakit<br>Mata</th>

    </tr>

    @php
        $no = 1;
    @endphp

    @foreach ($rekap as $nama => $data)

        <tr>

            <td>
                {{ $no }}
            </td>

            <td>
                {{ $nama }}
            </td>

            <td>
                {{ $data['pukul'] ?? '-' }}
            </td>

            @foreach ($attributes as $attr)

                <td>
                    {{ ($data[$attr] ?? 0) == 1 ? 'V' : '-' }}
                </td>

            @endforeach

            <td>
                {{ array_sum(
                    array_map(
                        fn($attr) => $data[$attr] ?? 0,
                        $attributes
                    )
                ) }}
            </td>

            <td>
                {{ $data['keterangan'] ?? '' }}
            </td>

            <td>
                {{ $username }}
            </td>

            <td>
                {{ $namaProduksi }}
            </td>

        </tr>

        @php
            $no++;
        @endphp

    @endforeach

</table>

<br>
<br>

<table class="signature">

    <tr>

        <td width="50%">
            
        </td>

        <td width="50%">
            Diketahui oleh,
            <br><br><br>

            <span class="signature-name">
                {{ \App\Models\Gmp::where('plant', auth()->user()->plant)
                    ->where('date', $date)
                    ->whereNotNull('nama_spv')
                    ->value('nama_spv') ?? '-' }}
            </span>

            <br>
            QC.SPV.			
        </td>

    </tr>

</table>

</body>
</html>