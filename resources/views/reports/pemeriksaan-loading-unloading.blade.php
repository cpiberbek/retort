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
            padding: 1px 2px;
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
    $namaPemeriksa  = (!empty($pemeriksa) && $pemeriksa !== '-') ? $pemeriksa : '';
    $namaWarehouse  = (!empty($warehouse) && $warehouse !== '-') ? $warehouse : '';
    $namaSpv        = ($semuaDisetujui && !empty($spv) && $spv !== '-') ? $spv : '';

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
    $qrWarehouse = $makeQr($namaWarehouse, $tglPemeriksa ?? '');
    $qrSpv       = $makeQr($namaSpv, $tglSpv ?? '');

    $kondisiRaw = $header->kondisi_mobil ?? [];
    if (is_string($kondisiRaw)) {
        $kondisiRaw = json_decode($kondisiRaw, true) ?? [];
    }
    $kondisiMobil = collect($kondisiRaw)
        ->map(fn ($item) => ucwords(str_replace('_', ' ', $item)))
        ->implode(', ');

    $tanggal = $header ? \Carbon\Carbon::parse($header->tanggal)->format('d-m-Y') : '';

    $pages = $allDetails->count() > 0 ? $allDetails->values()->chunk(15) : collect([collect()]);
@endphp

@foreach($pages as $pageIndex => $pageDetails)

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
        PEMERIKSAAN LOADING – UNLOADING PRODUK
    </h2>
    <br>
    <br>

    <table width="100%" class="tbl-header" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td width="14%">Hari / Tanggal</td>
            <td width="19%">: {{ $tanggal }}</td>
            <td width="14%">Jam Mulai</td>
            <td width="19%">: {{ $header->jam_mulai ?? '' }}</td>
            <td width="14%">Ekspedisi</td>
            <td width="20%">: {{ $header->ekspedisi ?? '' }}</td>
        </tr>
        <tr>
            <td>Shift</td>
            <td>: {{ $header->shift ?? '' }}</td>
            <td>Jam Selesai</td>
            <td>: {{ $header->jam_selesai ?? '' }}</td>
            <td>Tujuan / Asal</td>
            <td>: {{ $header->tujuan_asal ?? '' }}</td>
        </tr>
        <tr>
            <td>No. Pol Mobil</td>
            <td>: {{ $header->no_pol_mobil ?? '' }}</td>
            <td>Nama Sopir</td>
            <td>: {{ $header->nama_supir ?? '' }}</td>
            <td>Jenis Aktivitas</td>
            <td>: {{ $header->jenis_aktivitas ?? '' }}</td>
        </tr>
        <tr>
            <td>Jenis Kendaraan</td>
            <td>: {{ $header->jenis_kendaraan ?? '' }}</td>
            <td>No. Segel</td>
            <td>: {{ $header->no_segel ?? '' }}</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>Kondisi Mobil</td>
            <td colspan="5">: {{ $kondisiMobil }}</td>
        </tr>
    </table>

    <br>

    <table width="100%" class="tbl-main" cellpadding="0" cellspacing="0">
        <tr>
            <th width="4%"><b>No</b></th>
            <th width="26%"><b>Nama Varian</b></th>
            <th width="18%"><b>Kode Batch</b></th>
            <th width="18%"><b>Kode Expired</b></th>
            <th width="10%"><b>Jumlah</b></th>
            <th width="24%"><b>Keterangan</b></th>
        </tr>

        @forelse($pageDetails as $detailIndex => $detail)
            @php
                $kodeProduksi = $kodeMap[$detail->kode_produksi] ?? $detail->kode_produksi ?? '';
            @endphp
            <tr>
                <td width="4%"  align="center">{{ ($pageIndex * 15) + $loop->iteration }}</td>
                <td width="26%" align="center">{{ $detail->nama_produk ?? '' }}</td>
                <td width="18%" align="center">{{ $kodeProduksi }}</td>
                <td width="18%" align="center">
                    {{ $detail->kode_expired ? \Carbon\Carbon::parse($detail->kode_expired)->format('d-m-Y') : '' }}
                </td>
                <td width="10%" align="center">{{ $detail->jumlah ?? '' }} {{ $detail->satuan ?? '' }}</td>
                <td width="24%" align="center">{{ $detail->keterangan ?? '' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" align="center">Tidak ada data</td>
            </tr>
        @endforelse
    </table>

    <div style="margin-top:5px; text-align:right;">
        <i>{{ $noDokumen ?? '-' }}</i>
    </div>

    @if($loop->last)

        <br>

        <table width="100%" class="note" cellpadding="0" cellspacing="0" nobr="true">
            <tr>
                <td colspan="4"><strong>Keterangan :</strong></td>
            </tr>
            <tr>
                <td width="15%">V OK<br>X Tidak</td>
                <td width="15%">1 Bersih<br>2 Bocor<br>3 Debu</td>
                <td width="15%">4 Kering<br>5 Basah<br>6 Hama</td>
                <td width="55%">7 Noda (Karat, cat, tinta)<br>8 Bekas oli di lantai, di dinding<br>9 Tidak ada varian non halal</td>
            </tr>
        </table>

        <br>

        <table width="100%" class="note" cellpadding="0" cellspacing="0" nobr="true">
            <tr>
                <td width="33%" align="center" style="vertical-align: top;">
                    Diperiksa oleh :<br>
                    @if($namaPemeriksa)
                        <br>
                        <img src="data:image/png;base64,{{ $qrPemeriksa }}" width="60" height="60"><br>
                        <u>{{ $namaPemeriksa }}</u><br>
                        QC
                    @endif
                </td>

                <td width="34%" align="center" style="vertical-align: top;">
                    Diketahui oleh :<br>
                    @if($namaWarehouse)
                        <br>
                        <img src="data:image/png;base64,{{ $qrWarehouse }}" width="60" height="60"><br>
                        <u>{{ $namaWarehouse }}</u><br>
                        Warehouse
                    @endif
                </td>

                <td width="33%" align="center" style="vertical-align: top;">
                    Disetujui oleh :<br>
                    @if($namaSpv)
                        <br>
                        <img src="data:image/png;base64,{{ $qrSpv }}" width="60" height="60"><br>
                        <u>{{ $namaSpv }}</u><br>
                        QC SPV
                    @elseif($loadings->count() > 0)
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