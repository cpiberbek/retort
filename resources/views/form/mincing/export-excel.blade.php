<!DOCTYPE html>
<html xmlns:x="urn:schemas-microsoft-com:office:excel">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #000000;
            padding: 8px;
            vertical-align: middle;
            font-family: 'Arial', sans-serif;
            font-size: 11px;
        }
        /* Styling Header Form */
        .title {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            border: none;
            background-color: #ffffff;
        }
        .subtitle {
            font-size: 12px;
            text-align: center;
            border: none;
            background-color: #ffffff;
        }
        /* Styling Header Kolom */
        .table-header {
            background-color: #4F81BD;
            color: #ffffff;
            font-weight: bold;
            text-align: center;
        }
        .table-header-2 {
            background-color: #DCE6F1;
            color: #000000;
            font-weight: bold;
            text-align: center;
        }
        /* Trik agar Excel membaca data MURNI sebagai TEXT, mencegah error #NAME? dan ####### */
        .text-mode {
            mso-number-format: "\@";
        }
        .center {
            text-align: center;
        }
    </style>
</head>
<body>
    <table>
        {{-- HEADER LAPORAN --}}
        <tr>
            <td colspan="23" class="title">FORM PEMERIKSAAN MINCING - EMULSIFYING - AGING</td>
        </tr>
        <tr>
            <td colspan="23" class="subtitle">Periode: {{ $periode }}</td>
        </tr>
        <tr>
            <td colspan="23" style="border: none;"></td> {{-- Spasi kosong --}}
        </tr>

        {{-- HEADER TABEL --}}
        <tr>
            <th class="table-header" rowspan="2">No</th>
            <th class="table-header" rowspan="2">Tanggal</th>
            <th class="table-header" rowspan="2">Shift</th>
            <th class="table-header" rowspan="2">Nama Varian</th>
            <th class="table-header" rowspan="2">Kode Batch</th>
            <th class="table-header" colspan="2">Preparation</th>
            <th class="table-header" rowspan="2">Bahan Baku (Non-Premix)</th>
            <th class="table-header" rowspan="2">Premix</th>
            <th class="table-header" rowspan="2">Suhu (Sebelum Grinding)</th>
            <th class="table-header" rowspan="2">Waktu Mixing Premix<br style="mso-data-placement:same-cell;" />(Menit)</th>
            <th class="table-header" rowspan="2">Waktu Bowl Cutter<br style="mso-data-placement:same-cell;" />(Menit)</th>
            <th class="table-header" colspan="2">Waktu Aging Emulsi</th>
            <th class="table-header" rowspan="2">Suhu Akhir Emulsi Gel</th>
            <th class="table-header" rowspan="2">Waktu Mixing<br style="mso-data-placement:same-cell;" />(Menit)</th>
            <th class="table-header" rowspan="2">Suhu Akhir Mixing</th>
            <th class="table-header" rowspan="2">Suhu Akhir Emulsifying</th>
            <th class="table-header" rowspan="2">QC (Pembuat)</th>
            <th class="table-header" rowspan="2">Status QC</th>
            <th class="table-header" rowspan="2">SPV (Pemeriksa)</th>
            <th class="table-header" rowspan="2">Status SPV</th>
            <th class="table-header" rowspan="2">Catatan</th>
        </tr>
        <tr>
            <th class="table-header-2">Mulai</th>
            <th class="table-header-2">Selesai</th>
            <th class="table-header-2">Awal</th>
            <th class="table-header-2">Akhir</th>
        </tr>

        {{-- LOOPING DATA --}}
        @forelse($data as $row)
            @php
                // --- 1. HANDLING DATA NON-PREMIX ---
                $nonPremix = is_string($row->non_premix) ? json_decode($row->non_premix, true) : ($row->non_premix ?? []);
                $np_arr = [];
                if (is_array($nonPremix)) {
                    foreach ($nonPremix as $np) {
                        $nama  = $np['nama_bahan'] ?? '-';
                        $berat = $np['berat_bahan'] ?? '-';
                        // mso-data-placement:same-cell; membuat enter di dalam 1 kotak cell Excel
                        $np_arr[] = "• " . $nama . " (" . $berat . " Kg)";
                    }
                }
                $np_str = implode("<br style='mso-data-placement:same-cell;' />", $np_arr) ?: '-';

                // --- 2. HANDLING DATA PREMIX ---
                $premix = is_string($row->premix) ? json_decode($row->premix, true) : ($row->premix ?? []);
                $px_arr = [];
                if (is_array($premix)) {
                    foreach ($premix as $px) {
                        $nama  = $px['nama_premix'] ?? '-';
                        $berat = $px['berat_premix'] ?? '-';
                        $px_arr[] = "• " . $nama . " (" . $berat . " Kg)";
                    }
                }
                $px_str = implode("<br style='mso-data-placement:same-cell;' />", $px_arr) ?: '-';

                // --- 3. HANDLING DATA SUHU GRINDING ---
                $suhu = is_string($row->suhu_sebelum_grinding) ? json_decode($row->suhu_sebelum_grinding, true) : ($row->suhu_sebelum_grinding ?? []);
                $sh_arr = [];
                if (is_array($suhu) && count($suhu) > 0) {
                    foreach ($suhu as $sh) {
                        $daging = $sh['daging'] ?? '?';
                        $derajat = $sh['suhu'] ?? '-';
                        $sh_arr[] = "• " . $daging . ": " . $derajat . "°C";
                    }
                } elseif (!empty($row->daging)) {
                    $sh_arr[] = "• " . $row->daging . ": " . $row->suhu_sebelum_grinding . "°C";
                }
                $sh_str = implode("<br style='mso-data-placement:same-cell;' />", $sh_arr) ?: '-';

                // --- 4. STATUS PENAMAAN ---
                $status_qc = $row->status_produksi == 1 ? 'Checked' : ($row->status_produksi == 2 ? 'Recheck' : 'Created');
                $status_spv = $row->status_spv == 1 ? 'Verified' : ($row->status_spv == 2 ? 'Revision' : 'Created');
                
                // --- 5. CLEANER (Mencegah Excel membaca awalan "-" atau "=" sebagai rumus) ---
                $clean = function($str) {
                    if (empty($str)) return '-';
                    // Menambahkan spasi tak terlihat di depan jika diawali tanda minus
                    if (str_starts_with(trim($str), '-') || str_starts_with(trim($str), '=')) {
                        return ' ' . $str;
                    }
                    return $str;
                };
            @endphp

            <tr>
                <td class="center">{{ $loop->iteration }}</td>
                <td class="center text-mode">{{ \Carbon\Carbon::parse($row->date)->format('d-m-Y') }}</td>
                <td class="center text-mode">{{ $row->shift }}</td>
                <td class="text-mode">{{ $row->nama_produk }}</td>
                <td class="center text-mode">{{ $row->kode_produksi ?? '-' }}</td>
                <td class="center text-mode">{{ $clean($row->waktu_mulai) }}</td>
                <td class="center text-mode">{{ $clean($row->waktu_selesai) }}</td>
                
                {{-- Data Dinamis dengan Break Line di dalam Cell --}}
                <td class="text-mode">{!! $np_str !!}</td>
                <td class="text-mode">{!! $px_str !!}</td>
                <td class="text-mode">{!! $sh_str !!}</td>
                
                <td class="center text-mode">{{ $clean($row->waktu_mixing_premix ?? '0') }}</td>
                <td class="center text-mode">{{ $clean($row->waktu_bowl_cutter ?? '0') }}</td>
                <td class="center text-mode">{{ $clean($row->waktu_aging_emulsi_awal) }}</td>
                <td class="center text-mode">{{ $clean($row->waktu_aging_emulsi_akhir) }}</td>
                <td class="center text-mode">{{ $clean($row->suhu_akhir_emulsi_gel) }}</td>
                <td class="center text-mode">{{ $clean($row->waktu_mixing ?? '0') }}</td>
                <td class="center text-mode">{{ $clean($row->suhu_akhir_mixing) }}</td>
                <td class="center text-mode">{{ $clean($row->suhu_akhir_emulsi) }}</td>
                
                <td class="center text-mode">{{ $row->username }}</td>
                <td class="center text-mode" style="color: {{ $status_qc == 'Checked' ? 'green' : ($status_qc == 'Recheck' ? 'red' : 'black') }};">
                    {{ $status_qc }}
                </td>
                <td class="center text-mode">{{ $row->nama_spv ?? '-' }}</td>
                <td class="center text-mode" style="color: {{ $status_spv == 'Verified' ? 'green' : ($status_spv == 'Revision' ? 'red' : 'black') }};">
                    {{ $status_spv }}
                </td>
                <td class="text-mode">{{ $clean($row->catatan) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="23" class="center" style="font-style: italic;">Belum ada data mincing pada periode ini.</td>
            </tr>
        @endforelse
    </table>
</body>
</html>