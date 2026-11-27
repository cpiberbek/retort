<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Form Berita Acara</title>
    <style>
        body {
            font-family: times;
            font-size: 9pt;
        }

        table {
            border-collapse: collapse;
        }

        .title {
            font-size: 12pt;
            font-weight: bold;
            text-align: center;
        }

        .outer {
            border: 0.5px solid #000;
        }

        .outer td {
            vertical-align: top;
        }

        .note td {
            vertical-align: top;
            line-height: 1.4;
        }
    </style>
</head>

<body>

@php
    $makeQr = function ($nama, $tanggal) {
        if (empty($nama) || $nama === '-') {
            return '';
        }
        $teks = 'Ditandatangani secara digital oleh ' . $nama;
        if (!empty($tanggal)) {
            $teks .= ' pada ' . $tanggal;
        }
        $barcode = new \TCPDF2DBarcode($teks, 'QRCODE,H');
        return base64_encode($barcode->getBarcodePngData(4, 4, [0, 0, 0]));
    };

    foreach ($signers as $i => $s) {
        $signers[$i]['qr'] = $makeQr($s['nama'], $s['tanggal']);
    }

    $k = $data['keputusan'];
@endphp

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0;">
    <tr>
        <td style="padding:0; line-height:1;">
            <span style="font-size:12pt;"><b>PT Charoen Pokphand Indonesia</b></span><br>
            <span style="font-size:12pt;"><b>Food Division</b></span>
        </td>
    </tr>
</table>

<h2 class="title" style="margin:0; padding:0;">FORM BERITA ACARA</h2>
<br>

<table width="100%" class="outer" cellpadding="4" cellspacing="0">
    <tr>
        <td width="100%">

            <table width="100%" cellpadding="4" cellspacing="0" border="0">
                <tr>
                    <td width="50%">
                        Nomor : {{ $data['nomor'] }}<br>
                        Nama Barang : {{ $data['namaBarang'] }}<br>
                        Jumlah Barang : {{ $data['jumlahBarang'] }}<br>
                        Supplier : {{ $data['supplier'] }}<br>
                        Uraian Masalah : {{ $data['uraianMasalah'] }}
                    </td>
                    <td width="50%">
                        No Surat Jalan : {{ $data['noSuratJalan'] }}<br>
                        DO / PO : {{ $data['doPo'] }}<br>
                        Tanggal Kedatangan : {{ $data['tanggalKedatangan'] }}
                    </td>
                </tr>
            </table>

            <br>

            <table width="100%" cellpadding="4" cellspacing="0" border="0">
                <tr>
                    <td width="50%" valign="top">
                        Keputusan :<br>

                        <table width="100%" cellpadding="2" cellspacing="0" border="0">
                            <tr>
                                <td width="15" style="border:1px solid #000; text-align:center;">{{ $k['pengembalian'] }}</td>
                                <td>&nbsp;Pengembalian Barang</td>
                            </tr>
                            <tr>
                                <td width="15" style="border:1px solid #000; text-align:center;">{{ $k['potongan'] }}</td>
                                <td>&nbsp;Potongan Harga</td>
                            </tr>
                            <tr>
                                <td width="15" style="border:1px solid #000; text-align:center;">{{ $k['sortir'] }}</td>
                                <td>&nbsp;Sortir</td>
                            </tr>
                        </table>
                    </td>

                    <td width="50%" valign="top">
                        <br>

                        <table width="100%" cellpadding="2" cellspacing="0" border="0">
                            <tr>
                                <td width="15" style="border:1px solid #000; text-align:center;">{{ $k['penukaran'] }}</td>
                                <td>&nbsp;Penukaran Barang</td>
                            </tr>
                            <tr>
                                <td width="15" style="border:1px solid #000; text-align:center;">{{ $k['penggantian'] }}</td>
                                <td>&nbsp;Penggantian Biaya</td>
                            </tr>
                            <tr>
                                <td width="15" style="border:1px solid #000; text-align:center;">{{ $k['lain'] }}</td>
                                <td>&nbsp;Lain-lain: <u>{{ $data['lainLain'] }}</u></td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td width="100%" colspan="2">
                        Tanggal Keputusan : {{ $data['tanggalKeputusan'] }}
                    </td>
                </tr>
            </table>

            <br>

            <table width="100%" class="note" cellpadding="2" cellspacing="0" border="0" nobr="true">
                <tr>
                    @foreach($signers as $s)
                        <td width="33.33%" align="center" style="vertical-align: top;">
                            {{ $s['label'] }},<br>
                            @if(!empty($s['nama']))
                                <br>
                                <img src="data:image/png;base64,{{ $s['qr'] }}" width="60" height="60"><br>
                                <u>{{ $s['nama'] }}</u><br>
                                {{ $s['jabatan'] }}
                            @else
                                <br><br><br><br><br><br>
                                {{ $s['jabatan'] }}
                            @endif
                        </td>
                    @endforeach
                </tr>
            </table>

        </td>
    </tr>
    <tr>
        <td width="100%" align="center" style="background-color:#d9d9d9; font-weight:bold; border-top:0.5px solid #000;">
            Diisi Oleh Supplier
        </td>
    </tr>
    <tr>
        <td width="100%">
            <strong>A. Analisa Penyebab Penyimpangan :</strong> {{ $data['analisa'] }}<br>
            <strong>B. Tindak Lanjut Perbaikan dan Pencegahan :</strong> {{ $data['tindakLanjut'] }}
        </td>
    </tr>
</table>

<div style="margin-top:5px; text-align:right;">
    <i>{{ $noDokumen ?? '-' }}</i>
</div>

<br pagebreak="true" />

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0;">
    <tr>
        <td style="padding:0; line-height:1;">
            <span style="font-size:12pt;"><b>PT Charoen Pokphand Indonesia</b></span><br>
            <span style="font-size:12pt;"><b>Food Division</b></span>
        </td>
    </tr>
</table>

<br>

<table width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td height="20"></td>
    </tr>
</table>

<table width="100%" class="outer" cellpadding="10" cellspacing="0">
    <tr>
        <td width="100%" style="height:400px; padding:15px;">
            <strong>Lampiran:</strong>
            <br><br>
            {{ $data['lampiran'] ?: '-' }}
        </td>
    </tr>
</table>

<div style="margin-top:5px; text-align:right;">
    <i>{{ $noDokumen ?? '-' }}</i>
</div>

</body>
</html>