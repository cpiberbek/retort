<?php

namespace App\Exports;

use App\Models\MagnetTrapModel;
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

class MagnetTrapExport implements FromCollection, WithHeadings, WithMapping, WithEvents, WithCustomStartCell, WithStyles, ShouldAutoSize
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

                $sheet->mergeCells('A1:M1');
                $sheet->mergeCells('A2:M2');

                $sheet->setCellValue('A1', 'CHECKLIST CLEANING MAGNET TRAP');
                $sheet->setCellValue('A2', $this->periode ?? 'Periode: Semua Periode');

                $sheet->getStyle('A1:M1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $sheet->getStyle('A2:M2')->applyFromArray([
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

                $sheet->getStyle('A4:M4')->applyFromArray([
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
            'Kode Batch',
            'Nama Produk',
            'Pukul',
            'Jumlah Temuan',
            'Status',
            'Keterangan',
            'Produksi ID',
            'Engineer ID',
            'Status SPV',
            'Catatan SPV',
            'Verified At SPV',
        ];
    }

    public function map($row): array
    {
        return [
            $this->rowNumber++,
            $row->created_at ? Carbon::parse($row->created_at)->format('d-m-Y') : '-',
            $row->mincing->kode_produksi ?? '-', // Use `kode_produksi` from the `mincing` relationship
            $row->nama_produk ?? '-',
            $row->pukul ? Carbon::parse($row->pukul)->format('H:i') : '-',
            $row->jumlah_temuan ?? '-',
            $row->status === 'v' ? 'Valid' : ($row->status === 'x' ? 'Invalid' : '-'),
            $row->keterangan ?? '-',
            $row->produksi_id ?? '-',
            $row->engineer_id ?? '-',
            $row->status_spv == 1 ? 'Verified' : ($row->status_spv == 2 ? 'Revision' : 'Pending'),
            $row->catatan_spv ?? '-',
            $row->verified_at_spv ? Carbon::parse($row->verified_at_spv)->format('d-m-Y H:i') : '-',
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
            'A:M' => [
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_TOP,
                    'wrapText' => true,
                ],
            ],
        ];
    }
}
