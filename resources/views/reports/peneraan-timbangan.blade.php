<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-size: 9px; }
        table { border-collapse: collapse; }

        .title {
            font-size: 13px;
            font-weight: bold;
            text-align: center;
        }

        .small { font-size: 8px; }
        .center { text-align: center; }
        .sign { text-align: center; }

        .tbl-main, .tbl-main th, .tbl-main td {
            border: 0.4px solid #000;
        }

        .tbl-main th {
            font-size: 8px;
            text-align: center;
            vertical-align: middle;
        }

        .tbl-header td {
            padding: 2px;
            font-size: 9px;
        }
    </style>
</head>

<body>

<table width="100%">
    <tr>
        <td class="small" width="40%">
            PT Charoen Pokphand Indonesia<br>
            Food Division
        </td>
    </tr>
</table>

<h2 class="title">PENERAAN TIMBANGAN</h2>

<br>
<br>

@php
    $firstItem = $items->first();
    $date = $firstItem ? \Carbon\Carbon::parse($firstItem->date)->format('d-m-Y') : '';
    $shift = $firstItem ? $firstItem->shift : '';

    $allPeneraan = [];

    foreach ($items as $item) {
        $peneraan = json_decode($item->peneraan, true);

        if (is_array($peneraan)) {
            foreach ($peneraan as $data) {
                if (
                    !empty($data['kode_timbangan']) ||
                    !empty($data['standar']) ||
                    !empty($data['pukul']) ||
                    !empty($data['hasil_tera']) ||
                    !empty($data['tindakan_perbaikan'])
                ) {
                    $allPeneraan[] = $data;
                }
            }
        }
    }
@endphp

<table width="100%" class="tbl-header">
    <tr>
        <td width="15%">Hari / Tanggal</td>
        <td width="35%">: {{ $date }}</td>
        <td width="10%">Shift</td>
        <td width="40%">: {{ $shift }}</td>
    </tr>
</table>

<br>

<table width="100%" class="tbl-main small">
    <tr>
        <th rowspan="2" class="center">KODE TIMBANGAN</th>
        <th rowspan="2" class="center">STANDAR (gr)</th>
        <th colspan="2" class="center">PENERAAN</th>
        <th rowspan="2" class="center">TINDAKAN PERBAIKAN</th>
    </tr>
    <tr>
        <th class="center">PUKUL</th>
        <th class="center">HASIL TERA</th>
    </tr>

    @foreach($allPeneraan as $peneraan)
        <tr>
            <td style="height:40px;">{{ $peneraan['kode_timbangan'] ?? '' }}</td>
            <td>{{ $peneraan['standar'] ?? '' }}</td>
            <td>{{ $peneraan['pukul'] ?? '' }}</td>
            <td>{{ $peneraan['hasil_tera'] ?? '' }}</td>
            <td>{{ $peneraan['tindakan_perbaikan'] ?? '' }}</td>
        </tr>
    @endforeach
</table>

<div style="text-align:right; font-size:8px;font-style:italic">
    QT 57 / 00
</div>

<br>

<table width="100%" class="small">
    <tr>
        <td>
            <strong>Keterangan :</strong><br>
            - Tera timbangan dilakukan di setiap awal produksi<br>
            - Timbangan ditera menggunakan anak timbangan standar<br>
            - Jika ada selisih angka timbang dengan berat timbangan standar,
              beri keterangan (+) atau (–) angka selisih
        </td>
    </tr>
</table>

<br><br>
<br>

<table width="100%" class="small">
    <tr>
        <td width="50%" class="sign">
            Dibuat oleh,<br><br><br>
            (<u> {{ $firstItem->username ?? '-' }} </u>)<br>
            QC
        </td>

        @php
            $semuaDisetujui = $items->isNotEmpty() &&
                $items->every(fn($item) => (int) $item->status_spv === 1);
        @endphp

        <td width="50%" class="sign">
            Diketahui oleh,<br><br><br>
            (
            <u>{{ $semuaDisetujui ? ($firstItem->nama_spv ?? '-') : 'Belum semua entry disetujui SPV' }}</u>
            )<br>
            QC SPV
        </td>
    </tr>
</table>

</body>
</html>