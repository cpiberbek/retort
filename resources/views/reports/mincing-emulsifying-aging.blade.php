<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 8px;
            color: #000;
        }

        table {
            border-collapse: collapse;
        }

        .title {
            font-size: 13px;
            font-weight: bold;
            text-align: center;
        }

        .tbl-header td {
            padding: 0;
            font-size: 8px;
            line-height: 1.2;
        }

        .tbl-main th,
        .tbl-main td {
            padding: 3px 2px;
            text-align: center;
            vertical-align: middle;
            border: 0.8px solid #000;
            font-size: 8px;
            line-height: 1.2;
        }

        .tbl-main th {
            background: #d9e2f3;
            font-weight: bold;
        }

        .note td {
            vertical-align: top;
            line-height: 1.4;
            font-size: 8px;
        }
    </style>
</head>

<body>

@php
    $namaPemeriksa = (!empty($pemeriksa) && $pemeriksa !== '-') ? $pemeriksa : '';
    $namaSpv       = ($semuaDisetujui && !empty($spv) && $spv !== '-') ? $spv : '';

    $makeQr = function ($nama, $tanggal) {
        if (empty($nama)) {
            return '';
        }
        $teks = 'Ditandatangani secara digital oleh ' . $nama;
        if (!empty($tanggal)) {
            $teks .= ' pada ' . $tanggal;
        }
        $barcode = new \TCPDF2DBarcode($teks, 'QRCODE,H');
        return base64_encode($barcode->getBarcodePngData(4, 4, [0, 0, 0]));
    };

    $qrPemeriksa = $makeQr($namaPemeriksa, $tglPemeriksa ?? '');
    $qrSpv       = $makeQr($namaSpv, $tglSpv ?? '');

    $firstRow = $produks->first();

    $tanggalLaporan = $firstRow && $firstRow->date
        ? \Carbon\Carbon::parse($firstRow->date)->format('d-m-Y')
        : '';

    $jam = function ($value) {
        return $value ? \Carbon\Carbon::parse($value)->format('H:i') : '-';
    };
@endphp

<div style="margin-left:-30px; margin-bottom:0;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0;">
        <tr>
            <td style="padding:0; line-height:1;">
                <span style="font-size:14pt;"><b>PT Charoen Pokphand Indonesia</b></span><br>
                <span style="font-size:14pt;"><b>Food Division</b></span>
            </td>
        </tr>
    </table>
</div>

<h2 class="title" style="margin:0; padding:0;">
    PEMERIKSAAN MINCING - EMULSIFYING - AGING
</h2>
<br>
<br>

<table width="100%" class="tbl-header" cellpadding="0" cellspacing="0">
    <tr>
        <td width="25%" align="left">Hari / Tanggal : {{ $tanggalLaporan }}</td>
        <td width="20%" align="center">Shift : {{ $firstRow->shift ?? '-' }}</td>
        <td width="55%" align="right">Nama Produk : {{ $firstRow->nama_produk ?? '-' }}</td>
    </tr>
</table>

<br>

