<?php

namespace App\Exports;

use App\Models\Metal;
use Carbon\Carbon;
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

class MetalExport implements FromCollection, WithHeadings, WithMapping, WithEvents, WithCustomStartCell, WithStyles, ShouldAutoSize
{
    protected $data;
    protected $date;
    private $rowNumber = 1;

    public function __construct($data, $date = null)
    {
        $this->data = $data;
        $this->date = $date;
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

                $sheet->mergeCells('A1:H1');
                $sheet->mergeCells('A2:H2');

                $sheet->setCellValue('A1', 'LAPORAN PENGECEKAN METAL DETECTOR');
                $sheet->setCellValue('A2', $this->date ? 'Tanggal: ' . $this->date : 'Semua Tanggal');

                $sheet->getStyle('A1:H1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $sheet->getStyle('A2:H2')->applyFromArray([
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

                $sheet->getStyle('A4:H4')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF4F81BD']],
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
            'Nama Produksi',
            'Nama Engineer',
            'Fe',
            'NFe',
            'SUS',
            'Catatan',
        ];
    }

    public function map($row): array
    {
        return [
            $this->rowNumber++,
            $row->date ? Carbon::parse($row->date)->format('d-m-Y') : '-',
            $row->nama_produksi ?? '-',
            $row->nama_engineer ?? '-',
            $row->fe ?? '-',
            $row->nfe ?? '-',
            $row->sus ?? '-',
            $row->catatan ?? '-',
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
            'A:H' => [
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_TOP,
                    'wrapText' => true,
                ],
            ],
        ];
    }
}