<!DOCTYPE html>
<html>
<head>
    <title>Laporan Verifikasi Timer Chamber</title>
    <style>
        /* CSS Dasar untuk TCPDF */
        body {
            font-family: helvetica;
            font-size: 8pt; /* Ukuran font standar */
        }

        /* Reset Table */
        table {
            border-collapse: collapse;
            width: 100%;
            border-spacing: 0;
        }

        /* Style Border */
        th, td {
            border: 1px solid #000000;
        }

        /* Utility Classes */
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }
        
        /* Header Style (Tanpa Border) */
        .header-table td { border: none; }
        .title { font-size: 14pt; font-weight: bold; text-align: center; }
        .sub-title { font-size: 9pt; }
        
        /* Background Colors */
        .bg-header { background-color: #E0E0E0; font-weight: bold; }
        .bg-sub-header { background-color: #F0F0F0; font-weight: bold; }

        /* Font Sizes Specific */
        .f-small { font-size: 7pt; } /* Untuk data tabel yang padat */
        .f-norm { font-size: 8pt; }

        /* Status Colors */
        .status-ok { color: #006400; font-weight: bold; }
        .status-rev { color: #8B0000; font-weight: bold; }

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

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="border:none;margin:0;padding:0;">
    <tr>
        <td width="55" style="border:none;padding:0;">
            <img src="{{ public_path('assets/img/Logo CPI.png') }}" width="50">
        </td>
        <td style="border:none;padding:0;line-height:1.1;">
            <span style="font-size:12pt;"><b>PT Charoen</b></span><br>
            <span style="font-size:12pt;"><b>Pokphand Indonesia</b></span><br>
            <span style="font-size:12pt;"><b>Food Division</b></span>
        </td>
    </tr>
</table>

<h2 class="title" style="margin:5px 0 0 0;">LAPORAN VERIFIKASI TIMER CHAMBER</h2>

@php
    $groups = $items->groupBy(fn($item) => $item->date . '_' . $item->shift);
    $rentang = [5, 10, 20, 30, 60];
@endphp

@foreach($groups as $group)

    @foreach($group as $item)

        @php
            $verifikasi = json_decode($item->verifikasi, true) ?? [];
        @endphp

        <table cellpadding="3" cellspacing="0" border="1" width="100%">
            <tr>
                <td width="50%" style="font-weight:bold;">
                    Hari/Tanggal :
                    {{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }}
                </td>
                <td width="50%" style="font-weight:bold;">
                    Shift : {{ $item->shift }}
                </td>
            </tr>
        </table>

        <table cellpadding="2" cellspacing="0" border="1" width="100%">

            <tr>
                <th colspan="2" align="center" style="font-weight:bold;">Nomer Chamber</th>

                @foreach($verifikasi as $i => $chamber)
                    <th colspan="5" align="center" style="font-weight:bold;">Chamber {{ $i + 1 }}</th>
                @endforeach
            </tr>

            <tr>
                 <th colspan="2" align="center" style="font-weight:bold;">RENTANG UKUR</th>

                @foreach($verifikasi as $chamber)
                    <th colspan="2" align="center" style="font-weight:bold;">PLC</th>
                    <th colspan="2" align="center" style="font-weight:bold;">STOPWATCH</th>
                    <th align="center"  style="font-weight:bold;">FAKTOR</th>
                @endforeach
            </tr>

            <tr>
                <th align="center" style="font-weight:bold;">MENIT</th>
                <th align="center" style="font-weight:bold;">DETIK</th>

                @foreach($verifikasi as $chamber)
                    <th align="center" style="font-weight:bold;">MENIT</th>
                    <th align="center" style="font-weight:bold;">DETIK</th>
                    <th align="center" style="font-weight:bold;">MENIT</th>
                    <th align="center" style="font-weight:bold;">DETIK</th>
                    <th align="center" style="font-weight:bold;">Koreksi</th>
                @endforeach
            </tr>

            @foreach($rentang as $m)
                <tr>
                    <td align="center">{{ $m }}</td>
                    <td align="center">00</td>

                    @foreach($verifikasi as $chamber)

                        <td align="center">
                            {{ sprintf('%02d', $chamber["plc_menit_$m"] ?? 0) }}
                        </td>
                        <td align="center">
                            {{ sprintf('%02d', $chamber["plc_detik_$m"] ?? 0) }}
                        </td>

                        <td align="center">
                            {{ sprintf('%02d', $chamber["stopwatch_menit_$m"] ?? 0) }}
                        </td>
                        <td align="center">
                            {{ sprintf('%02d', $chamber["stopwatch_detik_$m"] ?? 0) }}
                        </td>

                        <td align="center">
                            {{ $chamber["faktor_koreksi_$m"] ?? '-' }}
                        </td>

                    @endforeach
                </tr>
            @endforeach

            <tr>
                <td colspan="2" style="font-weight:bold;">PARAF SPV</td>
                <td colspan="{{ count($verifikasi) * 5 }}" style="font-weight:bold;">
                    {{ $item->nama_spv ?? '-' }}
                </td>
            </tr>

            <tr>
                <td colspan="2" style="font-weight:bold;">PARAF PRODUKSI</td>
                <td colspan="{{ count($verifikasi) * 5 }}">
                    {{ $item->username_updated ?? $item->username ?? '-' }}
                </td>
            </tr>

        </table>

        <br><br>

    @endforeach

@endforeach

@php
    $spvNames = [];
    $allApproved = true;

    foreach ($groups as $group) {
        foreach ($group as $item) {
            if (empty($item->nama_spv)) {
                $allApproved = false;
                break 2;
            }

            $spvNames[$item->nama_spv] = $item->nama_spv;
        }
    }

    $spvName = $allApproved ? implode(', ', $spvNames) : 'Masih ada entry yang belum di approve';
@endphp

<table width="100%">
    <tr>
        <td width="75%"></td>
        <td width="25%" align="right" style="font-style: italic;">
            {{ $noDokumen }}
        </td>
    </tr>
</table>

<br>
<br>
<br>
<table class="header-table" cellpadding="2" cellspacing="0" style="page-break-inside: avoid;">
    <tr>
        <td width="80%"></td>
        <td width="20%" class="text-center f-norm">
            Disetujui Oleh,<br>
            QC Supervisor
            <br><br><br><br><br>

            <div style="width:80%; margin:0 auto; text-align:center;">
                <span style="display:inline-block; min-width:120px; border-bottom:1px solid #000; padding-bottom:2px;">
                    (<u>{{ $spvName }}</u>)
                </span>
            </div>

        </td>
    </tr>
</table>

</body>
</html>