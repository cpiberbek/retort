<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 7px;
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
            line-height: 1;
        }

        .tbl-main th,
        .tbl-main td {
            padding: 3px 1px;
            text-align: center;
            vertical-align: middle;
            border: 0.8px solid #000;
            font-size: 7px;
            line-height: 1.2;
        }

        .tbl-main th {
            background: #d9e2f3;
            font-weight: bold;
            line-height: 1.15;
        }

        .note td {
            vertical-align: top;
            line-height: 1.4;
            font-size: 7px;
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

    $dateFilter = request('date')
        ? \Carbon\Carbon::parse(request('date'))->format('d-m-Y')
        : 'Semua Tanggal';

    $pages = $prepackings->count() > 0 ? $prepackings->chunk(6) : collect([collect()]);
@endphp

@foreach($pages as $pageIndex => $pageItems)

    <div style="margin-left:-30px; margin-bottom:0;">
        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0;">
            <tr>
                <td width="55" style="padding:0;">
                    <img src="{{ public_path('assets/img/Logo CPI.png') }}" width="50">
                </td>
                <td style="padding:0; line-height:1;">
                    <span style="font-size:14pt;"><b>PT Charoen</b></span><br>
                    <span style="font-size:14pt;"><b>Pokphand Indonesia</b></span><br>
                    <span style="font-size:14pt;"><b>Food Division</b></span>
                </td>
            </tr>
        </table>
    </div>

    <h2 class="title" style="margin:0; padding:0;">
        PEMERIKSAAN PRE PACKING
    </h2>
    <br>
    <br>

    <table width="100%" class="tbl-header" cellpadding="0" cellspacing="0">
        <tr>
            <td width="100%">Hari / Tanggal : {{ $dateFilter }}</td>
        </tr>
    </table>

    <br>

    <table width="100%" class="tbl-main" cellpadding="0" cellspacing="0">
        <tr>
            <th rowspan="2" width="3%"><b>No</b></th>
            <th colspan="2" width="21%"><b>Varian</b></th>
            <th rowspan="2" width="7%"><b>No.<br>Conveyor</b></th>
            <th rowspan="2" width="7%"><b>Suhu<br>Varian</b></th>
            <th rowspan="2" width="8%"><b>Bagian<br>Badan Sosis</b></th>
            <th colspan="2" width="14%"><b>Air (%)</b></th>
            <th colspan="2" width="14%"><b>Minyak (%)</b></th>
            <th colspan="2" width="16%"><b>Berat Varian per</b></th>
            <th rowspan="2" width="10%"><b>PARAF<br>QC</b></th>
        </tr>
        <tr>
            <th width="12%"><b>Nama</b></th>
            <th width="9%"><b>Kode</b></th>
            <th width="7%"><b>Basah</b></th>
            <th width="7%"><b>Kering</b></th>
            <th width="7%"><b>Basah</b></th>
            <th width="7%"><b>Kering</b></th>
            <th width="8%"><b>pcs</b></th>
            <th width="8%"><b>Toples<br>(berat kotor)</b></th>
        </tr>

        @forelse($pageItems as $index => $prepacking)
            @php
                $number = ($pageIndex * 6) + $index + 1;

                $kondisi = json_decode($prepacking->kondisi_produk, true) ?? [];
                $berat   = json_decode($prepacking->berat_produk, true) ?? [];
                $suhuArr = json_decode($prepacking->suhu_produk, true) ?? [];

                $airBasahUjung     = $kondisi['basah_air_ujung'] ?? 0;
                $airKeringUjung    = $kondisi['kering_air_ujung'] ?? 0;
                $minyakBasahUjung  = $kondisi['basah_minyak_ujung'] ?? 0;
                $minyakKeringUjung = $kondisi['kering_minyak_ujung'] ?? 0;

                $airBasahSeal      = $kondisi['basah_air_seal'] ?? 0;
                $airKeringSeal     = $kondisi['kering_air_seal'] ?? 0;
                $minyakBasahSeal   = $kondisi['basah_minyak_seal'] ?? 0;
                $minyakKeringSeal  = $kondisi['kering_minyak_seal'] ?? 0;

                $pcs1    = $berat['pcs_1'] ?? 0;
                $pcs2    = $berat['pcs_2'] ?? 0;
                $pcs3    = $berat['pcs_3'] ?? 0;
                $toples1 = $berat['toples_1'] ?? 0;
                $toples2 = $berat['toples_2'] ?? 0;
                $toples3 = $berat['toples_3'] ?? 0;

                $suhu1 = $suhuArr['suhu_1'] ?? $suhuArr[0] ?? '-';
                $suhu2 = $suhuArr['suhu_2'] ?? $suhuArr[1] ?? '-';
                $suhu3 = $suhuArr['suhu_3'] ?? $suhuArr[2] ?? '-';
            @endphp

            <tr>
                <td width="3%"  rowspan="3" align="center">{{ $number }}</td>
                <td width="12%" rowspan="3" align="center">{{ $prepacking->nama_produk ?? '-' }}</td>
                <td width="9%"  rowspan="3" align="center">{{ $kodeMap[$prepacking->kode_produksi] ?? '-' }}</td>
                <td width="7%"  rowspan="3" align="center">{{ $prepacking->conveyor ?? '-' }}</td>
                <td width="7%"  align="center">{{ $suhu1 }}</td>
                <td width="8%"  align="center">Ujung</td>
                <td width="7%"  align="center">{{ $airBasahUjung }}</td>
                <td width="7%"  align="center">{{ $airKeringUjung }}</td>
                <td width="7%"  align="center">{{ $minyakBasahUjung }}</td>
                <td width="7%"  align="center">{{ $minyakKeringUjung }}</td>
                <td width="8%"  align="center">{{ $pcs1 }}</td>
                <td width="8%"  align="center">{{ $toples1 }}</td>
                <td width="10%" rowspan="3" align="center">{{ $userMap[$prepacking->username] ?? $prepacking->username ?? '-' }}</td>
            </tr>
            <tr>
                <td width="7%" align="center">{{ $suhu2 }}</td>
                <td width="8%" align="center">Seal</td>
                <td width="7%" align="center">{{ $airBasahSeal }}</td>
                <td width="7%" align="center">{{ $airKeringSeal }}</td>
                <td width="7%" align="center">{{ $minyakBasahSeal }}</td>
                <td width="7%" align="center">{{ $minyakKeringSeal }}</td>
                <td width="8%" align="center">{{ $pcs2 }}</td>
                <td width="8%" align="center">{{ $toples2 }}</td>
            </tr>
            <tr>
                <td width="7%" align="center">{{ $suhu3 }}</td>
                <td width="8%" align="center">Total</td>
                <td width="7%" align="center">{{ $airBasahUjung + $airBasahSeal }}</td>
                <td width="7%" align="center">{{ abs(100 - ($airKeringUjung + $airKeringSeal)) }}</td>
                <td width="7%" align="center">{{ $minyakBasahUjung + $minyakBasahSeal }}</td>
                <td width="7%" align="center">{{ abs(100 - ($minyakKeringUjung + $minyakKeringSeal)) }}</td>
                <td width="8%" align="center">{{ $pcs3 }}</td>
                <td width="8%" align="center">{{ $toples3 }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="13" align="center">Tidak ada data</td>
            </tr>
        @endforelse
    </table>

    <div style="margin-top:5px; text-align:right;">
        <i>{{ $noDokumen ?? '-' }}</i>
    </div>

    @if($loop->last)

        <br>
        <br>

        <table width="100%" class="note" cellpadding="0" cellspacing="0" nobr="true">
            <tr>
                <td width="60%">
                    <strong>CATATAN :</strong><br>
                    {{ $catatan ?: '-' }}
                </td>

                <td width="20%" align="center" style="vertical-align: top;">
                    Dibuat oleh :<br>
                    @if($namaPemeriksa)
                        <u>{{ $namaPemeriksa }}</u><br>
                        <br>
                        <img src="data:image/png;base64,{{ $qrPemeriksa }}" width="60" height="60"><br>
                        QC
                    @endif
                </td>

                <td width="20%" align="center" style="vertical-align: top;">
                    Diverifikasi oleh :<br>
                    @if($namaSpv)
                        <u>{{ $namaSpv }}</u><br>
                        <br>
                        <img src="data:image/png;base64,{{ $qrSpv }}" width="60" height="60"><br>
                        QC SPV
                    @elseif($prepackings->count() > 0)
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

    @else
        <br pagebreak="true" />
    @endif

@endforeach

</body>
</html>