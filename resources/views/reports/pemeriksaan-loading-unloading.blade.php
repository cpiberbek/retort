<!DOCTYPE html>

<html>

<head>
    <meta charset="utf-8">

    <style>
        body {
            font-size: 8px;
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
            font-size: 7px;
        }

        .center {
            text-align: center;
        }

        .sign {
            text-align: center;
        }

        .tbl-main,
        .tbl-main th,
        .tbl-main td {
            border: 0.3px solid #000;
        }

        .tbl-main th {
            text-align: center;
            vertical-align: middle;
            font-size: 8px;
        }

        .tbl-header td {
            padding: 2px;
            font-size: 8px;
        }

        body,
        table,
        tr,
        td,
        th {
            font-family: times;
            font-size: 9pt;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>

@php
    $firstLoading = $loadings->first();

    $date = $firstLoading
        ? \Carbon\Carbon::parse($firstLoading->tanggal)->format('d-m-Y')
        : '';

    $shift = $firstLoading ? $firstLoading->shift : '';
    $jamMulai = $firstLoading ? $firstLoading->jam_mulai : '';
    $jamSelesai = $firstLoading ? $firstLoading->jam_selesai : '';
    $noPol = $firstLoading ? $firstLoading->no_pol_mobil : '';
    $namaSupir = $firstLoading ? $firstLoading->nama_supir : '';
    $jenisKendaraan = $firstLoading ? $firstLoading->jenis_kendaraan : '';
    $ekspedisi = $firstLoading ? $firstLoading->ekspedisi : '';
    $tujuanAsal = $firstLoading ? $firstLoading->tujuan_asal : '';
    $noSegel = $firstLoading ? $firstLoading->no_segel : '';
    $kondisi_mobil = $firstLoading ? $firstLoading->kondisi_mobil : '';

    $allDetails = [];

    foreach ($loadings as $loading) {
        if ($loading->details && $loading->details->count() > 0) {
            foreach ($loading->details as $detail) {
                $allDetails[] = $detail;
            }
        }
    }

    $pages = array_chunk($allDetails, 18);
@endphp

@foreach($pages as $pageIndex => $pageDetails)

    {{-- HEADER --}}
    <div style="margin-left:-30px;">
        <table width="100%" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td width="55">
                    <img src="{{ public_path('assets/img/Logo CPI.png') }}" width="50">
                </td>

                <td>
                    <span style="font-size:12pt;">
                        <b>PT Charoen</b>
                    </span>
                    <br>

                    <span style="font-size:12pt;">
                        <b>Pokphand Indonesia</b>
                    </span>
                    <br>

                    <span style="font-size:12pt;">
                        <b>Food Division</b>
                    </span>
                </td>
            </tr>
        </table>
    </div>

    <h2 class="title">
        PEMERIKSAAN LOADING – UNLOADING PRODUK
    </h2>

    <br>
    <br>

    {{-- INFO --}}
    <table width="100%" class="tbl-header">
        <tr>
            <td width="15%">Hari / Tanggal</td>
            <td width="18%">: {{ $date }}</td>

            <td width="15%">Jam Mulai</td>
            <td width="18%">: {{ $jamMulai }}</td>

            <td width="15%">Ekspedisi</td>
            <td width="19%">: {{ $ekspedisi }}</td>
        </tr>

        <tr>
            <td>Shift</td>
            <td>: {{ $shift }}</td>

            <td>Jam Selesai</td>
            <td>: {{ $jamSelesai }}</td>

            <td>Tujuan / Asal</td>
            <td>: {{ $tujuanAsal }}</td>
        </tr>

        <tr>
            <td>No. Pol Mobil</td>
            <td>: {{ $noPol }}</td>

            <td>Nama Sopir</td>
            <td>: {{ $namaSupir }}</td>

            <td>No. Segel</td>
            <td>: {{ $noSegel }}</td>
        </tr>

        <tr>
            <td>Jenis Kendaraan</td>
            <td colspan="5">: {{ $jenisKendaraan }}</td>
        </tr>

        <tr>
            <td>Kondisi Mobil</td>
            <td colspan="5">
                :
                {{ collect($kondisi_mobil ?? [])
                    ->map(fn($item) => ucwords(str_replace('_', ' ', $item)))
                    ->implode(', ') }}
            </td>
        </tr>
    </table>

    <br>

    {{-- TABEL UTAMA --}}
    <table width="100%" class="tbl-main small">
        <tr>
            <th width="4%" class="center">No</th>
            <th width="26%" class="center">Nama Varian</th>
            <th width="18%" class="center">Kode Batch</th>
            <th width="18%" class="center">Kode Expired</th>
            <th width="10%" class="center">Jumlah</th>
            <th width="24%" class="center">Keterangan</th>
        </tr>

        @foreach($pageDetails as $detailIndex => $detail)

            @php
                $globalIndex = ($pageIndex * 18) + $detailIndex + 1;

                $kodeProduksi = $detail->kode_produksi ?? '';

                if (preg_match('/^[0-9a-f-]{36}$/i', $kodeProduksi)) {
                    $kodeProduksi = \App\Models\Mincing::where('uuid', $kodeProduksi)
                        ->value('kode_produksi') ?? $kodeProduksi;
                }
            @endphp

            <tr>
                <td class="center">
                    {{ $globalIndex }}
                </td>

                <td>
                    {{ $detail->nama_produk ?? '' }}
                </td>

                <td>
                    {{ $kodeProduksi }}
                </td>

                <td>
                    {{ $detail->kode_expired
                        ? \Carbon\Carbon::parse($detail->kode_expired)->format('d-m-Y')
                        : '' }}
                </td>

                <td class="center">
                    {{ $detail->jumlah ?? '' }} {{ $detail->satuan ?? '' }}
                </td>

                <td>
                    {{ $detail->keterangan ?? '' }}
                </td>
            </tr>

        @endforeach
    </table>

    <table width="100%">
        <tr>
            <td width="75%"></td>

            <td width="25%" align="right" style="font-style: italic;">
                {{ $noDokumen }}
            </td>
        </tr>
    </table>

    <br>

    {{-- KETERANGAN --}}
    <table width="100%" class="small">
        <tr>
            <td colspan="4">
                Keterangan :<br>
            </td>
        </tr>

        <tr>
            <td>
                V OK<br>
                X Tidak
            </td>

            <td>
                1 Bersih<br>
                2 Bocor<br>
                3 Debu
            </td>

            <td>
                4 Kering<br>
                5 Basah<br>
                6 Hama
            </td>

            <td>
                7 Noda (Karat, cat, tinta)<br>
                8 Bekas oli di lantai, di dinding<br>
                9 Tidak ada varian non halal
            </td>
        </tr>
    </table>

    <br>
    <br>
    <br>

    {{-- TTD --}}
    <table width="100%" class="small">
        <tr>
            <td width="33%" class="sign">
                Diperiksa oleh,
                <br>
                <br>
                <br>

                (
                <u>{{ $header->pic_qc ?? '' }}</u>
                )

                <br>
                QC
            </td>

            <td width="33%" class="sign">
                Diketahui oleh,
                <br>
                <br>
                <br>

                (
                <u>{{ $header->pic_warehouse ?? '' }}</u>
                )

                <br>
                Warehouse
            </td>

            <td width="33%" class="sign">
                Disetujui oleh,
                <br>
                <br>
                <br>

                (
                <u>{{ $header->pic_qc_spv ?? '' }}</u>
                )

                <br>
                QC SPV
            </td>
        </tr>
    </table>

    @if($pageIndex < count($pages) - 1)
        <div class="page-break"></div>
    @endif

@endforeach

</body>

</html>