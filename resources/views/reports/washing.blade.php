<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Washing - Drying</title>
    <style>
        body { font-family: helvetica, sans-serif; font-size: 6px; line-height: 1.1; }
        
        /* Header */
        .company-header { width: 100%; border-bottom: 2px solid #000; margin-bottom: 5px; }
        .company-name { font-size: 9px; font-weight: bold; }
        .report-title { font-size: 11px; font-weight: bold; text-align: center; text-transform: uppercase; }

        /* Tabel Data */
        table.tbl-data { width: 100%; border-collapse: collapse; }
        table.tbl-data th {
            background-color: #e6e6e6; border: 1px solid #000;
            font-weight: bold; text-align: center; vertical-align: middle; padding: 2px 1px;
        }
        table.tbl-data td {
            border: 1px solid #000; vertical-align: middle; padding: 2px 1px; text-align: center;
        }

        /* Utility */
        .text-left { text-align: left !important; padding-left: 2px !important; }
        .bg-ok { color: #006400; font-weight: bold; }
        .bg-rev { color: #8B0000; font-weight: bold; }
        /* tnr */
        body,
        table,
        tr,
        td,
        th {
            font-family: times;
            font-size: 6pt;
        }
    </style>
</head>
<body>

@php
$item = $items->first();
@endphp

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

<table width="100%" border="0" cellpadding="3" cellspacing="0">
    <tr>
        <td width="18%">
        </td>
        <td width="64%" align="center" style="font-size:12pt;"><b>PEMERIKSAAN WASHING - DRYING</b></td>
        <td width="18%"></td>
    </tr>
</table>



<table border="1" cellpadding="3" cellspacing="0" width="100%">

    <tr>
        <td width="28%">Nama Produk</td>
        <td width="72%" align="center">{{ $item->nama_produk ?? '-' }}</td>
    </tr>

    <tr>
    <td>Kode Produksi</td>
        <td align="center">
            {{ \App\Models\Mincing::where('id', $item->id)->value('kode_produksi') ?? '-' }}
        </td>
    </tr>
    <tr>
        <td>Waktu</td>
        <td align="center">{{ $item->pukul ?? '-' }}</td>
    </tr>

    <tr>
        <td>Panjang Produk Akhir</td>
        <td align="center">{{ $item->panjang_produk ?? '-' }}</td>
    </tr>

    <tr>
        <td>Diameter Produk Akhir</td>
        <td align="center">{{ $item->diameter_produk ?? '-' }}</td>
    </tr>

    <tr>
        <td>Airtrap</td>
        <td align="center">{{ $item->airtrap ?? '-' }}</td>
    </tr>

    <tr>
        <td>Lengket</td>
        <td align="center">{{ $item->lengket ?? '-' }}</td>
    </tr>

    <tr>
        <td>Sisa Adonan</td>
        <td align="center">{{ $item->sisa_adonan ?? '-' }}</td>
    </tr>

    <tr>
        <td>Cek Kebocoran/Vacuum</td>
        <td align="center">{{ $item->kebocoran ?? '-' }}</td>
    </tr>

    <tr>
        <td>Kekuatan Seal</td>
        <td align="center">{{ $item->kekuatan_seal ?? '-' }}</td>
    </tr>

    <tr>
        <td>Print Kode Produksi</td>
        <td align="center">{{ $item->print_kode ?? '-' }}</td>
    </tr>

    <tr>
        <td>Konsentrasi PC Kleer 1</td>
        <td align="center">{{ $item->konsentrasi_pckleer ?? '-' }}</td>
    </tr>

    <tr>
        <td>Suhu PC Kleer 1</td>
        <td align="center">{{ $item->suhu_pckleer_1 ?? '-' }}</td>
    </tr>

    <tr>
        <td>Suhu PC Kleer 2</td>
        <td align="center">{{ $item->suhu_pckleer_2 ?? '-' }}</td>
    </tr>

    <tr>
        <td>pH PC Kleer</td>
        <td align="center">{{ $item->ph_pckleer ?? '-' }}</td>
    </tr>

    <tr>
        <td>Kondisi Air PC Kleer</td>
        <td align="center">{{ $item->kondisi_air_pckleer ?? '-' }}</td>
    </tr>

    <tr>
        <td>Konsentrasi Pottasium Sorbate</td>
        <td align="center">{{ $item->konsentrasi_pottasium ?? '-' }}</td>
    </tr>

    <tr>
        <td>Suhu Pottasium Sorbate</td>
        <td align="center">{{ $item->suhu_pottasium ?? '-' }}</td>
    </tr>

    <tr>
        <td>pH Pottasium Sorbate</td>
        <td align="center">{{ $item->ph_pottasium ?? '-' }}</td>
    </tr>

    <tr>
        <td>Kondisi Air Pottasium Sorbate</td>
        <td align="center">{{ $item->kondisi_pottasium ?? '-' }}</td>
    </tr>

    <tr>
        <td>Suhu Heater</td>
        <td align="center">{{ $item->suhu_heater ?? '-' }}</td>
    </tr>

    <tr>
        <td>Speed Conv. Drying 1</td>
        <td align="center">{{ $item->speed_1 ?? '-' }}</td>
    </tr>

    <tr>
        <td>Speed Conv. Drying 2</td>
        <td align="center">{{ $item->speed_2 ?? '-' }}</td>
    </tr>

    <tr>
        <td>Speed Conv. Drying 3</td>
        <td align="center">{{ $item->speed_3 ?? '-' }}</td>
    </tr>

    <tr>
        <td>Speed Conv. Drying 4</td>
        <td align="center">{{ $item->speed_4 ?? '-' }}</td>
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

<table border="0" cellpadding="3" cellspacing="0" width="100%" style="margin-top:4px;">

    <tr>
        <td width="10%"><b>Standar</b></td>
        <td width="100%"> &nbsp;:&nbsp;
            Suhu PC Kleer : 46 ± 3°C &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            Suhu Heater : 125 - 135°C 
            <br>
            &nbsp;&nbsp;Konsentrasi PC Kleer : 0.7% (Ayam), 1% (Sapi), 0.8% (Cuci)&nbsp;&nbsp;&nbsp;
            Konsentrasi Potassium Sorbate : 0.15%
        </td>
    </tr>

    <tr>
        <td><b>Catatan</b></td>
        <td style="height:30px;">
            :&nbsp;{{ $item->catatan ?? '-' }}
        </td>
    </tr>

</table>

<br>

<table border="0" cellpadding="3" cellspacing="0" width="100%">

    <tr>
        <td width="50%" align="center">
            Diperiksa Oleh
        </td>

        <td width="50%" align="center">
            Disetujui Oleh
        </td>
    </tr>

    <tr>
        <td align="center" style="height:10px;"></td>
        <td align="center"></td>
    </tr>

    <tr>
        <td align="center">
           <u>({{ $item->username_updated ?? $item->username }})</u>
        </td>

        <td align="center">
           <u>({{ $item->nama_spv ?? '-' }})</u>
        </td>
    </tr>

    <tr>
        <td align="center">
            QC
        </td>

        <td align="center">
            QC SPV
        </td>
    </tr>

</table>

</body>
</html>