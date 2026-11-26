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
            padding: 2px 3px;
            text-align: center;
            vertical-align: middle;
            border: 0.8px solid #000;
            line-height: 1.2;
        }

        .tbl-main th {
            background: #d9e2f3;
            font-weight: bold;
        }

        .note td {
            vertical-align: top;
            line-height: 1.4;
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

    $pages = $pages->count() > 0 ? $pages : collect([collect()]);
@endphp

@foreach($pages as $pageIndex => $kartons)

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
        KONTROL LABELISASI KARTON
    </h2>
    <br>
    <br>

    <table width="100%" class="tbl-main" cellpadding="0" cellspacing="0">
        <tr>
            <th width="8%"><b>No.</b></th>
            <th width="42%"><b>Kode Produksi</b></th>
            <th width="50%" colspan="2"><b>Detail</b></th>
        </tr>

        @forelse($kartons as $karton)
            @php
                $path = $karton->kode_karton
                    ? public_path(str_replace('public/', 'storage/', $karton->kode_karton))
                    : null;

                $namaQc = $userMap[$karton->username] ?? $karton->username ?? '-';
            @endphp

            <tr>
                <td width="8%" rowspan="9" align="center">{{ ($pageIndex * 4) + $loop->iteration }}</td>
                <td width="42%" rowspan="9" align="center">
                    @if($path && file_exists($path))
                        <img src="{{ $path }}" width="300">
                    @else
                        Belum ada dokumentasi<br>Kode Karton
                    @endif
                </td>
                <td width="50%" colspan="2" align="center" style="background-color:#d9e2f3;"><b>Nama Produk</b></td>
            </tr>
            <tr>
                <td width="50%" colspan="2" align="center">{{ $karton->nama_produk ?? '-' }}</td>
            </tr>
            <tr>
                <td width="50%" colspan="2" align="center" style="background-color:#d9e2f3;"><b>Start - Finish</b></td>
            </tr>
            <tr>
                <td width="50%" colspan="2" align="center">
                    {{ $karton->waktu_mulai ? \Carbon\Carbon::parse($karton->waktu_mulai)->format('H:i') : '-' }}
                    -
                    {{ $karton->waktu_selesai ? \Carbon\Carbon::parse($karton->waktu_selesai)->format('H:i') : '-' }}
                </td>
            </tr>
            <tr>
                <td width="20%" align="center">Supplier</td>
                <td width="30%" align="center">{{ $karton->nama_supplier ?? '-' }}</td>
            </tr>
            <tr>
                <td width="20%" align="center">No. Lot Karton</td>
                <td width="30%" align="center">{{ $karton->no_lot ?? '-' }}</td>
            </tr>
            <tr>
                <td width="20%" align="center">Paraf Operator</td>
                <td width="30%" align="center">{{ $karton->nama_operator ?? '-' }}</td>
            </tr>
            <tr>
                <td width="20%" align="center">Paraf QC</td>
                <td width="30%" align="center">{{ $namaQc }}</td>
            </tr>
            <tr>
                <td width="20%" align="center">Paraf Koordinator</td>
                <td width="30%" align="center">{{ $karton->nama_koordinator ?? '-' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4" align="center">Tidak ada data</td>
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
                <td width="60%"></td>

                <td width="20%" align="center" style="vertical-align: top;">
                    Dibuat oleh :<br>
                    @if($namaPemeriksa)
                        <u>{{ $namaPemeriksa }}</u><br>
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
                    @elseif($kartons->count() > 0)
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