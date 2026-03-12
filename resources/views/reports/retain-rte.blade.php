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
        'nama_produk' => '-',
        'kode_produksi' => '-',
        'analisa' => '[]',
        'username_updated' => '',
        'username' => '',
        'nama_spv' => ''
    ];
@endphp

    {{-- HEADER --}}
    <table width="100%" style="border: none; border-collapse: collapse;">
    <tr>
        <td style="border: none;">
            PT Charoen Pokphand Indonesia<br>
            Food Division
        </td>
    </tr>
</table>


    <h3 class="center">PEMERIKSAAN SAMPEL RETAIN RTE</h3>

    {{-- INFO --}}
    <table width="100%" class="tbl-header" style="border: none; border-collapse: collapse;">
        <tr>
            <td width="34.5%" style="border: none;">
                Nama Varian : {{ $produk->nama_produk }}
            </td>
            <td width="50%" style="border: none;">
                Kode Batch : {{ $produk->kode_produksi }}
            </td>
        </tr>
    </table>

    <br>
    <br>

    {{-- TABLE UTAMA RETAIN RTE--}}
    @php
        $analisa = json_decode($produk->analisa ?? '[]', true);
        $labels = [
            'Fisik/ Tekstur' => 'fisik',
            'Aroma' => 'aroma',
            'Rasa' => 'rasa',
            'Score' => 'rata_score',
            'Cemaran' => 'cemaran'
        ];
    @endphp

    <table width="100%" border="1" cellspacing="0" cellpadding="5" style="border-collapse: collapse; font-size: 10px;">
        <thead>
            <tr>
                <th rowspan="2" style="border:1px solid black; text-align:center; vertical-align: middle;">No</th>
                <th rowspan="2" style="border:1px solid black; text-align:center; vertical-align: middle;">Hasil Analisa</th>
                <th colspan="12" style="border:1px solid black; text-align:center;">Bulan</th>
            </tr>
            <tr>
                @for ($col = 1; $col <= 12; $col++)
                    <th style="border:1px solid black; text-align:center;">{{ $col }}</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @foreach($labels as $index => $key)
                <tr>
                    <td style="border:1px solid black; text-align:center; vertical-align: middle;">{{ $loop->iteration }}</td>
                    <td style="border:1px solid black; vertical-align: middle;">{{ $index }}</td>
                    @for ($col = 0; $col < 12; $col++)
                        @php
                            $val = $analisa[$col][$key] ?? '';
                        @endphp
                        <td style="border:1px solid black; text-align:center;">{{ $val }}</td>
                    @endfor
                </tr>
            @endforeach
        </tbody>
    </table>





    <div style="text-align:right; font-size:10px;">QT 43 / 00</div>

   
    <br>

    {{-- CATATAN --}}
    <table width="60%" class="small" style="font-size:10px; margin-top:5px; border:none; border-collapse:collapse;">
        <tr>
            <td width="33%" valign="top" style="border:none; padding-right:10px;">
                <strong>Keterangan Score Orlep:</strong><br>
                1 Sangat Tidak<br>
                2 Biasa<br>
                3 Sangat
            </td>

        <td width="33%" valign="top" style="border:none; text-align:right; padding-right:10px;">
                <strong>Keterangan:</strong><br>
                1,5<br>
                1,6
            </td>

        <td width="33%" valign="top" style="border:none; text-align:left; padding-left:10px;">
                <strong>Hasil Score:</strong><br>
                Tidak Lolos<br>
                Lolos
            </td>
        </tr>
    </table>



    <br><br><br><br><br><br><br><br><br><br>

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
