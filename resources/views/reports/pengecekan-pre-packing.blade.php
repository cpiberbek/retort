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
            font-size: 7px;
            text-align: center;
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
            font-size: 7pt;
        }
    </style>
</head>

<body>

@php
    $pages = $prepackings->chunk(6);

    if ($pages->isEmpty()) {
        $pages = collect([collect()]);
    }
@endphp

@foreach($pages as $pageIndex => $page)

    @php
        $prepackings = $page;
        $isFirstPage = $pageIndex === 0;
        $isLastPage = $pageIndex === $pages->count() - 1;
    @endphp

    @if(!$isFirstPage)
        <div style="page-break-before: always;"></div>
    @endif

    @if($isFirstPage)

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

        <table width="100%" border="0" cellpadding="3" cellspacing="0">
            <tr>
                <td width="18%"></td>
                <td width="64%" align="center" style="font-size:12pt;">
                    <b>PEMERIKSAAN PRE PACKING</b>
                </td>
                <td width="18%"></td>
            </tr>
        </table>

        <br>
        <br>

        @php
            $dateFilter = request('date')
                ? \Carbon\Carbon::parse(request('date'))->format('d-m-Y')
                : 'Semua Tanggal';
        @endphp

        <table width="100%" class="tbl-header" style="font-size:11pt !important;">
            <tr>
                <td width="15%">
                    Hari / Tanggal: {{ $dateFilter }}
                </td>
                <td width="85%"></td>
            </tr>
        </table>

    @endif

    <table width="100%" class="tbl-main small" cellpadding="3" align="center">

        <tr style="font-weight: bold;">
            <th rowspan="2" style="width:3%;">No</th>
            <th colspan="2" style="width:20%;">Varian</th>
            <th rowspan="2" style="width:7%;">
                No.<br>Conveyor
            </th>
            <th rowspan="2" style="width:8%;">
                Suhu<br>Varian
            </th>
            <th rowspan="2" style="width:10%;">
                Bagian Badan Sosis
            </th>
            <th colspan="2" style="width:12%;">Air (%)</th>
            <th colspan="2" style="width:12%;">Minyak (%)</th>
            <th colspan="2" style="width:18%;">
                Berat Varian per
            </th>
            <th rowspan="2" style="width:10%;">
                PARAF<br>QC
            </th>
        </tr>

        <tr style="font-weight: bold;">
            <th class="center">Nama</th>
            <th class="center">Kode</th>
            <th class="center">Basah</th>
            <th class="center">Kering</th>
            <th class="center">Basah</th>
            <th class="center">Kering</th>
            <th class="center">pcs</th>
            <th class="center">
                Toples<br>(berat kotor)
            </th>
        </tr>

        @forelse($prepackings as $index => $prepacking)

            @php
                $number = ($pageIndex * 6) + $index + 1;

                $kondisi = json_decode($prepacking->kondisi_produk, true) ?? [];

                $airBasahUjung = $kondisi['basah_air_ujung'] ?? 0;
                $airKeringUjung = $kondisi['kering_air_ujung'] ?? 0;
                $minyakBasahUjung = $kondisi['basah_minyak_ujung'] ?? 0;
                $minyakKeringUjung = $kondisi['kering_minyak_ujung'] ?? 0;

                $airBasahSeal = $kondisi['basah_air_seal'] ?? 0;
                $airKeringSeal = $kondisi['kering_air_seal'] ?? 0;
                $minyakBasahSeal = $kondisi['basah_minyak_seal'] ?? 0;
                $minyakKeringSeal = $kondisi['kering_minyak_seal'] ?? 0;

                $berat = json_decode($prepacking->berat_produk, true) ?? [];

                $pcsUjung = implode(' | ', [
                    $berat['pcs_1'] ?? 0,
                    $berat['pcs_2'] ?? 0,
                    $berat['pcs_3'] ?? 0,
                ]);

                $toplesUjung = implode(' | ', [
                    $berat['toples_1'] ?? 0,
                    $berat['toples_2'] ?? 0,
                    $berat['toples_3'] ?? 0,
                ]);
            @endphp

            <tr>
                <td rowspan="3" class="center">
                    {{ $number }}
                </td>

                <td rowspan="3" class="center">
                    {{ $prepacking->nama_produk ?? '-' }}
                </td>

                <td rowspan="3" class="center">
                    {{ \App\Models\Mincing::where('uuid', $prepacking->kode_produksi)->value('kode_produksi') ?? '-' }}
                </td>

                <td rowspan="3" class="center">
                    {{ $prepacking->conveyor ?? '-' }}
                </td>

                <td rowspan="3" class="center">
                    {{ $prepacking->suhu_produk
                        ? implode(' | ', json_decode($prepacking->suhu_produk, true))
                        : '-'
                    }}
                </td>

                <td>Ujung</td>

                <td class="center">
                    {{ $airBasahUjung }}
                </td>

                <td class="center">
                    {{ $airKeringUjung }}
                </td>

                <td class="center">
                    {{ $minyakBasahUjung }}
                </td>

                <td class="center">
                    {{ $minyakKeringUjung }}
                </td>

                <td class="center">
                    {{ $pcsUjung }}
                </td>

                <td class="center">
                    {{ $toplesUjung }}
                </td>

                <td rowspan="3" class="center">
                    {{ $prepacking->username ?? '-' }}
                </td>
            </tr>

            <tr>
                <td>Seal</td>

                <td class="center">
                    {{ $airBasahSeal }}
                </td>

                <td class="center">
                    {{ $airKeringSeal }}
                </td>

                <td class="center">
                    {{ $minyakBasahSeal }}
                </td>

                <td class="center">
                    {{ $minyakKeringSeal }}
                </td>

                <td class="center">-</td>

                <td class="center">-</td>
            </tr>

            <tr>
                <td>Total</td>

                <td class="center">
                    {{ $airBasahUjung + $airBasahSeal }}
                </td>

                <td class="center">
                    {{ $airKeringUjung + $airKeringSeal }}
                </td>

                <td class="center">
                    {{ $minyakBasahUjung + $minyakBasahSeal }}
                </td>

                <td class="center">
                    {{ $minyakKeringUjung + $minyakKeringSeal }}
                </td>

                <td class="center">-</td>

                <td class="center">-</td>
            </tr>

        @empty

        @endforelse

        @if($pages->count() === 1 && count($prepackings) < 6)

            @for($i = count($prepackings) + 1; $i <= 6; $i++)

                <tr>
                    <td rowspan="3" class="center">
                        {{ $i }}
                    </td>

                    <td rowspan="3"></td>
                    <td rowspan="3"></td>
                    <td rowspan="3"></td>
                    <td rowspan="3"></td>

                    <td>Ujung</td>

                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>

                    <td rowspan="3"></td>
                </tr>

                <tr>
                    <td>Seal</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>

                <tr>
                    <td>Total</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>

            @endfor

        @endif

    </table>

    @if($isLastPage)

        <table width="100%">
            <tr>
                <td width="75%"></td>

                <td width="25%" align="right" style="font-style: italic;">
                    {{ $noDokumen }}
                </td>
            </tr>
        </table>

        @php
            $catatan = $prepackings
                ->map(function ($item) {
                    $kode = \App\Models\Mincing::where(
                        'uuid',
                        $item->kode_produksi
                    )->value('kode_produksi') ?? '-';

                    return !empty(trim($item->catatan))
                        ? $kode . ': ' . $item->catatan
                        : null;
                })
                ->filter()
                ->implode(', ');

            $catatan = wordwrap($catatan, 75, "\n", true);

            $allApproved = $prepackings->every(
                fn($item) => !empty($item->nama_spv)
            );

            $namaSpv = $allApproved && $prepackings->isNotEmpty()
                ? $prepackings->first()->nama_spv
                : null;
        @endphp

        <table width="100%" class="small">
            <tr>
                <td>
                    <b>CATATAN :</b><br>
                    {!! nl2br(e($catatan ?: '-')) !!}
                </td>
            </tr>
        </table>

        <table width="100%" class="small">
            <tr>
                <td width="70%"></td>

                <td width="30%">
                    <table width="100%" class="sign">
                        <tr>
                            <td>
                                Disetujui Oleh,
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <br><br><br>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                (
                                <u>
                                    @if($namaSpv)
                                        {{ $namaSpv }}
                                    @else
                                        Belum Semua Entry Disetujui Oleh SPV
                                    @endif
                                </u>
                                )
                                <br>
                                QC SPV
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

    @endif

@endforeach

</body>
</html>