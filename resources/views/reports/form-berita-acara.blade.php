<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
    body {
        font-size: 9px;
        font-family: times;
    }

    table {
        border-collapse: collapse;
    }

    .title {
        font-size: 12px;
        font-weight: bold;
        text-align: center;
    }

    .small {
        font-size: 8px;
    }

    .center {
        text-align: center;
    }

    .sign {
        text-align: center;
    }

    .tbl-main {
        border: 0.5px solid #000;
        border-collapse: collapse;
    }

    .tbl-main td {
        border: none;
        padding: 4px;
        vertical-align: top;
    }

    .box {
        border: 0.6px solid #000;
        width: 14px;
        height: 14px;
        display: inline-block;
        margin-right: 6px;
        text-align: center;
        line-height: 14px;
    }

    .line {
        border-bottom: 0.4px solid #000;
        height: 16px;
        margin-bottom: 4px;
    }

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

<div style="margin-left:-30px;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td width="55">
                <img src="{{ public_path('assets/img/Logo CPI.png') }}" width="50">
            </td>
            <td>
                <span style="font-size:12pt;"><b>PT Charoen </b></span><br>
                <span style="font-size:12pt;"><b>Pokphand Indonesia</b></span><br>
                <span style="font-size:12pt;"><b>Food Division</b></span>
            </td>
        </tr>
    </table>
</div>

<table width="100%" border="0" cellpadding="3" cellspacing="0">
    <tr>
        <td width="18%"></td>
        <td width="64%" align="center" style="font-size:12pt;"><b>FORM BERITA ACARA</b></td>
        <td width="18%"></td>
    </tr>
</table>

<table width="100%" class="tbl-main small">
<br>

@php
    $firstBeritaAcara = $beritaAcaras->first();
    $nomor = $firstBeritaAcara ? $firstBeritaAcara->nomor : '';
    $namaBarang = $firstBeritaAcara ? $firstBeritaAcara->nama_barang : '';
    $jumlahBarang = $firstBeritaAcara ? $firstBeritaAcara->jumlah_barang : '';
    $supplier = $firstBeritaAcara ? $firstBeritaAcara->supplier : '';
    $uraianMasalah = $firstBeritaAcara ? $firstBeritaAcara->uraian_masalah : '';
    $noSuratJalan = $firstBeritaAcara ? $firstBeritaAcara->no_surat_jalan : '';
    $doPo = $firstBeritaAcara ? $firstBeritaAcara->dd_po : '';
    $tanggalKedatangan = $firstBeritaAcara ? \Carbon\Carbon::parse($firstBeritaAcara->tanggal_kedatangan)->format('d-m-Y') : '';
@endphp

<table width="100%" class="small" cellpadding="4" cellspacing="0" border="0">
    <tr>
        <td width="50%" style="border:none;">
            Nomor : {{ $nomor }}<br>
            Nama Barang : {{ $namaBarang }}<br>
            Jumlah Barang : {{ $jumlahBarang }}<br>
            Supplier : {{ $supplier }}<br>
            Uraian Masalah : {{ $uraianMasalah }}
        </td>
        <td width="50%" style="border:none;">
            No Surat Jalan : {{ $noSuratJalan }}<br>
            DO / PO : {{ $doPo }}<br>
            Tanggal Kedatangan : {{ $tanggalKedatangan }}
        </td>
    </tr>
</table>

@php
    $pengembalian = $firstBeritaAcara && $firstBeritaAcara->keputusan_pengembalian ? 'V' : '';
    $potonganHarga = $firstBeritaAcara && $firstBeritaAcara->keputusan_potongan_harga ? 'V' : '';
    $sortir = $firstBeritaAcara && $firstBeritaAcara->keputusan_sortir ? 'V' : '';
    $penukaranBarang = $firstBeritaAcara && $firstBeritaAcara->keputusan_penukaran_barang ? 'V' : '';
    $penggantianBiaya = $firstBeritaAcara && $firstBeritaAcara->keputusan_penggantian_biaya ? 'V' : '';
    $keputusanLainLain = $firstBeritaAcara ? $firstBeritaAcara->keputusan_lain_lain : '';
    $analisaPenyebab = $firstBeritaAcara ? $firstBeritaAcara->analisa_penyebab : '';
    $tindakLanjutPerbaikan = $firstBeritaAcara ? $firstBeritaAcara->tindak_lanjut_perbaikan : '';
    $Lampiran = $firstBeritaAcara ? $firstBeritaAcara->lampiran : '';
