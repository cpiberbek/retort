<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class SuhuExport implements WithEvents
{
    protected $items;       // Collection of Suhu records (sudah difilter date & shift)
    protected $areaSuhus;   // Collection of Area_suhu (master area)
    protected $date;
    protected $shift;

    public function __construct($items, $areaSuhus, $date = null, $shift = null)
    {
        $this->items     = $items;
        $this->areaSuhus = $areaSuhus;
        $this->date      = $date;
        $this->shift      = $shift;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // ----------------------------------------------------------------
                // Warna & gaya
                // ----------------------------------------------------------------
                $headerBlue  = 'FF4F81BD';
                $redFont     = 'FFFF0000';
                $blueFont    = 'FF0070C0';
                $white       = 'FFFFFFFF';
                $black       = 'FF000000';
                $lightYellow = 'FFFFFDE7';
                $lightBlue   = 'FFE9F0FB';

                // ----------------------------------------------------------------
                // Bangun daftar area dari master
                // ----------------------------------------------------------------
                $areas = $this->areaSuhus; // Collection Area_suhu

                // Jumlah kolom data = jumlah area × 2 (Suhu + RH)
                // Kolom layout:
                //   A = Pukul
                //   B...(B + count*2 - 1) = area suhu, area RH, ...
                //   Kolom Keterangan = setelah semua area
                //   Kolom QC, PROD = terakhir

                $areaCount   = count($areas);
                $startCol    = 2; // B = index 2 (1-based dalam PhpSpreadsheet)

                // Helper: index -> kolom huruf
                $col = function (int $index): string {
                    return \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index);
                };

                // Kolom per area: Suhu = startCol + i*2, RH = startCol + i*2 + 1
                $suhuCols = [];
                $rhCols   = [];
                foreach ($areas as $i => $area) {
                    $suhuCols[$i] = $startCol + $i * 2;
                    $rhCols[$i]   = $startCol + $i * 2 + 1;
                }

                $keteranganColIdx = $startCol + $areaCount * 2;
                $qcColIdx         = $keteranganColIdx + 1;
                $prodColIdx       = $qcColIdx + 1;
                $lastColIdx       = $prodColIdx;

                $lastColLetter = $col($lastColIdx);

                // ----------------------------------------------------------------
                // ROW 1 – Nama Perusahaan (kiri)
                // ----------------------------------------------------------------
                $sheet->mergeCells('A1:C1');
                $sheet->setCellValue('A1', 'PT. Charoen Pokphand Indonesia');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(9);

                // ----------------------------------------------------------------
                // ROW 2 – Sub-perusahaan
                // ----------------------------------------------------------------
                $sheet->mergeCells('A2:C2');
                $sheet->setCellValue('A2', 'Food Division');
                $sheet->getStyle('A2')->getFont()->setSize(8);

                // ----------------------------------------------------------------
                // ROW 3 – Judul tengah
                // ----------------------------------------------------------------
                $midStart = 'D';
                $sheet->mergeCells("D3:{$lastColLetter}3");
                $sheet->setCellValue('D3', 'PEMERIKSAAN SUHU DAN RH');
                $sheet->getStyle("D3:{$lastColLetter}3")->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 13],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(3)->setRowHeight(22);

                // ----------------------------------------------------------------
                // ROW 4 – Hari/Tanggal & Shift
                // ----------------------------------------------------------------
                $tanggalLabel = $this->date
                    ? \Carbon\Carbon::parse($this->date)->translatedFormat('l, d F Y')
                    : '_______________';
                $shiftLabel = $this->shift ? 'Shift ' . $this->shift : '________';

                $sheet->mergeCells('A4:D4');
                $sheet->setCellValue('A4', 'Hari/Tanggal : ' . $tanggalLabel);
                $sheet->mergeCells('E4:H4');
                $sheet->setCellValue('E4', 'Shift : ' . $shiftLabel);
                $sheet->getStyle('A4:H4')->applyFromArray([
                    'font'      => ['size' => 9],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(4)->setRowHeight(14);

                // ----------------------------------------------------------------
                // ROW 5 – Header "Ruangan (°C)" + "PARAF"
                // ----------------------------------------------------------------
                $ruanganEnd = $col($keteranganColIdx - 1);
                $sheet->mergeCells("B5:{$ruanganEnd}5");
                $sheet->setCellValue('B5', 'Ruangan (°C)');

                $sheet->mergeCells($col($qcColIdx) . '5:' . $col($prodColIdx) . '5');
                $sheet->setCellValue($col($qcColIdx) . '5', 'PARAF');

                $sheet->getStyle("B5:{$lastColLetter}5")->applyFromArray([
                    'font'      => ['bold' => true, 'color' => ['argb' => $white], 'size' => 9],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerBlue]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getStyle('A5')->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerBlue]],
                ]);
                $sheet->getRowDimension(5)->setRowHeight(16);

                // ----------------------------------------------------------------
                // ROW 6 – Sub-header area (nama area, span 2 row)
                // ----------------------------------------------------------------
                $sheet->mergeCells('A6:A7');
                $sheet->setCellValue('A6', 'Pukul');

                foreach ($areas as $i => $area) {
                    $sc = $col($suhuCols[$i]);
                    $rc = $col($rhCols[$i]);

                    if ($area->rh_min !== null || $area->rh_max !== null) {
                        // Ada RH → merge 1 baris saja, baris 7 isi Suhu & RH
                        $sheet->mergeCells("{$sc}6:{$rc}6");
                        $sheet->setCellValue("{$sc}6", $area->area);
                        $sheet->setCellValue("{$sc}7", 'Suhu');
                        $sheet->setCellValue("{$rc}7", 'RH');
                    } else {
                        // Tidak ada RH → merge 2 baris & 2 kolom
                        $sheet->mergeCells("{$sc}6:{$rc}7");
                        $sheet->setCellValue("{$sc}6", $area->area);
                    }
                }

                // Keterangan span 2 baris
                $kCol = $col($keteranganColIdx);
                $sheet->mergeCells("{$kCol}6:{$kCol}7");
                $sheet->setCellValue("{$kCol}6", 'Keterangan');

                // QC span 2 baris
                $sheet->mergeCells($col($qcColIdx) . '6:' . $col($qcColIdx) . '7');
                $sheet->setCellValue($col($qcColIdx) . '6', 'QC');

                // PROD span 2 baris
                $sheet->mergeCells($col($prodColIdx) . '6:' . $col($prodColIdx) . '7');
                $sheet->setCellValue($col($prodColIdx) . '6', 'PROD.');

                // Style header rows 6-7
                $sheet->getStyle("A6:{$lastColLetter}7")->applyFromArray([
                    'font'      => ['bold' => true, 'color' => ['argb' => $white], 'size' => 8],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerBlue]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                ]);
                $sheet->getRowDimension(6)->setRowHeight(24);
                $sheet->getRowDimension(7)->setRowHeight(14);

                // ----------------------------------------------------------------
                // ROW 8 – STD (standar suhu/RH dari master)
                // ----------------------------------------------------------------
                $sheet->setCellValue('A8', 'STD (°C)');
                $sheet->getStyle('A8')->applyFromArray([
                    'font'      => ['bold' => true, 'color' => ['argb' => $redFont], 'size' => 8],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $stdStyle = [
                    'font'      => ['bold' => true, 'color' => ['argb' => $redFont], 'size' => 7],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                ];

                foreach ($areas as $i => $area) {
                    $sc = $col($suhuCols[$i]);
                    $rc = $col($rhCols[$i]);

                    // Standar Suhu
                    if ($area->standar_min !== null && $area->standar_max !== null) {
                        $sheet->setCellValue("{$sc}8", $area->standar_min . ' – ' . $area->standar_max);
                    }
                    $sheet->getStyle("{$sc}8")->applyFromArray($stdStyle);

                    // Standar RH
                    if ($area->rh_min !== null && $area->rh_max !== null) {
                        $sheet->setCellValue("{$rc}8", $area->rh_min . ' – ' . $area->rh_max);
                    }
                    $sheet->getStyle("{$rc}8")->applyFromArray($stdStyle);
                }

                $sheet->getRowDimension(8)->setRowHeight(14);

                // ----------------------------------------------------------------
                // ROW 9-32 – Data per jam (0:00 – 23:00)
                // ----------------------------------------------------------------
                $startDataRow = 9;
                $shiftBlueHours = ['0:00', '8:00', '16:00'];

                // Index data dari koleksi items: key by pukul (H:i)
                // Jika banyak record per pukul (multi-shift), ambil semuanya
                $dataByPukul = [];
                foreach ($this->items as $rec) {
                    $pukul = \Carbon\Carbon::parse($rec->pukul)->format('G:i');
                    // Normalisasi jam tanpa leading zero
                    $dataByPukul[$pukul][] = $rec;
                }

                // Buat lookup suhu/rh dari hasil_suhu JSON
                $getVal = function ($rec, string $areaName, string $type) {
                    if (!$rec) return null;
                    $hasil = is_array($rec->hasil_suhu) ? $rec->hasil_suhu : (json_decode($rec->hasil_suhu, true) ?? []);
                    $found = collect($hasil)->firstWhere('area', $areaName);
                    return $found[$type] ?? null;
                };

                for ($h = 0; $h <= 23; $h++) {
                    $hourLabel = $h . ':00';
                    $row       = $startDataRow + $h;

                    $sheet->setCellValue('A' . $row, $hourLabel);

                    $fontColor = in_array($hourLabel, $shiftBlueHours) ? $blueFont : $black;
                    $isBold    = in_array($hourLabel, $shiftBlueHours);
                    $sheet->getStyle('A' . $row)->applyFromArray([
                        'font'      => ['color' => ['argb' => $fontColor], 'bold' => $isBold, 'size' => 8],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);

                    // Cari record untuk jam ini
                    $recs = $dataByPukul[$hourLabel] ?? [];
                    $rec  = $recs[0] ?? null; // ambil pertama jika ada

                    if ($rec) {
                        // Isi nilai suhu dan RH per area
                        foreach ($areas as $i => $area) {
                            $suhuVal = $getVal($rec, $area->area, 'suhu');
                            $rhVal   = $getVal($rec, $area->area, 'rh');

                            $sc = $col($suhuCols[$i]);
                            $rc = $col($rhCols[$i]);

                            // Tentukan warna suhu
                            $suhuColor = $black;
                            if ($suhuVal !== null && $suhuVal !== '-' && is_numeric($suhuVal)) {
                                if ($area->standar_min !== null && $area->standar_max !== null) {
                                    $min = min($area->standar_min, $area->standar_max);
                                    $max = max($area->standar_min, $area->standar_max);
                                    $suhuColor = ($suhuVal >= $min && $suhuVal <= $max)
                                        ? 'FF006400'   // hijau gelap
                                        : 'FFCC0000';  // merah gelap
                                }
                            }

                            // Tentukan warna RH
                            $rhColor = $black;
                            if ($rhVal !== null && $rhVal !== '-' && is_numeric($rhVal)) {
                                if ($area->rh_min !== null && $area->rh_max !== null) {
                                    $min = min($area->rh_min, $area->rh_max);
                                    $max = max($area->rh_min, $area->rh_max);
                                    $rhColor = ($rhVal >= $min && $rhVal <= $max)
                                        ? 'FF006400'
                                        : 'FFCC0000';
                                }
                            }

                            if ($suhuVal !== null) {
                                $sheet->setCellValue("{$sc}{$row}", $suhuVal === '-' ? '-' : (is_numeric($suhuVal) ? (float)$suhuVal : $suhuVal));
                                $sheet->getStyle("{$sc}{$row}")->applyFromArray([
                                    'font'      => ['color' => ['argb' => $suhuColor], 'bold' => true, 'size' => 8],
                                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                                ]);
                            }

                            if ($rhVal !== null && ($area->rh_min !== null || $area->rh_max !== null)) {
                                $sheet->setCellValue("{$rc}{$row}", $rhVal === '-' ? '-' : (is_numeric($rhVal) ? (float)$rhVal : $rhVal));
                                $sheet->getStyle("{$rc}{$row}")->applyFromArray([
                                    'font'      => ['color' => ['argb' => $rhColor], 'bold' => true, 'size' => 8],
                                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                                ]);
                            }
                        }

                        // Keterangan
                        $keteranganVal = $rec->keterangan ?? '';
                        $sheet->setCellValue($col($keteranganColIdx) . $row, $keteranganVal);
                        $sheet->getStyle($col($keteranganColIdx) . $row)->applyFromArray([
                            'font'      => ['size' => 7],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                        ]);

                        // QC (username)
                        $sheet->setCellValue($col($qcColIdx) . $row, $rec->username ?? '');
                        $sheet->getStyle($col($qcColIdx) . $row)->applyFromArray([
                            'font'      => ['size' => 7],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        ]);

                        // PROD
                        $sheet->setCellValue($col($prodColIdx) . $row, $rec->nama_produksi ?? '');
                        $sheet->getStyle($col($prodColIdx) . $row)->applyFromArray([
                            'font'      => ['size' => 7],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        ]);
                    }

                    // Warna row alternating ringan
                    $rowBg = ($h % 2 === 0) ? 'FFFAFAFA' : 'FFFFFFFF';
                    $sheet->getStyle("A{$row}:{$lastColLetter}{$row}")->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setARGB($rowBg);

                    $sheet->getRowDimension($row)->setRowHeight(13);
                }

                $lastDataRow = $startDataRow + 23; // row 32

                // ----------------------------------------------------------------
                // ROW Catatan
                // ----------------------------------------------------------------
                $catatanRow = $lastDataRow + 1;
                // Ambil catatan dari record pertama (per hari)
                $catatanVal = '';
                if ($this->items->isNotEmpty()) {
                    $catatanVal = $this->items->first()->catatan ?? '';
                }

                $sheet->mergeCells("A{$catatanRow}:{$lastColLetter}{$catatanRow}");
                $sheet->setCellValue("A{$catatanRow}", 'Catatan : ' . $catatanVal);
                $sheet->getStyle("A{$catatanRow}")->applyFromArray([
                    'font'      => ['size' => 8],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension($catatanRow)->setRowHeight(14);

                // ----------------------------------------------------------------
                // ROW QT nomor form
                // ----------------------------------------------------------------
                $qtRow = $catatanRow + 1;
                $sheet->mergeCells("A{$qtRow}:{$lastColLetter}{$qtRow}");
                $sheet->setCellValue("A{$qtRow}", 'QT 25/01');
                $sheet->getStyle("A{$qtRow}")->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 8],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                ]);
                $sheet->getRowDimension($qtRow)->setRowHeight(14);

                // ----------------------------------------------------------------
                // ROW blank & Tanda Tangan
                // ----------------------------------------------------------------
                $ttdRow = $qtRow + 4;
                $sheet->mergeCells("A{$ttdRow}:F{$ttdRow}");
                $sheet->setCellValue("A{$ttdRow}", 'Diperiksa Oleh :');
                $sheet->getStyle("A{$ttdRow}")->getFont()->setSize(9)->setBold(true);

                $ttdMidIdx = (int)round(($lastColIdx + 1) / 2);
                $ttdMid    = $col($ttdMidIdx);
                $sheet->mergeCells("{$ttdMid}{$ttdRow}:{$lastColLetter}{$ttdRow}");
                $sheet->setCellValue("{$ttdMid}{$ttdRow}", 'Disetujui Oleh :');
                $sheet->getStyle("{$ttdMid}{$ttdRow}")->getFont()->setSize(9)->setBold(true);

                $ttdLineRow = $ttdRow + 4;
                $sheet->mergeCells("A{$ttdLineRow}:F{$ttdLineRow}");
                $sheet->setCellValue("A{$ttdLineRow}", '( _________________________ )');
                $sheet->getStyle("A{$ttdLineRow}")->applyFromArray([
                    'font'      => ['size' => 9],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                $sheet->mergeCells("{$ttdMid}{$ttdLineRow}:{$lastColLetter}{$ttdLineRow}");
                $sheet->setCellValue("{$ttdMid}{$ttdLineRow}", '( _________________________ )');
                $sheet->getStyle("{$ttdMid}{$ttdLineRow}")->applyFromArray([
                    'font'      => ['size' => 9],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // ----------------------------------------------------------------
                // BORDER – seluruh tabel utama
                // ----------------------------------------------------------------
                $tableRange = "A5:{$lastColLetter}{$lastDataRow}";
                $sheet->getStyle($tableRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color'       => ['argb' => $black],
                        ],
                    ],
                ]);

                // Border outline catatan
                $sheet->getStyle("A{$catatanRow}:{$lastColLetter}{$catatanRow}")->applyFromArray([
                    'borders' => [
                        'outline' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color'       => ['argb' => $black],
                        ],
                    ],
                ]);

                // ----------------------------------------------------------------
                // Lebar kolom
                // ----------------------------------------------------------------
                $sheet->getColumnDimension('A')->setWidth(7);  // Pukul

                foreach ($areas as $i => $area) {
                    $hasRh = ($area->rh_min !== null || $area->rh_max !== null);
                    $sheet->getColumnDimension($col($suhuCols[$i]))->setWidth(7);
                    $sheet->getColumnDimension($col($rhCols[$i]))->setWidth($hasRh ? 7 : 0.1); // sembunyikan jika tidak ada RH
                }

                $sheet->getColumnDimension($col($keteranganColIdx))->setWidth(18);
                $sheet->getColumnDimension($col($qcColIdx))->setWidth(10);
                $sheet->getColumnDimension($col($prodColIdx))->setWidth(10);

                // ----------------------------------------------------------------
                // Print setup
                // ----------------------------------------------------------------
                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->getPageSetup()->setFitToHeight(0);
                $sheet->getPageSetup()->setFitToPage(true);
            },
        ];
    }
}
