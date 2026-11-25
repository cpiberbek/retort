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

        .tbl-main th,
        .tbl-main td {
            padding: 4px 2px;
            text-align: center;
            vertical-align: middle;
            border: 0.8px solid #000;
            font-size: 8px;
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

    $perPage = 12;
    $pages   = $items->count() > 0 ? $items->values()->chunk($perPage) : collect([collect()]);
@endphp

@foreach($pages as $pageIndex => $pageItems)

    <div style="margin-left:-30px; margin-bottom:0;">
        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0;">
            <tr>
                {{-- <td width="55" style="padding:0;">
                    <img src="{{ public_path('assets/img/Logo CPI.png') }}" width="50">
                </td> --}}
                <td style="padding:0; line-height:1;">
                    <span style="font-size:14pt;"><b>PT Charoen Pokphand Indonesia</b></span><br>
                    <span style="font-size:14pt;"><b>Food Division</b></span>
                </td>
            </tr>
        </table>
    </div>

    <h2 class="title" style="margin:0; padding:0;">
        PENGAMBILAN SAMPEL
    </h2>
    <br>
    <br>

    <table width="100%" class="tbl-main" cellpadding="0" cellspacing="0">
        <tr>
            <th width="12%"><b>Tgl. Pengambilan</b></th>
            <th width="16%"><b>Jenis Sampel</b></th>
            <th width="24%"><b>Nama Produk</b></th>
            <th width="16%"><b>Kode Produk</b></th>
            <th width="22%"><b>Keterangan</b></th>
            <th width="10%"><b>Paraf SPV</b></th>
        </tr>

        @foreach($pageItems as $item)
            <tr>
                <td width="12%" align="center">{{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }}</td>
                <td width="16%" align="center">{{ $item->jenis_sampel }}</td>
                <td width="24%" align="center">{{ $item->nama_produk }}</td>
                <td width="16%" align="center">{{ $kodeMap[$item->kode_produksi] ?? $item->kode_produksi }}</td>
                <td width="22%" align="center">{{ $item->keterangan }}</td>
                <td width="10%" align="center">{{ $item->nama_spv ? ($userMap[$item->nama_spv] ?? $item->nama_spv) : 'Belum disetujui' }}</td>
            </tr>
        @endforeach

        @for($i = $pageItems->count(); $i < $perPage; $i++)
            <tr>
                <td width="12%">&nbsp;</td>
                <td width="16%">&nbsp;</td>
                <td width="24%">&nbsp;</td>
                <td width="16%">&nbsp;</td>
                <td width="22%">&nbsp;</td>
                <td width="10%">&nbsp;</td>
            </tr>
        @endfor
    </table>

    <div style="margin-top:5px; text-align:right;">
        <i>{{ $noDokumen ?? '-' }}</i>
    </div>

    @if($loop->last)

        <br>
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
                    @elseif($items->count() > 0)
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