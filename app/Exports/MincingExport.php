<?php

namespace App\Exports;

use App\Models\Mincing;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class MincingExport implements FromCollection, WithHeadings, WithMapping, WithEvents, WithCustomStartCell, WithStyles, ShouldAutoSize
{
    protected $data;
    protected $periode;
    private $rowNumber = 1;

    public function __construct($data, $periode = null)
    {
        $this->data = $data;
        $this->periode = $periode;
    }

    public function collection()
    {
        return $this->data;
    }

    public function startCell(): string
    {
        return 'A4';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $sheet->mergeCells('A1:W1');
                $sheet->mergeCells('A2:W2');

                $sheet->setCellValue('A1', 'FORM PEMERIKSAAN MINCING - EMULSIFYING - AGING');
                $sheet->setCellValue('A2', $this->periode ?? 'Periode: Semua Periode');

                $sheet->getStyle('A1:W1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $sheet->getStyle('A2:W2')->applyFromArray([
                    'font' => [
                        'size' => 12,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $sheet->getRowDimension(1)->setRowHeight(24);
                $sheet->getRowDimension(2)->setRowHeight(18);

                $sheet->getStyle('A4:W4')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['argb' => 'FFFFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FF4F81BD'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                ]);
            },
        ];
    }

    public function headings(): array
    {
        return [
            'NO',
            'Tanggal',
            'Shift',
            'Nama Varian',
            'Kode Batch',
            'Waktu Mulai',
            'Waktu Selesai',
            'Bahan Baku (Non-Premix)',
            'Premix',
            'Suhu (Sebelum Grinding)',
            'Waktu Mixing Premix (Menit)',
            'Waktu Bowl Cutter (Menit)',
            'Waktu Aging Emulsi Awal',
            'Waktu Aging Emulsi Akhir',
            'Suhu Akhir Emulsi Gel',
            'Waktu Mixing (Menit)',
            'Suhu Akhir Mixing',
            'Suhu Akhir Emulsifying',
            'QC (Pembuat)',
            'Status QC',
            'SPV (Pemeriksa)',
            'Status SPV',
            'Catatan'
        ];
    }

    public function map($row): array
    {
        $nonPremix = is_array($row->non_premix) ? $row->non_premix : json_decode($row->non_premix ?? '[]', true);
        $np_str = '-';
        if (!empty($nonPremix) && is_array($nonPremix)) {
            $np_str = implode("\n", array_map(function ($n) {
                return '• ' . ($n['nama_bahan'] ?? '-') . ' (' . ($n['berat_bahan'] ?? '0') . ' Kg)';
            }, $nonPremix));
        }

        $premix = is_array($row->premix) ? $row->premix : json_decode($row->premix ?? '[]', true);
        $px_str = '-';
        if (!empty($premix) && is_array($premix)) {
            $px_str = implode("\n", array_map(function ($p) {
                return '• ' . ($p['nama_premix'] ?? '-') . ' (' . ($p['berat_premix'] ?? '0') . ' Kg)';
            }, $premix));
        }

        $suhu = is_array($row->suhu_sebelum_grinding) ? $row->suhu_sebelum_grinding : json_decode($row->suhu_sebelum_grinding ?? '[]', true);
        $sh_str = '-';
        if (!empty($suhu) && is_array($suhu)) {
            $sh_str = implode("\n", array_map(function ($s) {
                return '• ' . ($s['daging'] ?? '?') . ': ' . ($s['suhu'] ?? '-') . '°C';
            }, $suhu));
        }

        return [
            $this->rowNumber++,
            \Carbon\Carbon::parse($row->date)->format('d-m-Y'),
            $row->shift,
            $row->nama_produk,
            $row->kode_produksi,
            $row->waktu_mulai ?? '-',
            $row->waktu_selesai ?? '-',
            $np_str,
            $px_str,
            $sh_str,
            $row->waktu_mixing_premix ?? '-',
            $row->waktu_bowl_cutter ?? '-',
            $row->waktu_aging_emulsi_awal ?? '-',
            $row->waktu_aging_emulsi_akhir ?? '-',
            $row->suhu_akhir_emulsi_gel ?? '-',
            $row->waktu_mixing ?? '-',
            $row->suhu_akhir_mixing ?? '-',
            $row->suhu_akhir_emulsi ?? '-',
            $row->username,
            $row->status_produksi == 1 ? 'Checked' : ($row->status_produksi == 2 ? 'Recheck' : 'Created'),
            $row->nama_spv ?? '-',
            $row->status_spv == 1 ? 'Verified' : ($row->status_spv == 2 ? 'Revision' : 'Created'),
            $row->catatan ?? '-'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            4 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF4F81BD']],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
            ],
            'A:W' => [
                'alignment' => [
                    'wrapText' => true,
                    'vertical' => Alignment::VERTICAL_TOP,
                ],
            ],
        ];
    }
}