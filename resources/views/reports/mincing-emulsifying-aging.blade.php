<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-size: 8px;
            font-family: sans-serif;
        }

        .title {
            font-size: 12px;
            font-weight: bold;
            text-align: center;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        .tbl-header td {
            padding: 2px;
            font-size: 8px;
        }

        .tbl-main,
        .tbl-main th,
        .tbl-main td {
            border: 0.3px solid #000;
        }

        .tbl-main th {
            font-size: 7px;
            text-align: center;
            vertical-align: middle;
            background-color: #f2f2f2;
        }

        .center {
            text-align: center;
        }

        .small {
            font-size: 7px;
        }

        .sign {
            text-align: center;
        }

        .page-break {
            page-break-after: always;
        }

        /* tnr */
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
                    <span style="font-size:14pt;"><b>PT Charoen </b></span><br>
                    <span style="font-size:14pt;"><b>Pokphand Indonesia</b></span><br>
                    <span style="font-size:14pt;"><b>Food Division</b></span>
                </td>
            </tr>
        </table>
    </div>

    <br>

    <div class="title">
        PEMERIKSAAN MINCING - EMULSIFYING - AGING
    </div>

    <br>

    <table class="tbl-header">
        <tr>
            <td width="20%">Hari/Tanggal: {{ \Carbon\Carbon::parse($produks->first()->date)->format('d-m-Y') }}</td>
            <td width="20%">Shift: {{ $produks->first()->shift ?? '-' }}</td>
            <td width="60%">Nama Produk: {{ $produks->first()->nama_produk ?? '-' }}</td>
        </tr>
    </table>


    <br>

    @foreach ($produks as $row)
        @php
            $nonPremix = json_decode($row->non_premix, true) ?? [];
            $premix = json_decode($row->premix, true) ?? [];

            $suhu = json_decode($row->suhu_sebelum_grinding, true) ?? [];
            $suhuText = [];

            foreach ($suhu as $s) {
                $suhuText[] = ($s['daging'] ?? '-') . ': ' . ($s['suhu'] ?? '-') . ' °C';
            }
        @endphp

        <table class="tbl-main">

            <tr>
                <th class="center">Kode Batch</th>
                <td colspan="5" class="center">{{ $row->kode_produksi ?? '-' }}</td>
            </tr>

            <tr>
                <th class="center">Preparation</th>
                <td colspan="5" class="center">
                    {{ $row->waktu_mulai ?? '-' }} s/d {{ $row->waktu_selesai ?? '-' }}
                </td>
            </tr>

            <tr>
                <th colspan="6" class="left">Bahan Baku & Tambahan (Non-Premix)</th>
            </tr>

            <tr>
                <th class="center">Nama Bahan</th>
                <th class="center">Kode</th>
                <th class="center">(°C)</th>
                <th class="center">pH</th>
                <th class="center">Kg</th>
                <th class="center">Sens</th>
            </tr>

            @foreach ($nonPremix as $i => $item)
                <tr>
                    <td class="center">
                        {{ $item['nama_bahan'] ?? '-' }}
                    </td>
                    <td class="center">
                        @if (!empty($item['inspection_uuid']))
                            {{ \App\Models\InspectionProductDetail::where('uuid', $item['inspection_uuid'])->value('kode_batch') ?? '-' }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="center">{{ $item['suhu_bahan'] ?? '-' }}</td>
                    <td class="center">{{ $item['ph_bahan'] ?? '-' }}</td>
                    <td class="center">{{ $item['berat_bahan'] ?? '-' }}</td>
                    <td class="center">{{ $item['sensori'] ?? '-' }}</td>
                </tr>
            @endforeach


            <tr>
                <th colspan="6" class="left">Premix</th>
            </tr>

            <tr>
                <th class="center">Nama Premix</th>
                <th colspan="2" class="center">Kode</th>
                <th colspan="2" class="center">Kg</th>
                <th class="center">Sens</th>
            </tr>

            @foreach ($premix as $i => $item)
                <tr>
                    <td class="center">
                        {{ $item['nama_premix'] ?? '-' }}
                    </td>
                    <td colspan="2" class="center">
                        {{ $item['kode_premix'] ?? '-' }}
                    </td>
                    <td colspan="2" class="center">
                        {{ $item['berat_premix'] ?? '-' }}
                    </td>
                    <td class="center">
                        {{ $item['sensori_premix'] ?? '-' }}
                    </td>
                </tr>
            @endforeach


            <tr>
                <td colspan="2" class="left"><b>Suhu Sebelum Grinding</b></td>
                <td colspan="4" class="center">
                    {{ implode(', ', $suhuText) ?: '-' }}
                </td>
            </tr>

            <tr>
                <td colspan="2" class="left"><b>Waktu Mixing Premix</b></td>
                <td colspan="4" class="center">
                    {{ $row->waktu_mixing_premix_start
                        ? \Carbon\Carbon::parse($row->waktu_mixing_premix_start)->format('H:i')
                        : '-' }}
                    -
                    {{ $row->waktu_mixing_premix_end ? \Carbon\Carbon::parse($row->waktu_mixing_premix_end)->format('H:i') : '-' }}
                    ({{ $row->waktu_mixing_premix ?? 0 }} Menit)
                </td>
            </tr>

            <tr>
                <td colspan="2" class="left"><b>Waktu Bowl Cutter</b></td>
                <td colspan="4" class="center">
                    {{ $row->waktu_bowl_cutter_start ? \Carbon\Carbon::parse($row->waktu_bowl_cutter_start)->format('H:i') : '-' }}
                    -
                    {{ $row->waktu_bowl_cutter_end ? \Carbon\Carbon::parse($row->waktu_bowl_cutter_end)->format('H:i') : '-' }}
                    ({{ $row->waktu_bowl_cutter ?? 0 }} Menit)
                </td>
            </tr>

            <tr>
                <td colspan="2" class="left"><b>Waktu Aging Emulsi</b></td>
                <td colspan="4" class="center">

                    @php
                        $awalAging = $row->waktu_aging_emulsi_awal;
                        $akhirAging = $row->waktu_aging_emulsi_akhir;

                        $agingMenit = 0;

                        if ($awalAging && $akhirAging) {
                            $agingMenit = \Carbon\Carbon::parse($awalAging)->diffInMinutes(
                                \Carbon\Carbon::parse($akhirAging),
                            );
                        }
                    @endphp

                    {{ $awalAging ? \Carbon\Carbon::parse($awalAging)->format('H:i') : '-' }}
                    -
                    {{ $akhirAging ? \Carbon\Carbon::parse($akhirAging)->format('H:i') : '-' }}
                    ({{ $agingMenit }} Menit)

                </td>
            </tr>

            <tr>
                <td colspan="2" class="left"><b>Suhu Akhir Emulsi Gel</b></td>
                <td colspan="4" class="center">
                    {{ $row->suhu_akhir_emulsi_gel ?? '-' }} °C
                </td>
            </tr>

            <tr>
                <td colspan="2" class="left"><b>Waktu Mixing</b></td>
                <td colspan="4" class="center">
                    {{ $row->waktu_mixing_start ? \Carbon\Carbon::parse($row->waktu_mixing_start)->format('H:i') : '-' }}
                    -
                    {{ $row->waktu_mixing_end ? \Carbon\Carbon::parse($row->waktu_mixing_end)->format('H:i') : '-' }}
                    ({{ $row->waktu_mixing ?? 0 }} Menit)
                </td>
            </tr>

            <tr>
                <td colspan="2" class="left"><b>Suhu Akhir Mixing</b></td>
                <td colspan="4" class="center">
                    {{ $row->suhu_akhir_mixing ?? '-' }} °C
                </td>
            </tr>

            <tr>
                <td colspan="2" class="left"><b>Suhu Akhir Emulsifying</b></td>
                <td colspan="4" class="center">
                    {{ $row->suhu_akhir_emulsi ?? '-' }} °C
                </td>
            </tr>

            <tr>
                <td colspan="2" class="left"><b>PARAF QC</b></td>
                <td colspan="4" class="center">
                    {{ $row->username_updated ?? ($row->username ?? '-') }}
                </td>
            </tr>

            <tr>
                <td colspan="2" class="left"><b>PARAF Produksi</b></td>
                <td colspan="4" class="center">
                    {{ $row->nama_spv ?? '-' }}
                </td>
            </tr>

        </table>
        <table class="tbl-header">
            <tr>
                <td style="text-align:right;">
                    <i>{{ $noDokumen ?? '-' }}</i>
                </td>
            </tr>
        </table>


        <table class="tbl-header">
            <tr>
                <td style="text-align:left;">
                    Catatan<br>
                    *pengukuran pH khusus untuk air produksi<br><br>
                    {{ $row->catatan ?? ' ' }}
                </td>
            </tr>
        </table>


        <br>
    @endforeach

    <br>


</body>

</html>
