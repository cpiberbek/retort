<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-size: 10px; }
        .title { font-size: 10px; font-weight: bold; text-align: center; }
        table {
            border-collapse: collapse;
            border: 0.3px solid #000;
            width: 100%;
        }
        .tbl-header td {
            padding: 2px;
            font-size: 10px;
            border: 0.3px solid #000;
        }
        .tbl-main, .tbl-main th, .tbl-main td {
            border: 0.3px solid #000;
        }
        .tbl-main th {
            font-size: 10px;
            text-align: center;
            vertical-align: middle;
            padding: 2px;
        }
        .tbl-main td {
            padding: 2px;
            font-size: 10px;
                vertical-align: middle;
        }
        .center { text-align: center; }
        .small { font-size: 7px; }
        .sign { text-align: center; }
    </style>

</head>

<body>

@foreach ($data->count() ? $data : [null] as $produk)
    @php
        $produk = $produk ?? (object) [
            'date' => '-',
            'username_updated' => '-',
            'username' => '-',
            'nama_spv' => '-'
        ];
    @endphp

    <table width="100%" style="border: none; border-collapse: collapse;">
        <tr>
            <td style="border: none;">
                PT Charoen Pokphand Indonesia<br>
                Food Division
            </td>
        </tr>
    </table>

    <h3 class="center">DATA RELEASE PACKING RTE</h3>

    <table width="100%" class="tbl-header" style="border: none; border-collapse: collapse;">
        <tr>
            <td width="100%" style="border: none;">
                Hari/Tanggal : {{ $produk->date }}
            </td>
        </tr>
    </table>

    <br><br>

    <table width="100%" border="1" cellspacing="0" cellpadding="5" style="border-collapse: collapse; font-size: 10px;">
        <thead>
            <tr>
                <th rowspan="2" style="border:1px solid #000; text-align:center; vertical-align: middle;">No</th>
                <th rowspan="2" style="border:1px solid #000; text-align:center; vertical-align: middle;">Nama Varian</th>
                <th rowspan="2" style="border:1px solid #000; text-align:center; vertical-align: middle;">Kode Batch</th>
                <th rowspan="2" style="border:1px solid #000; text-align:center; vertical-align: middle;">Expired</th>
                <th colspan="2" style="border:1px solid #000; text-align:center;">Jumlah</th>
                <th rowspan="2" style="border:1px solid #000; text-align:center; vertical-align: middle;">Keterangan</th>
            </tr>
            <tr>
                <th style="border:1px solid #000; text-align:center;">Reject</th>
                <th style="border:1px solid #000; text-align:center;">Release</th>
            </tr>
        </thead>
        <tbody>
        <tr>
            <td style="border:1px solid #000; text-align:center;">1</td>
            <td style="border:1px solid #000; text-align:center;">{{ $produk->nama_produk ?? '-' }}</td>
            <td style="border:1px solid #000; text-align:center;">{{ $produk->kode_produksi ?? '-' }}</td>
            <td style="border:1px solid #000; text-align:center;">{{ $produk->expired_date ?? '-' }}</td>
            <td style="border:1px solid #000; text-align:center;">{{ $produk->reject ?? '-' }}</td>
            <td style="border:1px solid #000; text-align:center;">{{ $produk->release ?? '-' }}</td>
            <td style="border:1px solid #000; text-align:center;">{{ $produk->keterangan ?? '-' }}</td>
        </tr>
</tbody>

    </table>

    <div style="text-align:right; font-size:10px;">QT 34 / 00</div>

    <br><br>

     <table width="100%" style="border:none; border-collapse:collapse; font-size:10px;">
        <tr>
            <td width="50%" style="border:none; text-align:center;">Diperiksa oleh</td>
            <td width="50%" style="border:none; text-align:center;">Disetujui oleh</td>
        </tr>
        <tr>
            <td style="border:none; text-align:center;"><br><br></td>
            <td style="border:none; text-align:center;"><br><br></td>
        </tr>
        <tr>
            <td style="border:none; text-align:center;">(___________________)</td>
            <td style="border:none; text-align:center;">(___________________)</td>
        </tr>
        <tr>
            <td style="border:none; text-align:center;">{{ $produk->username_updated ?? $produk->username }}</td>
            <td style="border:none; text-align:center;">{{ $produk->nama_spv }}</td>
        </tr>
        <tr>
            <td style="border:none; text-align:center;">QC</td>
            <td style="border:none; text-align:center;">QC SPV</td>
        </tr>
    </table>

    @if (!$loop->last)
        <div style="page-break-after: always;"></div>
    @endif
@endforeach

</body>
</html>