@foreach($produks as $row)
    @php
        $nonPremix = array_values(json_decode($row->non_premix, true) ?? []);
        $premix    = array_values(json_decode($row->premix, true) ?? []);
        $suhu      = json_decode($row->suhu_sebelum_grinding, true) ?? [];

        $suhuText = [];
        foreach ($suhu as $s) {
            $suhuText[] = ($s['daging'] ?? '-') . ': ' . ($s['suhu'] ?? '-') . ' °C';
        }

        $awalAging  = $row->waktu_aging_emulsi_awal;
        $akhirAging = $row->waktu_aging_emulsi_akhir;
        $agingMenit = ($awalAging && $akhirAging)
            ? \Carbon\Carbon::parse($awalAging)->diffInMinutes(\Carbon\Carbon::parse($akhirAging))
            : 0;

        $qcRow  = $row->username_updated ?: $row->username;
        $spvRow = $row->nama_spv;
    @endphp

    <table width="100%" class="tbl-main" cellpadding="0" cellspacing="0" nobr="true">
        <tr>
            <td width="40%" align="left">Batch</td>
            <td width="60%" colspan="5" align="center">{{ $row->kode_produksi ?? '-' }}</td>
        </tr>
        <tr>
            <th width="40%" align="left">Parameter</th>
            <th width="19%">Kode</th>
            <th width="8%">(°C)</th>
            <th width="8%">*pH</th>
            <th width="13%">Kg</th>
            <th width="12%">Sens</th>
        </tr>
        <tr>
            <td width="40%" align="left">Preparation</td>
            <td width="60%" colspan="5" align="center">{{ $row->waktu_mulai ?? '-' }} s/d {{ $row->waktu_selesai ?? '-' }}</td>
        </tr>
        <tr>
            <td width="40%" align="left">Bahan baku dan bahan tambahan</td>
            <td width="19%">&nbsp;</td>
            <td width="8%">&nbsp;</td>
            <td width="8%">&nbsp;</td>
            <td width="13%">&nbsp;</td>
            <td width="12%">&nbsp;</td>
        </tr>

        @foreach($nonPremix as $item)
            <tr>
                <td width="40%" align="left">{{ $loop->iteration }}. {{ $item['nama_bahan'] ?? '-' }}</td>
                <td width="19%" align="center">
                    {{ !empty($item['inspection_uuid']) ? ($kodeBatchMap[$item['inspection_uuid']] ?? '-') : '-' }}
                </td>
                <td width="8%" align="center">{{ $item['suhu_bahan'] ?? '-' }}</td>
                <td width="8%" align="center">{{ $item['ph_bahan'] ?? '-' }}</td>
                <td width="13%" align="center">{{ $item['berat_bahan'] ?? '-' }}</td>
                <td width="12%" align="center">{{ $item['sensori'] ?? '-' }}</td>
            </tr>
        @endforeach

        @for($i = count($nonPremix); $i < 15; $i++)
            <tr>
                <td width="40%" align="left">{{ $i + 1 }}</td>
                <td width="19%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
                <td width="13%">&nbsp;</td>
                <td width="12%">&nbsp;</td>
            </tr>
        @endfor

        <tr>
            <th width="40%" align="left">Premix</th>
            <th width="35%" colspan="3">Kode</th>
            <th width="13%">Kg</th>
            <th width="12%">Sens</th>
        </tr>

        @foreach($premix as $item)
            <tr>
                <td width="40%" align="left">{{ $loop->iteration }}. {{ $item['nama_premix'] ?? '-' }}</td>
                <td width="35%" colspan="3" align="center">{{ $item['kode_premix'] ?? '-' }}</td>
                <td width="13%" align="center">{{ $item['berat_premix'] ?? '-' }}</td>
                <td width="12%" align="center">{{ $item['sensori_premix'] ?? '-' }}</td>
            </tr>
        @endforeach

        @for($i = count($premix); $i < 3; $i++)
            <tr>
                <td width="40%" align="left">{{ $i + 1 }}</td>
                <td width="35%" colspan="3">&nbsp;</td>
                <td width="13%">&nbsp;</td>
                <td width="12%">&nbsp;</td>
            </tr>
        @endfor

        <tr>
            <td width="40%" align="left">Suhu BEEF - SBB - SBL - MDM-CCM (sebelum Grinding)</td>
            <td width="60%" colspan="5" align="center">{{ implode(', ', $suhuText) ?: '-' }}</td>
        </tr>
        <tr>
            <td width="40%" align="left">Waktu mixing premix</td>
            <td width="48%" colspan="4" align="center">{{ $jam($row->waktu_mixing_premix_start) }} - {{ $jam($row->waktu_mixing_premix_end) }}</td>
            <td width="12%" align="right">{{ $row->waktu_mixing_premix ?? 0 }} menit</td>
        </tr>
        <tr>
            <td width="40%" align="left">Waktu Bowl cutter</td>
            <td width="48%" colspan="4" align="center">{{ $jam($row->waktu_bowl_cutter_start) }} - {{ $jam($row->waktu_bowl_cutter_end) }}</td>
            <td width="12%" align="right">{{ $row->waktu_bowl_cutter ?? 0 }} menit</td>
        </tr>
        <tr>
            <td width="40%" align="left">Waktu aging emulsi</td>
            <td width="48%" colspan="4" align="center">{{ $jam($awalAging) }} - {{ $jam($akhirAging) }}</td>
            <td width="12%" align="right">{{ $agingMenit }} menit</td>
        </tr>
        <tr>
            <td width="40%" align="left">Suhu akhir emulsi gel (°C)</td>
            <td width="60%" colspan="5" align="center">{{ $row->suhu_akhir_emulsi_gel ?? '-' }}</td>
        </tr>
        <tr>
            <td width="40%" align="left">Waktu Mixing</td>
            <td width="48%" colspan="4" align="center">{{ $jam($row->waktu_mixing_start) }} - {{ $jam($row->waktu_mixing_end) }}</td>
            <td width="12%" align="right">{{ $row->waktu_mixing ?? 0 }} menit</td>
        </tr>
        <tr>
            <td width="40%" align="left">Suhu akhir Mixing (°C)</td>
            <td width="60%" colspan="5" align="center">{{ $row->suhu_akhir_mixing ?? '-' }}</td>
        </tr>
        <tr>
            <td width="40%" align="left">Suhu Akhir Emulsifying (°C)</td>
            <td width="60%" colspan="5" align="center">{{ $row->suhu_akhir_emulsi ?? '-' }}</td>
        </tr>
        <tr>
            <td width="40%" align="left">PARAF QC</td>
            <td width="60%" colspan="5" align="center">{{ $userMap[$qcRow] ?? $qcRow ?? '-' }}</td>
        </tr>
        <tr>
            <td width="40%" align="left">PARAF Produksi</td>
            <td width="60%" colspan="5" align="center">{{ $spvRow ? ($userMap[$spvRow] ?? $spvRow) : '-' }}</td>
        </tr>
    </table>

    <div style="margin-top:5px; text-align:right;">
        <i>{{ $noDokumen ?? '-' }}</i>
    </div>

    <table width="100%" class="note" cellpadding="0" cellspacing="0" nobr="true">
        <tr>
            <td>
                <strong>Catatan :</strong><br>
                *pengukuran pH khusus untuk air produksi<br>
                {{ $row->catatan ?? '' }}
            </td>
        </tr>
    </table>

    <br>
@endforeach

<br>

<table width="100%" class="note" cellpadding="0" cellspacing="0" nobr="true">
    <tr>
        <td width="60%"></td>

        <td width="20%" align="center" style="vertical-align: top;">
            Dibuat oleh :<br>
            @if($namaPemeriksa)
                <br>
                <img src="data:image/png;base64,{{ $qrPemeriksa }}" width="60" height="60"><br>
                <u>{{ $namaPemeriksa }}</u><br>
                QC
            @endif
        </td>

        <td width="20%" align="center" style="vertical-align: top;">
            Diverifikasi oleh :<br>
            @if($namaSpv)
                <br>
                <img src="data:image/png;base64,{{ $qrSpv }}" width="60" height="60"><br>
                <u>{{ $namaSpv }}</u><br>
                QC SPV
            @else
                <i>Belum semua entry disetujui</i><br>
                <br>
                <br>
                <br>
                <br>
                <br>
                QC SPV
            @endif
        </td>
    </tr>
</table>

</body>
</html>