<?php

namespace App\Http\Controllers;

use App\Models\Sampling;
use App\Models\Produk;
use App\Models\List_form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use TCPDF;

class SamplingController extends Controller
{
    public function index(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');
        $shift = $request->input('shift');
        $nama_produk = $request->input('nama_produk');
        $userPlant  = Auth::user()->plant;

        $data = Sampling::query()
            ->where('plant', $userPlant)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
                        ->orWhere('nama_produk', 'like', "%{$search}%")
                        ->orWhere('kode_produksi', 'like', "%{$search}%")
                        ->orWhere('jenis_kemasan', 'like', "%{$search}%");
                });
            })
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->when($shift, function ($query) use ($shift) {
                $query->where('shift', $shift);
            })
            ->when($nama_produk, function ($query) use ($nama_produk) {
                $query->where('nama_produk', $nama_produk);
            })
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->all());

        return view('form.sampling.index', compact('data', 'search', 'date', 'shift', 'nama_produk'));
    }

    public function create()
    {
        $userPlant = Auth::user()->plant;
        $produks = Produk::where('plant', $userPlant)->get();

        return view('form.sampling.create', compact('produks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date'            => 'required|date',
            'shift'           => 'required|string',
            'jenis_sampel'    => 'required|string',
            'jenis_kemasan'   => 'required|string',
            'nama_produk'     => 'required|string',
            'kode_produksi'   => 'required|string',
            'jumlah'          => 'nullable|string',
            'jamur'           => 'nullable|string',
            'lendir'          => 'nullable|string',
            'klip_tajam'      => 'nullable|string',
            'pin_hole'        => 'nullable|string',
            'air_trap_pvdc'   => 'nullable|string',
            'air_trap_produk' => 'nullable|string',
            'keriput'         => 'nullable|string',
            'bengkok'         => 'nullable|string',
            'non_kode'        => 'nullable|string',
            'over_lap'        => 'nullable|string',
            'kecil'           => 'nullable|string',
            'terjepit'        => 'nullable|string',
            'double_klip'     => 'nullable|string',
            'seal_halus'      => 'nullable|string',
            'basah'           => 'nullable|string',
            'dll'             => 'nullable|string',
            'catatan'         => 'nullable|string',
        ]);

        $username = Auth::user()->username ?? 'None';
        $userPlant = Auth::user()->plant;

        $data = [
            'date'            => $request->date,
            'shift'           => $request->shift,
            'jenis_sampel'    => $request->jenis_sampel,
            'jenis_kemasan'   => $request->jenis_kemasan,
            'nama_produk'     => $request->nama_produk,
            'kode_produksi'   => $request->kode_produksi,
            'jumlah'          => $request->jumlah,
            'jamur'           => $request->jamur,
            'lendir'          => $request->lendir,
            'klip_tajam'      => $request->klip_tajam,
            'pin_hole'        => $request->pin_hole,
            'air_trap_pvdc'   => $request->air_trap_pvdc,
            'air_trap_produk' => $request->air_trap_produk,
            'keriput'         => $request->keriput,
            'bengkok'         => $request->bengkok,
            'non_kode'        => $request->non_kode,
            'over_lap'        => $request->over_lap,
            'kecil'           => $request->kecil,
            'terjepit'        => $request->terjepit,
            'double_klip'     => $request->double_klip,
            'seal_halus'      => $request->seal_halus,
            'basah'           => $request->basah,
            'dll'             => $request->dll,
            'catatan'         => $request->catatan,
            'username'        => $username,
            'plant'           => $userPlant,
            'status_spv'      => '0',
        ];

        Sampling::create($data);

        return redirect()->route('sampling.index')
            ->with('success', 'Data Sampling Produk berhasil disimpan.');
    }

    public function update(string $uuid)
    {
        $sampling = Sampling::where('uuid', $uuid)->firstOrFail();
        $userPlant = Auth::user()->plant;
        $produks = Produk::where('plant', $userPlant)->get();

        return view('form.sampling.update', compact('sampling', 'produks'));
    }

    public function update_qc(Request $request, string $uuid)
    {
        $sampling = Sampling::where('uuid', $uuid)->firstOrFail();

        $request->validate([
            'date'            => 'required|date',
            'shift'           => 'required|string',
            'jenis_sampel'    => 'required|string',
            'jenis_kemasan'   => 'required|string',
            'nama_produk'     => 'required|string',
            'kode_produksi'   => 'required|string',
            'jumlah'          => 'nullable|string',
            'jamur'           => 'nullable|string',
            'lendir'          => 'nullable|string',
            'klip_tajam'      => 'nullable|string',
            'pin_hole'        => 'nullable|string',
            'air_trap_pvdc'   => 'nullable|string',
            'air_trap_produk' => 'nullable|string',
            'keriput'         => 'nullable|string',
            'bengkok'         => 'nullable|string',
            'non_kode'        => 'nullable|string',
            'over_lap'        => 'nullable|string',
            'kecil'           => 'nullable|string',
            'terjepit'        => 'nullable|string',
            'double_klip'     => 'nullable|string',
            'seal_halus'      => 'nullable|string',
            'basah'           => 'nullable|string',
            'dll'             => 'nullable|string',
            'catatan'         => 'nullable|string',
        ]);

        $username_updated = Auth::user()->username ?? 'None';

        $fields = [
            'jumlah',
            'jamur',
            'lendir',
            'klip_tajam',
            'pin_hole',
            'air_trap_pvdc',
            'air_trap_produk',
            'keriput',
            'bengkok',
            'non_kode',
            'over_lap',
            'kecil',
            'terjepit',
            'double_klip',
            'seal_halus',
            'basah',
            'dll',
        ];

        $updateData = [
            'date'             => $request->date,
            'shift'            => $request->shift,
            'jenis_sampel'     => $request->jenis_sampel,
            'jenis_kemasan'    => $request->jenis_kemasan,
            'nama_produk'      => $request->nama_produk,
            'kode_produksi'    => $request->kode_produksi,
            'catatan'          => $request->catatan,
            'username_updated' => $username_updated,
        ];

        foreach ($fields as $field) {
            $value = $request->input($field);

            $updateData[$field] = ($value === '') ? null : $value;
        }

        $sampling->update($updateData);

        return redirect()->route('sampling.index')
            ->with('success', 'Data Sampling Produk berhasil diperbarui.');
    }

    public function edit(string $uuid)
    {
        $sampling = Sampling::where('uuid', $uuid)->firstOrFail();
        $userPlant = Auth::user()->plant;
        $produks = Produk::where('plant', $userPlant)->get();

        return view('form.sampling.edit', compact('sampling', 'produks'));
    }

    public function edit_spv(Request $request, string $uuid)
    {
        $sampling = Sampling::where('uuid', $uuid)->firstOrFail();

        $request->validate([
            'date'            => 'required|date',
            'shift'           => 'required|string',
            'jenis_sampel'    => 'required|string',
            'jenis_kemasan'   => 'required|string',
            'nama_produk'     => 'required|string',
            'kode_produksi'   => 'required|string',
            'jumlah'          => 'nullable|string',
            'jamur'           => 'nullable|string',
            'lendir'          => 'nullable|string',
            'klip_tajam'      => 'nullable|string',
            'pin_hole'        => 'nullable|string',
            'air_trap_pvdc'   => 'nullable|string',
            'air_trap_produk' => 'nullable|string',
            'keriput'         => 'nullable|string',
            'bengkok'         => 'nullable|string',
            'non_kode'        => 'nullable|string',
            'over_lap'        => 'nullable|string',
            'kecil'           => 'nullable|string',
            'terjepit'        => 'nullable|string',
            'double_klip'     => 'nullable|string',
            'seal_halus'      => 'nullable|string',
            'basah'           => 'nullable|string',
            'dll'             => 'nullable|string',
            'catatan'         => 'nullable|string',
        ]);

        $username_updated = Auth::user()->username ?? 'None';

        $fields = [
            'jumlah',
            'jamur',
            'lendir',
            'klip_tajam',
            'pin_hole',
            'air_trap_pvdc',
            'air_trap_produk',
            'keriput',
            'bengkok',
            'non_kode',
            'over_lap',
            'kecil',
            'terjepit',
            'double_klip',
            'seal_halus',
            'basah',
            'dll',
        ];

        $updateData = [
            'date'             => $request->date,
            'shift'            => $request->shift,
            'jenis_sampel'     => $request->jenis_sampel,
            'jenis_kemasan'    => $request->jenis_kemasan,
            'nama_produk'      => $request->nama_produk,
            'kode_produksi'    => $request->kode_produksi,
            'catatan'          => $request->catatan,
            'username_updated' => $username_updated,
        ];

        foreach ($fields as $field) {
            $value = $request->input($field);

            $updateData[$field] = ($value === '') ? null : $value;
        }

        $sampling->update($updateData);

        return redirect()->route('sampling.index')
            ->with('success', 'Data Sampling Produk berhasil diperbarui.');
    }
    public function verification(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');
        $userPlant  = Auth::user()->plant;

        $data = Sampling::query()
            ->where('plant', $userPlant)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
                        ->orWhere('nama_produk', 'like', "%{$search}%")
                        ->orWhere('kode_produksi', 'like', "%{$search}%")
                        ->orWhere('jenis_kemasan', 'like', "%{$search}%");
                });
            })
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->all());

        return view('form.sampling.index', compact('data', 'search', 'date'));
    }

    public function updateVerification(Request $request, $uuid)
    {
        $request->validate([
            'status_spv'  => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:255',
        ]);

        $sampling = Sampling::where('uuid', $uuid)->firstOrFail();

        $sampling->update([
            'status_spv'  => $request->status_spv,
            'catatan_spv' => $request->catatan_spv,
            'nama_spv'    => Auth::user()->username,
            'tgl_update_spv' => now(),
        ]);

        return redirect()->route('sampling.index')
            ->with('success', 'Status Verifikasi Data Sampling Produk berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $sampling = Sampling::where('uuid', $uuid)->firstOrFail();
        $sampling->delete();
        return redirect()->route('sampling.index')->with('success', 'Sampling berhasil dihapus');
    }

    public function recyclebin()
    {
        $sampling = Sampling::onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->paginate(10);

        return view('form.sampling.recyclebin', compact('sampling'));
    }
    public function restore($uuid)
    {
        $sampling = Sampling::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $sampling->restore();

        return redirect()->route('sampling.recyclebin')
            ->with('success', 'Data berhasil direstore.');
    }
    public function deletePermanent($uuid)
    {
        $sampling = Sampling::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $sampling->forceDelete();

        return redirect()->route('sampling.recyclebin')
            ->with('success', 'Data berhasil dihapus permanen.');
    }

    public function exportPdf(Request $request)
    {
        $date = $request->input('date');
        $shift = $request->input('shift');
        $nama_produk = $request->input('nama_produk');
        $userPlant = Auth::user()->plant;

        $samplings = Sampling::query()
            ->where('plant', $userPlant)
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->when($shift, function ($query) use ($shift) {
                $query->where('shift', $shift);
            })
            ->when($nama_produk, function ($query) use ($nama_produk) {
                $query->where('nama_produk', $nama_produk);
            })
            ->orderBy('date', 'asc')
            ->orderBy('shift', 'asc')
            ->get();

        // Clear any previous output buffers to prevent "TCPDF ERROR: Some data has already been output"
        if (ob_get_length()) {
            ob_end_clean();
        }

        $noDokumen = List_form::where('plant', $userPlant)
            ->where('laporan', 'Data Sampling Produk')
            ->value('no_dokumen');

        // Create new TCPDF object
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Your Name/Company');
        $pdf->SetTitle('Data Sampling Produk');
        $pdf->SetSubject('Data Sampling Produk');

        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);

        // Set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // Set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, 8, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // Set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // Set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // Set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
            require_once(dirname(__FILE__) . '/lang/eng.php');
            $pdf->setLanguageArray($l);
        }

        // Set font
        $pdf->SetFont('helvetica', '', 8);

        // Add a page
        $pdf->AddPage('L', 'F4'); // Landscape A3 for many columns

        // Convert the Blade view to HTML
        $html = view('reports.data-sampling-produk', compact('samplings', 'request', 'noDokumen'))->render();

        // Print text using writeHTMLCell()
        $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

        // Close and output PDF document (Inline/Preview)
        $pdf->Output('Data_Sampling_Produk_' . date('Ymd_His') . '.pdf', 'I');

        exit();
    }

    public function exportExcel(Request $request)
    {
        try {
            $date = $request->input('date');
            $shift = $request->input('shift');
            $namaProduk = $request->input('nama_produk');
            $userPlant = Auth::user()->plant;

            $templatePath = app_path('templates/data_sampling_produk.xlsx');

            $spreadsheet = IOFactory::load($templatePath);
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->getStyle($sheet->calculateWorksheetDimension())
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

            $filename = 'Sampling_Produk_' .
                \Carbon\Carbon::parse($date)->format('d-m-Y') .
                '_Shift' . $shift . '.xlsx';

            $data = Sampling::where('plant', $userPlant)
                ->when($date, fn($q) => $q->whereDate('date', $date))
                ->when($shift, fn($q) => $q->where('shift', $shift))
                ->when($namaProduk, fn($q) => $q->where('nama_produk', $namaProduk))
                ->get();

            $hari = new RichText();
            $hari->createText('Hari/Tanggal : ');
            $hariValue = $hari->createTextRun(\Carbon\Carbon::parse($date)->format('d-m-Y'));
            $hariValue->getFont()->setUnderline(Font::UNDERLINE_SINGLE);
            $sheet->setCellValue('B6', $hari);

            $shiftText = new RichText();
            $shiftText->createText('Shift : ');
            $shiftValue = $shiftText->createTextRun(!empty($shift) ? $shift : 'Semua Shift');
            $shiftValue->getFont()->setUnderline(Font::UNDERLINE_SINGLE);
            $sheet->setCellValue('D6', $shiftText);

            $row = 10;
            $no = 1;

            foreach ($data as $sampling) {
                $sheet->setCellValue('A' . $row, $no++);
                $sheet->setCellValue('B' . $row, $sampling->jenis_sampel ?? '-');
                $sheet->setCellValue('C' . $row, $sampling->nama_produk ?? '-');
                $sheet->setCellValue('D' . $row, $sampling->kode_produksi ?? '-');
                $sheet->setCellValue(
                    'E' . $row,
                    $sampling->jumlah
                        ? $sampling->jumlah . ' ' . ($sampling->jenis_kemasan ?? '')
                        : '-'
                );
                $sheet->setCellValue('F' . $row, $sampling->jamur ?? '-');
                $sheet->setCellValue('G' . $row, $sampling->lendir ?? '-');
                $sheet->setCellValue('H' . $row, $sampling->klip_tajam ?? '-');
                $sheet->setCellValue('I' . $row, $sampling->pin_hole ?? '-');
                $sheet->setCellValue('J' . $row, $sampling->air_trap_pvdc ?? '-');
                $sheet->setCellValue('K' . $row, $sampling->air_trap_produk ?? '-');
                $sheet->setCellValue('L' . $row, $sampling->keriput ?? '-');
                $sheet->setCellValue('M' . $row, $sampling->bengkok ?? '-');
                $sheet->setCellValue('N' . $row, $sampling->non_kode ?? '-');
                $sheet->setCellValue('O' . $row, $sampling->over_lap ?? '-');
                $sheet->setCellValue('P' . $row, $sampling->kecil ?? '-');
                $sheet->setCellValue('Q' . $row, $sampling->terjepit ?? '-');
                $sheet->setCellValue('R' . $row, $sampling->double_klip ?? '-');
                $sheet->setCellValue('S' . $row, $sampling->seal_halus ?? '-');
                $sheet->setCellValue('T' . $row, $sampling->basah ?? '-');
                $sheet->setCellValue('U' . $row, $sampling->dll ?? '-');
                $sheet->setCellValue('V' . $row, $sampling->username ?? '-');

                if ($row >= 21) {
                    $sheet->getStyle("A{$row}:V{$row}")
                        ->getBorders()
                        ->getAllBorders()
                        ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                }

                $row++;
            }

            $noDokumen = List_form::where('plant', $userPlant)
                ->where('laporan', 'Data Sampling Produk')
                ->value('no_dokumen');

            $targetRow = $row <= 30 ? 30 : $row;

            $sheet->setCellValue('V' . $targetRow, $noDokumen ?? '-');

            $sheet->getStyle('V' . $targetRow)
                ->getFont()
                ->setItalic(true);

            $sheet->getStyle('V' . $targetRow)
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT)
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

            $catatan = $data->pluck('catatan')
                ->filter()
                ->unique()
                ->implode(', ');

            $catatanRow = $row <= 30 ? 32 : $row + 3;

            $sheet->setCellValue('A' . $catatanRow, 'Catatan:');

            $sheet->getStyle('A' . $catatanRow)
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

            $catatanRow++;

            foreach ($data as $sampling) {
                if (!empty($sampling->catatan)) {
                    $sheet->setCellValue(
                        'A' . $catatanRow,
                        ($sampling->kode_produksi ?? '-') . ': ' . $sampling->catatan
                    );

                

                    $sheet->getStyle('A' . $catatanRow)
                        ->getAlignment()
                        ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT)
                        ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                    $catatanRow++;
                }
            }

            $ttdRow = $row <= 30 ? 35 : $row + 5;

            $allApproved = $data->every(fn($item) => !empty($item->nama_spv));

            $namaSpv = $allApproved
                ? $data->pluck('nama_spv')->filter()->unique()->implode(', ')
                : 'Belum Semua Entry Disetujui Oleh SPV';

            $sheet->setCellValue('V' . $ttdRow, 'Disetujui Oleh,');
            $sheet->setCellValue('V' . ($ttdRow + 5), '(' . $namaSpv . ')');
            $sheet->setCellValue('V' . ($ttdRow + 6), 'QC SPV');

            foreach ([$ttdRow, $ttdRow + 5, $ttdRow + 6] as $r) {
                $sheet->getStyle('V' . $r)
                    ->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            }

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
            return back()->with('error', 'Gagal export: ' . $e->getMessage());
        }
    }

}
