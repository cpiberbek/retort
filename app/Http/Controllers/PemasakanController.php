<?php

namespace App\Http\Controllers;

use App\Models\Pemasakan;
use App\Models\Mincing;
use App\Models\Stuffing;
use App\Models\Produk;
use App\Models\Mesin;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use App\Models\List_form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use TCPDF;
use Carbon\Carbon;

class PemasakanController extends Controller
{

    public function index(Request $request)
    {
        $search     = $request->input('search');
        $kodeBatch  = $request->input('kode_batch');
        $date       = $request->input('date');
        $shift      = $request->input('shift');
        $userPlant  = Auth::user()->plant;

        $kodeProduksi = [];

        if ($kodeBatch) {
            $kodeProduksi = Mincing::where('kode_produksi', $kodeBatch)
                ->pluck('uuid')
                ->toArray();
        }

        $data = Pemasakan::query()
            ->where('plant', $userPlant)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
                        ->orWhere('nama_produk', 'like', "%{$search}%");
                });
            })
            ->when($kodeBatch, function ($query) use ($kodeProduksi) {
                $query->where(function ($q) use ($kodeProduksi) {
                    foreach ($kodeProduksi as $uuid) {
                        $q->orWhere('kode_produksi', 'like', "%{$uuid}%");
                    }
                });
            })
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->when($shift, function ($query) use ($shift) {
                $query->where('shift', $shift);
            })
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->all());

        $allUUID = [];

        foreach ($data as $row) {
            if (is_array($row->kode_produksi)) {
                $allUUID = array_merge($allUUID, $row->kode_produksi);
            }
        }

        $allUUID = array_unique($allUUID);

        $stuffingData = Mincing::whereIn('uuid', $allUUID)
            ->orWhereIn('kode_produksi', $allUUID)
            ->get()
            ->keyBy('uuid');

        return view('form.pemasakan.index', compact(
            'data',
            'search',
            'kodeBatch',
            'date',
            'shift',
            'stuffingData'
        ));
    }
    /**
     * Export PDF dengan Filter Shift
     */
    public function exportPdf(Request $request)
    {
        $kodeBatch = $request->input('kode_batch');
        $date      = $request->input('date');
        $shift     = $request->input('shift');
        $userPlant = Auth::user()->plant;

        $uuidMincing = Mincing::where('kode_produksi', $kodeBatch)
            ->pluck('uuid')
            ->toArray();

        $items = Pemasakan::query()
            ->where('plant', $userPlant)
            ->whereDate('date', $date)
            ->where('shift', $shift)
            ->where(function ($q) use ($uuidMincing) {
                foreach ($uuidMincing as $uuid) {
                    $q->orWhere('kode_produksi', 'like', "%{$uuid}%");
                }
            })
            ->orderBy('date', 'asc')
            ->get();

        if (ob_get_length()) {
            ob_end_clean();
        }

        $noDokumen = List_form::where('plant', $userPlant)
            ->where('laporan', 'Pemeriksaan Pemasakan')
            ->value('no_dokumen');

        $pdf = new \TCPDF('P', PDF_UNIT, 'F4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle('Pengecekan Pemasakan');
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 10);
        $pdf->SetFont('helvetica', '', 8);

        $pdf->AddPage();

        $html = view('reports.pemasakan', compact('items', 'request', 'noDokumen'))->render();

        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Output('Pengecekan_Pemasakan_' . date('YmdHis') . '.pdf', 'I');
        exit();
    }

    // public function exportExcel(Request $request)
    // {
    //     try {

    //         $date = $request->input('date');
    //         $shift = $request->input('shift');
    //         $kodeBatch = $request->input('kode_batch');
    //         $userPlant = Auth::user()->plant;

    //         if (empty($date) || empty($shift) || empty($kodeBatch)) {
    //             return back()->with('error', 'Filter belum lengkap.');
    //         }

    //         $uuidMincing = Mincing::where('kode_produksi', $kodeBatch)
    //             ->pluck('uuid')
    //             ->toArray();

    //         $items = Pemasakan::query()
    //             ->where('plant', $userPlant)
    //             ->whereDate('date', $date)
    //             ->where('shift', $shift)
    //             ->where(function ($q) use ($uuidMincing) {
    //                 foreach ($uuidMincing as $uuid) {
    //                     $q->orWhere('kode_produksi', 'like', "%{$uuid}%");
    //                 }
    //             })
    //             ->orderBy('date')
    //             ->get();

    //         if ($items->isEmpty()) {
    //             return back()->with('error', 'Data tidak ditemukan.');
    //         }

    //         $noDokumen = List_form::where('plant', $userPlant)
    //             ->where('laporan', 'Pemeriksaan Pemasakan')
    //             ->value('no_dokumen');

    //         $templatePath = app_path('templates/pengecekan_pemasakan.xlsx');

    //         $spreadsheet = IOFactory::load($templatePath);
    //         $sheet = $spreadsheet->getActiveSheet();

    //         $sheet->getStyle($sheet->calculateWorksheetDimension())
    //             ->getFont()
    //             ->setName('Times New Roman');

    //         $sheet->setCellValue(
    //             'A6',
    //             'Hari/Tanggal: ' . Carbon::parse($date)->format('d-m-Y')
    //         );

    //         $sheet->setCellValue(
    //             'K6',
    //             'Shift: ' . $shift
    //         );

    //         $show = function ($value) {

    //             if (is_array($value)) {
    //                 return implode(', ', array_filter($value, fn($v) => $v !== null && $v !== ''));
    //             }

    //             return $value ?? '-';
    //         };

    //         $fmtTime = function ($value) {

    //             if (is_array($value)) {
    //                 $value = $value[0] ?? null;
    //             }

    //             if (!$value) {
    //                 return '-';
    //             }

    //             try {
    //                 return Carbon::parse($value)->format('H:i');
    //             } catch (\Throwable $e) {
    //                 return $value;
    //             }
    //         };

    //         $entries = [];

    //         foreach ($items as $item) {

    //             $c = is_array($item->cooking)
    //                 ? $item->cooking
    //                 : json_decode($item->cooking, true);

    //             $uuidList = is_array($item->kode_produksi)
    //                 ? $item->kode_produksi
    //                 : json_decode($item->kode_produksi, true);

    //             if (!is_array($uuidList)) {
    //                 $uuidList = [];
    //             }

    //             $kodeList = Mincing::whereIn('uuid', $uuidList)
    //                 ->pluck('kode_produksi')
    //                 ->values()
    //                 ->toArray();

    //             foreach ($kodeList as $kodeProd) {

    //                 $entries[] = [
    //                     'item' => $item,
    //                     'c' => $c,
    //                     'kode_produksi' => $kodeProd,
    //                 ];
    //             }
    //         }

    //                 foreach ($entries as $index => $entry) {

    //             $item = $entry['item'];
    //             $c = $entry['c'];

    //             $column = Coordinate::stringFromColumnIndex(
    //                 6 + ($index * 4)
    //             );

    //             $jumlahTray = is_array($item->jumlah_tray)
    //                 ? implode(', ', $item->jumlah_tray)
    //                 : $item->jumlah_tray;

    //             $sheet->setCellValue($column.'9', $item->nama_produk);
    //             $sheet->setCellValue($column.'10', $item->no_chamber);
    //             $sheet->setCellValue($column.'11', $entry['kode_produksi']);
    //             $sheet->setCellValue($column.'12', $item->berat_produk);
    //             $sheet->setCellValue($column.'13', $item->suhu_produk);
    //             $sheet->setCellValue($column.'14', $jumlahTray);

    //             $sheet->setCellValue($column.'16', $show($c['tekanan_angin'] ?? null));
    //             $sheet->setCellValue($column.'17', $show($c['tekanan_steam'] ?? null));
    //             $sheet->setCellValue($column.'18', $show($c['tekanan_air'] ?? null));

    //             $sheet->setCellValue($column.'20', $show($c['suhu_air_awal'] ?? null));
    //             $sheet->setCellValue($column.'21', $show($c['tekanan_awal'] ?? null));
    //             $sheet->setCellValue($column.'22', $fmtTime($c['waktu_mulai_awal'] ?? null));
    //             $sheet->setCellValue($column.'23', $fmtTime($c['waktu_selesai_awal'] ?? null));

    //             $sheet->setCellValue($column.'25', $show($c['suhu_air_proses'] ?? null));
    //             $sheet->setCellValue($column.'26', $show($c['tekanan_proses'] ?? null));
    //             $sheet->setCellValue($column.'27', $fmtTime($c['waktu_mulai_proses'] ?? null));
    //             $sheet->setCellValue($column.'28', $fmtTime($c['waktu_selesai_proses'] ?? null));

    //             $sterAir = is_array($c['suhu_air_sterilisasi'] ?? null)
    //                 ? array_values($c['suhu_air_sterilisasi'])
    //                 : [$c['suhu_air_sterilisasi'] ?? null];

    //             $sterThermo = is_array($c['thermometer_retort'] ?? null)
    //                 ? array_values($c['thermometer_retort'])
    //                 : [$c['thermometer_retort'] ?? null];

    //             $sterTekanan = is_array($c['tekanan_sterilisasi'] ?? null)
    //                 ? array_values($c['tekanan_sterilisasi'])
    //                 : [$c['tekanan_sterilisasi'] ?? null];

    //             for ($i = 0; $i < 4; $i++) {

    //                 $subColumn = Coordinate::stringFromColumnIndex(
    //                     Coordinate::columnIndexFromString($column) + $i
    //                 );

    //                 $sheet->setCellValue($subColumn.'30', $sterAir[$i] ?? '-');
    //                 $sheet->setCellValue($subColumn.'31', $sterThermo[$i] ?? '-');
    //                 $sheet->setCellValue($subColumn.'32', $sterTekanan[$i] ?? '-');
    //             }

    //             $sheet->setCellValue($column.'33', $fmtTime($c['waktu_mulai_sterilisasi'] ?? null));
    //             $sheet->setCellValue($column.'34', $fmtTime($c['waktu_pengecekan_sterilisasi'] ?? null));
    //             $sheet->setCellValue($column.'35', $fmtTime($c['waktu_selesai_sterilisasi'] ?? null));

    //             $sheet->setCellValue($column.'37', $show($c['suhu_air_pendinginan_awal'] ?? null));
    //             $sheet->setCellValue($column.'38', $show($c['tekanan_pendinginan_awal'] ?? null));
    //             $sheet->setCellValue($column.'39', $fmtTime($c['waktu_mulai_pendinginan_awal'] ?? null));
    //             $sheet->setCellValue($column.'40', $fmtTime($c['waktu_selesai_pendinginan_awal'] ?? null));

    //             $sheet->setCellValue($column.'42', $show($c['suhu_air_pendinginan'] ?? null));
    //             $sheet->setCellValue($column.'43', $show($c['tekanan_pendinginan'] ?? null));
    //             $sheet->setCellValue($column.'44', $fmtTime($c['waktu_mulai_pendinginan'] ?? null));
    //             $sheet->setCellValue($column.'45', $fmtTime($c['waktu_selesai_pendinginan'] ?? null));

    //             $sheet->setCellValue($column.'47', $show($c['suhu_air_akhir'] ?? null));
    //             $sheet->setCellValue($column.'48', $show($c['tekanan_akhir'] ?? null));
    //             $sheet->setCellValue($column.'49', $fmtTime($c['waktu_mulai_akhir'] ?? null));
    //             $sheet->setCellValue($column.'50', $fmtTime($c['waktu_selesai_akhir'] ?? null));

    //             $sheet->setCellValue($column.'52', $fmtTime($c['waktu_mulai_total'] ?? null));
    //             $sheet->setCellValue($column.'53', $fmtTime($c['waktu_selesai_total'] ?? null));

    //             $sheet->setCellValue($column.'55', $show($c['suhu_produk_akhir'] ?? null));
    //             $sheet->setCellValue($column.'56', $show($c['panjang'] ?? null));
    //             $sheet->setCellValue($column.'57', $show($c['diameter'] ?? null));
    //             $sheet->setCellValue($column.'58', $show($c['rasa'] ?? null));
    //             $sheet->setCellValue($column.'59', $show($c['warna'] ?? null));
    //             $sheet->setCellValue($column.'60', $show($c['aroma'] ?? null));
    //             $sheet->setCellValue($column.'61', $show($c['texture'] ?? null));
    //             $sheet->setCellValue($column.'62', $show($c['sobek_seal'] ?? null));

    //             $sheet->setCellValue($column.'64', $item->username ?? '-');
    //             $sheet->setCellValue($column.'65', $item->nama_produksi ?? '-');
    //         }

    //         $sheet->setCellValue(
    //             'Q66',
    //             $noDokumen !== '' ? $noDokumen : '-'
    //         );


    //         $catatan = $items
    //             ->pluck('catatan')
    //             ->filter(fn($v) => filled(trim((string) $v)))
    //             ->unique()
    //             ->implode(', ');

    //         $sheet->setCellValue(
    //             'A68',
    //             $catatan !== '' ? $catatan : '-'
    //         );

    //         $namaSpv = $items
    //             ->pluck('nama_spv')
    //             ->filter(fn($v) => filled(trim((string) $v)))
    //             ->unique()
    //             ->values();

    //         $sheet->setCellValue(
    //             'M70',
    //             $items->whereNull('nama_spv')->isEmpty()
    //                 ? '(' . ($namaSpv->count() === 1
    //                     ? $namaSpv->first()
    //                     : $namaSpv->implode(', ')) . ')'
    //                 : '(____________________)'
    //         );

    //         $filename = 'Pemeriksaan_Pemasakan_' .
    //             Carbon::parse($date)->format('d-m-Y') .
    //             '_Shift' . $shift . '.xlsx';

    //         while (ob_get_level()) {
    //             ob_end_clean();
    //         }

    //         return response()->streamDownload(function () use ($spreadsheet) {
    //             $writer = new Xlsx($spreadsheet);
    //             $writer->save('php://output');
    //         }, $filename, [
    //             'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    //             'Cache-Control' => 'max-age=0',
    //         ]);

    //     } catch (\Throwable $e) {
    //         return back()->with('error', 'Gagal export: ' . $e->getMessage());
    //     }
    // }

    public function exportExcel(Request $request)
    {
        try {

            $date = $request->input('date');
            $shift = $request->input('shift');
            $kodeBatch = $request->input('kode_batch');
            $userPlant = Auth::user()->plant;

            if (!$date || !$shift || !$kodeBatch) {
                return back()->with('error', 'Filter belum lengkap.');
            }

            $uuidMincing = Mincing::where('kode_produksi', $kodeBatch)
                ->pluck('uuid')
                ->toArray();

            $items = Pemasakan::query()
                ->where('plant', $userPlant)
                ->whereDate('date', $date)
                ->where('shift', $shift)
                ->where(function ($q) use ($uuidMincing) {
                    foreach ($uuidMincing as $uuid) {
                        $q->orWhere('kode_produksi', 'like', "%{$uuid}%");
                    }
                })
                ->orderBy('date')
                ->get();

            if ($items->isEmpty()) {
                return back()->with('error', 'Data tidak ditemukan.');
            }

            $noDokumen = List_form::where('plant', $userPlant)
                ->where('laporan', 'Pemeriksaan Pemasakan')
                ->value('no_dokumen');

            $templatePath = app_path('templates/pengecekan_pemasakan.xlsx');

            $spreadsheet = IOFactory::load($templatePath);
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->getStyle($sheet->calculateWorksheetDimension())
                ->getFont()
                ->setName('Times New Roman');

            $sheet->setCellValue(
                'A6',
                'Hari/Tanggal: ' . Carbon::parse($date)->format('d-m-Y')
            );

            $sheet->setCellValue(
                'K6',
                'Shift: ' . $shift
            );

            $show = function ($value) {

                if (is_array($value)) {
                    return implode(', ', array_filter($value, fn($v) => $v !== null && $v !== ''));
                }

                return filled($value) ? $value : '-';
            };

            $fmtTime = function ($value) {

                if (is_array($value)) {
                    $value = $value[0] ?? null;
                }

                if (!$value) {
                    return '-';
                }

                try {
                    return Carbon::parse($value)->format('H:i');
                } catch (\Throwable $e) {
                    return $value;
                }
            };

            $entries = [];

            foreach ($items as $item) {

                $c = is_array($item->cooking)
                    ? $item->cooking
                    : json_decode($item->cooking, true);

                $uuidList = is_array($item->kode_produksi)
                    ? $item->kode_produksi
                    : json_decode($item->kode_produksi, true);

                if (!is_array($uuidList)) {
                    $uuidList = [];
                }

                $kodeList = Mincing::whereIn('uuid', $uuidList)
                    ->pluck('kode_produksi')
                    ->values()
                    ->toArray();

                foreach ($kodeList as $kodeProd) {

                    $entries[] = [
                        'item' => $item,
                        'c' => $c,
                        'kode_produksi' => $kodeProd,
                    ];
                }
            }

            $baseColumn = 6;
            $groupWidth = 4;
            $templateStart = 14;
            $templateEnd = 17;

            if (count($entries) > 3) {

                for ($copy = 3; $copy < count($entries); $copy++) {

                    $targetStart = $baseColumn + ($copy * $groupWidth);

                    $sheet->insertNewColumnBefore(
                        Coordinate::stringFromColumnIndex($targetStart),
                        $groupWidth
                    );

                    for ($offset = 0; $offset < $groupWidth; $offset++) {

                        $src = Coordinate::stringFromColumnIndex($templateStart + $offset);
                        $dst = Coordinate::stringFromColumnIndex($targetStart + $offset);

                        $sheet->duplicateStyle(
                            $sheet->getStyle($src . '1'),
                            $dst . '1:' . $dst . $sheet->getHighestRow()
                        );

                        $sheet->getColumnDimension($dst)
                            ->setWidth(
                                $sheet->getColumnDimension($src)->getWidth()
                            );
                    }

                    foreach ($sheet->getMergeCells() as $merge) {

                        preg_match('/([A-Z]+)(\d+):([A-Z]+)(\d+)/', $merge, $m);

                        if (!$m) {
                            continue;
                        }

                        $start = Coordinate::columnIndexFromString($m[1]);
                        $end = Coordinate::columnIndexFromString($m[3]);

                        if ($start >= $templateStart && $end <= $templateEnd) {

                            $newStart = Coordinate::stringFromColumnIndex(
                                $targetStart + ($start - $templateStart)
                            );

                            $newEnd = Coordinate::stringFromColumnIndex(
                                $targetStart + ($end - $templateStart)
                            );

                            $sheet->mergeCells(
                                $newStart . $m[2] . ':' . $newEnd . $m[4]
                            );
                        }
                    }
                }
            }

            foreach ($entries as $index => $entry) {

                $item = $entry['item'];
                $c = $entry['c'];

                $column = Coordinate::stringFromColumnIndex(
                    $baseColumn + ($index * $groupWidth)
                );

                $jumlahTray = is_array($item->jumlah_tray)
                    ? implode(', ', $item->jumlah_tray)
                    : $item->jumlah_tray;

                $item = $entry['item'];
                $c = $entry['c'];

                $column = Coordinate::stringFromColumnIndex(
                    $baseColumn + ($index * $groupWidth)
                );

                $jumlahTray = is_array($item->jumlah_tray)
                    ? implode(', ', $item->jumlah_tray)
                    : $item->jumlah_tray;

                $sheet->setCellValue($column . '9', $item->nama_produk);
                $sheet->setCellValue($column . '10', $item->no_chamber);
                $sheet->setCellValue($column . '11', $entry['kode_produksi']);
                $sheet->setCellValue($column . '12', $item->berat_produk);
                $sheet->setCellValue($column . '13', $item->suhu_produk);
                $sheet->setCellValue($column . '14', $jumlahTray);

                $sheet->setCellValue($column . '16', $show($c['tekanan_angin'] ?? null));
                $sheet->setCellValue($column . '17', $show($c['tekanan_steam'] ?? null));
                $sheet->setCellValue($column . '18', $show($c['tekanan_air'] ?? null));

                $sheet->setCellValue($column . '20', $show($c['suhu_air_awal'] ?? null));
                $sheet->setCellValue($column . '21', $show($c['tekanan_awal'] ?? null));
                $sheet->setCellValue($column . '22', $fmtTime($c['waktu_mulai_awal'] ?? null));
                $sheet->setCellValue($column . '23', $fmtTime($c['waktu_selesai_awal'] ?? null));

                $sheet->setCellValue($column . '25', $show($c['suhu_air_proses'] ?? null));
                $sheet->setCellValue($column . '26', $show($c['tekanan_proses'] ?? null));
                $sheet->setCellValue($column . '27', $fmtTime($c['waktu_mulai_proses'] ?? null));
                $sheet->setCellValue($column . '28', $fmtTime($c['waktu_selesai_proses'] ?? null));

                $sterAir = is_array($c['suhu_air_sterilisasi'] ?? null)
                    ? array_values($c['suhu_air_sterilisasi'])
                    : [$c['suhu_air_sterilisasi'] ?? null];

                $sterThermo = is_array($c['thermometer_retort'] ?? null)
                    ? array_values($c['thermometer_retort'])
                    : [$c['thermometer_retort'] ?? null];

                $sterTekanan = is_array($c['tekanan_sterilisasi'] ?? null)
                    ? array_values($c['tekanan_sterilisasi'])
                    : [$c['tekanan_sterilisasi'] ?? null];

                for ($i = 0; $i < 4; $i++) {

                    $subColumn = Coordinate::stringFromColumnIndex(
                        Coordinate::columnIndexFromString($column) + $i
                    );

                    $sheet->setCellValue($subColumn . '30', $sterAir[$i] ?? '-');
                    $sheet->setCellValue($subColumn . '31', $sterThermo[$i] ?? '-');
                    $sheet->setCellValue($subColumn . '32', $sterTekanan[$i] ?? '-');
                }

                $sheet->setCellValue($column . '33', $fmtTime($c['waktu_mulai_sterilisasi'] ?? null));
                $sheet->setCellValue($column . '34', $fmtTime($c['waktu_pengecekan_sterilisasi'] ?? null));
                $sheet->setCellValue($column . '35', $fmtTime($c['waktu_selesai_sterilisasi'] ?? null));

                $sheet->setCellValue($column . '37', $show($c['suhu_air_pendinginan_awal'] ?? null));
                $sheet->setCellValue($column . '38', $show($c['tekanan_pendinginan_awal'] ?? null));
                $sheet->setCellValue($column . '39', $fmtTime($c['waktu_mulai_pendinginan_awal'] ?? null));
                $sheet->setCellValue($column . '40', $fmtTime($c['waktu_selesai_pendinginan_awal'] ?? null));

                $sheet->setCellValue($column . '42', $show($c['suhu_air_pendinginan'] ?? null));
                $sheet->setCellValue($column . '43', $show($c['tekanan_pendinginan'] ?? null));
                $sheet->setCellValue($column . '44', $fmtTime($c['waktu_mulai_pendinginan'] ?? null));
                $sheet->setCellValue($column . '45', $fmtTime($c['waktu_selesai_pendinginan'] ?? null));

                $sheet->setCellValue($column . '47', $show($c['suhu_air_akhir'] ?? null));
                $sheet->setCellValue($column . '48', $show($c['tekanan_akhir'] ?? null));
                $sheet->setCellValue($column . '49', $fmtTime($c['waktu_mulai_akhir'] ?? null));
                $sheet->setCellValue($column . '50', $fmtTime($c['waktu_selesai_akhir'] ?? null));

                $sheet->setCellValue($column . '52', $fmtTime($c['waktu_mulai_total'] ?? null));
                $sheet->setCellValue($column . '53', $fmtTime($c['waktu_selesai_total'] ?? null));

                $sheet->setCellValue($column . '55', $show($c['suhu_produk_akhir'] ?? null));
                $sheet->setCellValue($column . '56', $show($c['panjang'] ?? null));
                $sheet->setCellValue($column . '57', $show($c['diameter'] ?? null));
                $sheet->setCellValue($column . '58', $show($c['rasa'] ?? null));
                $sheet->setCellValue($column . '59', $show($c['warna'] ?? null));
                $sheet->setCellValue($column . '60', $show($c['aroma'] ?? null));
                $sheet->setCellValue($column . '61', $show($c['texture'] ?? null));
                $sheet->setCellValue($column . '62', $show($c['sobek_seal'] ?? null));

                $sheet->setCellValue($column . '64', $item->username ?? '-');
                $sheet->setCellValue($column . '65', $item->nama_produksi ?? '-');
            }

            foreach ($entries as $index => $entry) {

                $columnIndex = 6 + ($index * 4);

                for ($i = 0; $i < 4; $i++) {

                    $col = Coordinate::stringFromColumnIndex($columnIndex + $i);

                    $sheet->getStyle($col . '8:' . $col . '65')
                        ->getBorders()
                        ->getAllBorders()
                        ->setBorderStyle(Border::BORDER_THIN);

                    $sheet->getStyle($col . '8:' . $col . '65')
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                        ->setVertical(Alignment::VERTICAL_CENTER);

                    $sheet->getStyle($col . '8:' . $col . '65')
                        ->getFont()
                        ->setBold(true);
                }

                $grayRows = [
                    8,
                    15,
                    19,
                    24,
                    29,
                    36,
                    41,
                    46,
                    51,
                    54,
                    63,
                ];

                foreach ($grayRows as $row) {

                    for ($i = 0; $i < 4; $i++) {

                        $col = Coordinate::stringFromColumnIndex($columnIndex + $i);

                        $sheet->getStyle($col . $row)
                            ->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setARGB('D9D9D9');
                    }
                }
            }

            $lastColumn = Coordinate::stringFromColumnIndex(
                6 + (count($entries) * 4) - 1
            );

            $sheet->setCellValue(
                $lastColumn . '66',
                $noDokumen ?: '-'
            );

            $sheet->getStyle($lastColumn . '66')
                ->getFont()
                ->setItalic(true);

            $catatan = $items
                ->pluck('catatan')
                ->filter(fn($v) => filled(trim((string) $v)))
                ->unique()
                ->implode(', ');

            $sheet->setCellValue(
                'A68',
                $catatan !== '' ? $catatan : '-'
            );

            $namaSpv = $items
                ->pluck('nama_spv')
                ->filter(fn($v) => filled(trim((string) $v)))
                ->unique()
                ->values();

            $ttd = $items->whereNull('nama_spv')->isEmpty()
                ? '(' . ($namaSpv->count() === 1
                    ? $namaSpv->first()
                    : $namaSpv->implode(', ')) . ')'
                : '(____________________)';

            $sheet->setCellValue('M70', $ttd);

            $sheet->getStyle('M70')
                ->getFont()
                ->setUnderline(true);

            $filename = 'Pemeriksaan_Pemasakan_' .
                Carbon::parse($date)->format('d-m-Y') .
                '_Shift' . $shift . '.xlsx';

            while (ob_get_level()) {
                ob_end_clean();
            }

            return response()->streamDownload(function () use ($spreadsheet) {

                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
            }, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Cache-Control' => 'max-age=0',
            ]);
        } catch (\Throwable $e) {

            return back()->with(
                'error',
                'Gagal export: ' . $e->getMessage()
            );
        }
    }

    public function create()
    {
        $userPlant = Auth::user()->plant;
        $produks = Produk::where('plant', $userPlant)->get();
        $batches = Stuffing::latest()->take(3)->get();
        $list_chambers = Mesin::where('plant', $userPlant)
            ->where('jenis_mesin', 'Chamber')
            ->orderBy('nama_mesin')
            ->get(['uuid', 'nama_mesin']);

        return view('form.pemasakan.create', compact('produks', 'list_chambers', 'batches'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $username   = Auth::user()->username ?? 'User RTM';
        $userPlant  = Auth::user()->plant;
        $nama_produksi = session()->has('selected_produksi')
            ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
            : 'Produksi RTT';

        $request->validate([
            'date'          => 'required|date',
            'shift'         => 'required',
            'nama_produk'   => 'required',
            'kode_produksi' => 'required|array',
            'jumlah_tray'   => 'required|array',
            'no_chamber'    => 'required',
            'berat_produk'  => 'required|numeric',
            'suhu_produk'   => 'required|numeric',
            'total_reject'  => 'nullable|array',
            'catatan'       => 'nullable|string',
            'cooking'       => 'nullable|array',
        ]);

        $data = $request->only([
            'date',
            'shift',
            'nama_produk',
            'kode_produksi',
            'no_chamber',
            'berat_produk',
            'suhu_produk',
            'jumlah_tray',
            'total_reject',
            'catatan',
        ]);
        $data['cooking']             = $request->input('cooking', []);
        $data['username']            = $username;
        $data['plant']               = $userPlant;
        $data['nama_produksi']       = $nama_produksi;
        $data['status_produksi']     = "1";
        $data['tgl_update_produksi'] = now()->addHour();
        $data['status_spv']          = "0";

        Pemasakan::create($data);

        return redirect()->route('pemasakan.index')->with('success', 'Pengecekan Pemasakan berhasil disimpan');
    }

    /** ====================== QC ====================== **/
    public function update(string $uuid)
    {
        $pemasakan = Pemasakan::where('uuid', $uuid)->firstOrFail();
        $userPlant = Auth::user()->plant;
        $produks = Produk::where('plant', $userPlant)->get();
        $list_chambers = Mesin::where('plant', $userPlant)
            ->where('jenis_mesin', 'Chamber')
            ->orderBy('nama_mesin')
            ->get(['uuid', 'nama_mesin']);

        $pemasakanData = [];
        if (!empty($pemasakan->cooking)) {
            $pemasakanData = is_string($pemasakan->cooking)
                ? (json_decode($pemasakan->cooking, true) ?? [])
                : (array) $pemasakan->cooking;
        }

        return view('form.pemasakan.update', compact('pemasakan', 'produks', 'pemasakanData', 'list_chambers'));
    }

    public function update_qc(Request $request, string $uuid)
    {
        $pemasakan = Pemasakan::where('uuid', $uuid)->firstOrFail();
        $username_updated = Auth::user()->username ?? 'User QC';

        $request->validate([
            'date'          => 'required|date',
            'shift'         => 'required',
            'nama_produk'   => 'required',
            'kode_produksi' => 'required|array',
            'no_chamber'    => 'required',
            'berat_produk'  => 'required|numeric',
            'suhu_produk'   => 'required|numeric',
            'jumlah_tray'   => 'required|array',
            'total_reject'  => 'nullable|array',
            'catatan'       => 'nullable|string',
            'cooking'       => 'nullable|array',
        ]);

        $data = [
            'date'             => $request->date,
            'shift'            => $request->shift,
            'nama_produk'      => $request->nama_produk,
            'kode_produksi'    => $request->kode_produksi,
            'no_chamber'       => $request->no_chamber,
            'berat_produk'     => $request->berat_produk,
            'suhu_produk'      => $request->suhu_produk,
            'jumlah_tray'      => $request->jumlah_tray,
            'total_reject'     => $request->total_reject,
            'catatan'          => $request->catatan,
            'username_updated' => $username_updated,
            'cooking'          => $request->input('cooking', []),
        ];

        $pemasakan->update($data);

        return redirect()->route('pemasakan.index')->with('success', 'Data QC berhasil diperbarui');
    }

    /** ====================== SPV ====================== **/
    public function edit(string $uuid)
    {
        $pemasakan = Pemasakan::where('uuid', $uuid)->firstOrFail();
        $userPlant = Auth::user()->plant;
        $produks = Produk::where('plant', $userPlant)->get();
        $list_chambers = Mesin::where('plant', $userPlant)
            ->where('jenis_mesin', 'Chamber')
            ->orderBy('nama_mesin')
            ->get(['uuid', 'nama_mesin']);

        $pemasakanData = [];
        if (!empty($pemasakan->cooking)) {
            $pemasakanData = is_string($pemasakan->cooking)
                ? (json_decode($pemasakan->cooking, true) ?? [])
                : (array) $pemasakan->cooking;
        }

        return view('form.pemasakan.edit', compact('pemasakan', 'produks', 'pemasakanData', 'list_chambers'));
    }

    public function edit_spv(Request $request, string $uuid)
    {
        $pemasakan = Pemasakan::where('uuid', $uuid)->firstOrFail();

        $request->validate([
            'date'          => 'required|date',
            'shift'         => 'required',
            'nama_produk'   => 'required',
            'kode_produksi' => 'required|array', // Pastikan di form name="kode_produksi[]"
            'jumlah_tray'   => 'required|array', // Pastikan di form name="jumlah_tray[]"
            'no_chamber'    => 'required',
            'berat_produk'  => 'required|numeric',
            'suhu_produk'   => 'required|numeric',
            'total_reject'  => 'nullable|array',
            'catatan'       => 'nullable|string',
            'cooking'       => 'nullable|array',
        ]);

        // Ambil data dari request
        $data = $request->only([
            'date',
            'shift',
            'nama_produk',
            'kode_produksi',
            'no_chamber',
            'berat_produk',
            'suhu_produk',
            'jumlah_tray',
            'total_reject',
            'catatan',
            'cooking'
        ]);

        // Model akan otomatis melakukan json_encode ke database karena $casts = 'array'
        $pemasakan->update($data);

        return redirect()->route('pemasakan.index')->with('success', 'Data berhasil diperbarui');
    }

    public function verification(Request $request)
    {
        $search     = $request->input('search');
        $date       = $request->input('date');
        $userPlant  = Auth::user()->plant;

        $data = Pemasakan::query()
            ->where('plant', $userPlant)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
                        ->orWhere('nama_produk', 'like', "%{$search}%")
                        ->orWhere('kode_produksi', 'like', "%{$search}%");
                });
            })
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->all());

        return view('form.pemasakan.index', compact('data', 'search', 'date'));
    }

    public function updateVerification(Request $request, $uuid)
    {
        $request->validate([
            'status_spv'  => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:255',
        ]);

        $pemasakan = Pemasakan::where('uuid', $uuid)->firstOrFail();

        $pemasakan->update([
            'status_spv'      => $request->status_spv,
            'catatan_spv'     => $request->catatan_spv,
            'nama_spv'        => Auth::user()->username,
            'tgl_update_spv'  => now(),
        ]);

        return redirect()->route('pemasakan.index', [
            'page' => $request->input('page', 1),
            'search' => $request->input('search'),
            'date' => $request->input('date'),
        ])->with('success', 'Status Verifikasi Pengecekan Pemasakan berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $pemasakan = Pemasakan::where('uuid', $uuid)->firstOrFail();
        $pemasakan->delete();
        return redirect()->route('pemasakan.index')->with('success', 'Pemasakan berhasil dihapus');
    }

    public function recyclebin()
    {
        $pemasakan = Pemasakan::onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->paginate(10);

        return view('form.pemasakan.recyclebin', compact('pemasakan'));
    }
    public function restore($uuid)
    {
        $pemasakan = Pemasakan::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $pemasakan->restore();

        return redirect()->route('pemasakan.recyclebin')
            ->with('success', 'Data berhasil direstore.');
    }
    public function deletePermanent($uuid)
    {
        $pemasakan = Pemasakan::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $pemasakan->forceDelete();

        return redirect()->route('pemasakan.recyclebin')
            ->with('success', 'Data berhasil dihapus permanen.');
    }
}