@endphp
<br>
<table width="100%" class="small" cellpadding="4" cellspacing="0" border="0">
    <tr>
        <td width="50%" valign="top">
            Keputusan :<br>

            <table width="100%" cellpadding="2" cellspacing="0" border="0">
                <tr>
                    <td width="15" style="border:1px solid #000; text-align:center;">{{ $pengembalian }}</td>
                    <td>&nbsp;Pengembalian Barang</td>
                </tr>
                <tr>
                    <td width="15" style="border:1px solid #000; text-align:center;">{{ $potonganHarga }}</td>
                    <td>&nbsp;Potongan Harga</td>
                </tr>
                <tr>
                    <td width="15" style="border:1px solid #000; text-align:center;">{{ $sortir }}</td>
                    <td>&nbsp;Sortir</td>
                </tr>
            </table>
        </td>

        <td width="50%" valign="top">
            <br>

            <table width="100%" cellpadding="2" cellspacing="0" border="0">
                <tr>
                    <td width="15" style="border:1px solid #000; text-align:center;">{{ $penukaranBarang }}</td>
                    <td>&nbsp;Penukaran Barang</td>
                </tr>
                <tr>
                    <td width="15" style="border:1px solid #000; text-align:center;">{{ $penggantianBiaya }}</td>
                    <td>&nbsp;Penggantian Biaya</td>
                </tr>
                <tr>
                    <td width="15" style="border:1px solid #000; text-align:center;">{{ $keputusanLainLain ? 'V' : '' }}</td>
                    <td>&nbsp;Lain-lain: <u>{{ $keputusanLainLain }}</u></td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td colspan="2">
            Tanggal Keputusan :
            {{ $firstBeritaAcara && $firstBeritaAcara->tanggal_keputusan
                ? \Carbon\Carbon::parse($firstBeritaAcara->tanggal_keputusan)->format('d-m-Y')
                : '' }}
        </td>
    </tr>

    <tr>
        <td colspan="2" style="padding-top:20px;">
            @php
                use App\Models\User;

                $creator = $firstBeritaAcara && $firstBeritaAcara->created_by
                    ? User::where('uuid', $firstBeritaAcara->created_by)->value('name')
                    : '';

                $ppicVerifier = $firstBeritaAcara && $firstBeritaAcara->ppic_verified_by
                    ? User::where('uuid', $firstBeritaAcara->ppic_verified_by)->value('name')
                    : '';

                $spvVerifier = $firstBeritaAcara && $firstBeritaAcara->spv_verified_by
                    ? User::where('uuid', $firstBeritaAcara->spv_verified_by)->value('name')
                    : '';
            @endphp
            <table width="100%" cellpadding="2" cellspacing="0" border="0">
                <tr>
                    <td width="33%" align="center">
                        Dibuat Oleh,<br><br><br>
                        ( {{ $creator }} )<br>
                        QC Warehouse
                    </td>

                    <td width="33%" align="center">
                        Mengetahui,<br><br><br>
                        ( {{ $ppicVerifier }} )<br>
                        PPIC
                    </td>

                    <td width="33%" align="center">
                        Disetujui Oleh,<br><br><br>
                        ( {{ $spvVerifier }} )<br>
                        QC Supervisor
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</table>
<br>

<table width="100%" class="tbl-main small">
    <tr>
        <td align="center" style="background-color:#d9d9d9; font-weight:bold;">
            Diisi Oleh Supplier
        </td>
    </tr>

    <tr>
        <td>
            <strong>A. Analisa Penyebab Penyimpangan :</strong> {{ $analisaPenyebab }}
            <br>

            <strong>B. Tindak Lanjut Perbaikan dan Pencegahan :</strong> {{ $tindakLanjutPerbaikan }}
            <br>
        </td>
    </tr>
</table>

<table width="100%">
    <tr>
        <td width="75%"></td>
        <td width="25%" align="right" style="font-style: italic;">
            {{ $noDokumen }}
        </td>
    </tr>
</table>

<div style="page-break-before: always;"></div>

<div style="margin-left:-30px;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td width="55">
                <img src="{{ public_path('assets/img/Logo CPI.png') }}" width="50">
            </td>
            <td>
                <span style="font-size:12pt;"><b>PT Charoen </b></span><br>
                <span style="font-size:12pt;"><b>Pokphand Indonesia</b></span><br>
                <span style="font-size:12pt;"><b>Food Division</b></span>
            </td>
        </tr>
    </table>
</div>

<table width="100%" cellpadding="10" cellspacing="0" border="0" style="height:100%;">
    <tr>
        <td style="border:1px solid #000; height:400px; vertical-align:top; padding:15px;">
            <strong>Lampiran:</strong>
            <br><br>
            {{ $Lampiran ?: '-' }}
        </td>
    </tr>
</table>

<table width="100%">
    <tr>
        <td width="75%"></td>
        <td width="25%" align="right" style="font-style: italic;">
            {{ $noDokumen }}
        </td>
    </tr>
</table>

</body>
</html>