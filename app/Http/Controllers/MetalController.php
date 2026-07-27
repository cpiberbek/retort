<?php

namespace App\Http\Controllers;

use App\Models\Metal;
use App\Models\Operator;
use App\Models\List_form;
use App\Models\Plant;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use TCPDF;
use App\Exports\MetalExport;
use Maatwebsite\Excel\Facades\Excel;

class MetalController extends Controller
{
    public function index(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');
        $userPlant  = Auth::user()->plant;

        $data = Metal::query()
        ->where('plant', $userPlant)
        ->when($search, function ($query) use ($search) {
            $query->where('username', 'like', "%{$search}%");
        })
        ->when($date, function ($query) use ($date) {
            $query->whereDate('date', $date);
        })
        ->orderBy('date', 'desc')
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->appends($request->all());

        return view('form.metal.index', compact('data', 'search', 'date'));
    }

    public function create()
    {
        $userPlant = Auth::user()->plant;

        $engineers = Operator::where('plant', $userPlant)
        ->where('bagian', 'Engineer')
        ->orderBy('nama_karyawan')
        ->get();

        return view('form.metal.create', compact('engineers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date'    => 'required|date',
            'pukul'   => 'required|date_format:H:i',
            'catatan' => 'nullable|string',
            'nama_engineer' => 'required|string',
        ]);

        $username  = Auth::user()->username ?? 'None';
        $userPlant = Auth::user()->plant;
        $nama_produksi = session()->has('selected_produksi')
        ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
        : 'Produksi RTT';

        $data = [
            'date'  => $request->date,
            'pukul' => $request->pukul,
            'fe'    => $request->fe,
            'nfe'   => $request->nfe,
            'sus'   => $request->sus,
            'catatan' => $request->catatan,
            'nama_engineer'       => $request->nama_engineer,
            'status_engineer'     => "1",
            'nama_produksi'       => $nama_produksi,
            'status_produksi'     => "1",
            'tgl_update_produksi' => now()->addHour(),
            'username'            => $username,
            'plant'               => $userPlant,
            'status_spv'          => "0",
        ];

        Metal::create($data);

        return redirect()->route('metal.index')
        ->with('success', 'Pengecekan Metal Detector berhasil disimpan');
    }

    public function update(string $uuid)
    {
        $metal = Metal::where('uuid', $uuid)->firstOrFail();
        $userPlant = Auth::user()->plant;

        $engineers = Operator::where('plant', $userPlant)
        ->where('bagian', 'Engineer')
        ->orderBy('nama_karyawan')
        ->get();

        return view('form.metal.update', compact('metal', 'engineers'));
    }

    public function update_qc(Request $request, string $uuid)
    {
        $metal = Metal::where('uuid', $uuid)->firstOrFail();

        $request->merge([
            'pukul' => substr($request->pukul, 0, 5)
        ]);

        $request->validate([
            'date'    => 'required|date',
            'pukul'   => 'required|date_format:H:i',
            'catatan' => 'nullable|string|max:500',
            'nama_engineer' => 'required|string',
        ]);

        $username_updated = Auth::user()->username ?? 'None';
        $nama_produksi = session()->has('selected_produksi')
        ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
        : 'Produksi RTT';

        $updateData = [
            'date'  => $request->date,
            'pukul' => $request->pukul,
            'fe'    => $request->fe,
            'nfe'   => $request->nfe,
            'sus'   => $request->sus,
            'catatan'          => $request->catatan,
            'username_updated' => $username_updated,
            'nama_engineer'       => $request->nama_engineer,
            'nama_produksi'    => $nama_produksi,
            'tgl_update_produksi' => now()->addHour(),
        ];

        $metal->update($updateData);

        return redirect()->route('metal.index')
        ->with('success', 'Pengecekan Metal Detector berhasil diperbarui');
    }

    public function edit(string $uuid)
    {
        $metal = Metal::where('uuid', $uuid)->firstOrFail();
        $userPlant = Auth::user()->plant;

        $engineers = Operator::where('plant', $userPlant)
        ->where('bagian', 'Engineer')
        ->orderBy('nama_karyawan')
        ->get();

        return view('form.metal.edit', compact('metal', 'engineers'));
    }

    public function edit_spv(Request $request, string $uuid)
    {
        $metal = Metal::where('uuid', $uuid)->firstOrFail();

        $request->merge([
            'pukul' => substr($request->pukul, 0, 5)
        ]);

        $request->validate([
            'date'    => 'required|date',
            'pukul'   => 'required|date_format:H:i',
            'catatan' => 'nullable|string|max:500',
            'nama_engineer' => 'required|string',
        ]);

        $updateData = [
            'date'  => $request->date,
            'pukul' => $request->pukul,
            'fe'    => $request->fe,
            'nfe'   => $request->nfe,
            'sus'   => $request->sus,
            'catatan'          => $request->catatan,
            'nama_engineer'       => $request->nama_engineer,
        ];

        $metal->update($updateData);

        return redirect()->route('metal.index')
        ->with('success', 'Pengecekan Metal Detector berhasil diperbarui');
    }

    public function verification(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');
        $userPlant  = Auth::user()->plant;

        $data = Metal::query()
        ->where('plant', $userPlant)
        ->when($search, function ($query) use ($search) {
            $query->where('username', 'like', "%{$search}%");
        })
        ->when($date, function ($query) use ($date) {
            $query->whereDate('date', $date);
        })
        ->orderBy('date', 'desc')
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->appends($request->all());

        return view('form.metal.index', compact('data', 'search', 'date'));
    }

    public function updateVerification(Request $request, $uuid)
    {
        $request->validate([
            'status_spv'  => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:255',
        ]);

        $metal = Metal::where('uuid', $uuid)->firstOrFail();

        $metal->update([
            'status_spv'  => $request->status_spv,
            'catatan_spv' => $request->catatan_spv,
            'nama_spv'    => Auth::user()->username,
            'tgl_update_spv' => now(),
        ]);

        return redirect()->route('metal.index')
        ->with('success', 'Status Verifikasi Pengecekan Metal Detector berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $metal = Metal::where('uuid', $uuid)->firstOrFail();
        $metal->delete();
        return redirect()->route('metal.index')->with('success', 'Metal Detector berhasil dihapus');
    }

    public function recyclebin()
    {
        $metal = Metal::onlyTrashed()
        ->orderBy('deleted_at', 'desc')
        ->paginate(10);

        return view('form.metal.recyclebin', compact('metal'));
    }
    public function restore($uuid)
    {
        $metal = Metal::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $metal->restore();

        return redirect()->route('metal.recyclebin')
        ->with('success', 'Data berhasil direstore.');
    }
    public function deletePermanent($uuid)
    {
        $metal = Metal::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $metal->forceDelete();

        return redirect()->route('metal.recyclebin')
        ->with('success', 'Data berhasil dihapus permanen.');
    }

    public function exportPdf(Request $request)
    {
        $date = $request->input('date');
        $userPlant = Auth::user()->plant;

        $metals = Metal::query()
        ->where('plant', $userPlant)
        ->when($date, function ($query) use ($date) {
            $query->whereDate('date', $date);
        })
        ->orderBy('date', 'asc')
        ->orderBy('pukul', 'asc')
        ->get();

        // Clear any previous output buffers to prevent "TCPDF ERROR: Some data has already been output"
        if (ob_get_length()) {
            ob_end_clean();
        }

        // Create new TCPDF object
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Your Name/Company');
        $pdf->SetTitle('Pengecekan Metal Detector');
        $pdf->SetSubject('Pengecekan Metal');

        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);

        // Set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // Set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // Set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // Set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // Set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf->setLanguageArray($l);
        }

        // Set font
        $pdf->SetFont('helvetica', '', 10);

        // Add a page
        $pdf->AddPage('P', 'A4');

        $noDokumen = List_form::where('plant', $userPlant)
        ->where('laporan', 'Pemeriksaan Metal Detector')
        ->value('no_dokumen');

        // Convert the Blade view to HTML
        $html = view('reports.metal-detector', compact('metals', 'request', 'noDokumen'))->render();

        // Print text using writeHTMLCell()
        $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

        // Close and output PDF document (Inline/Preview)
        $pdf->Output('Pengecekan_Metal_Detector_' . date('Ymd_His') . '.pdf', 'I');

        exit();
    }

    public function exportExcel(Request $request)
    {
        try {
            $search = $request->input('search');
            $date = $request->input('date');
            $userPlant = Auth::user()->plant;

            $data = Metal::where('plant', $userPlant)
                ->when($search, function ($query) use ($search) {
                    $query->where('username', 'like', "%{$search}%");
                })
                ->when($date, function ($query) use ($date) {
                    $query->whereDate('date', $date);
                })
                ->orderBy('pukul')
                ->get();

            if ($data->isEmpty()) {
                return back()->with('error', 'Data tidak ditemukan');
            }

            $template = base_path('form retort/FM QT 26 Pemeriksaan Metal Detector.xlsx');

            $spreadsheet = IOFactory::load($template);
            $sheet = $spreadsheet->getActiveSheet();

            $plantName = Plant::where('uuid', $userPlant)
                ->value('plant');

            if ($plantName == 'Cikande 2') {
                $sheet->setCellValue('P9', 'SUS 2.0 mm');
            } elseif ($plantName == 'Berbek') {
                $sheet->setCellValue('P9', 'SUS 2.5 mm');
            } else {
                $sheet->setCellValue('P9', 'SUS 2.0 mm');
            }

            $sheet->getStyle($sheet->calculateWorksheetDimension())
                ->getFont()
                ->setName('Times New Roman');

            $sheet->getStyle($sheet->calculateWorksheetDimension())
                ->getAlignment()
                ->setVertical(Alignment::VERTICAL_CENTER);

            if ($date) {
                $sheet->setCellValue(
                    'B7',
                    'Hari / Tanggal : ' . Carbon::parse($date)->format('d-m-Y')
                );
            }

            foreach ($data as $item) {
                $row = 11 + (int) Carbon::parse($item->pukul)->format('H');

                $values = [
                    "B{$row}" => $item->fe === 'Terdeteksi' ? 'V' : '',
                    "I{$row}" => $item->nfe === 'Terdeteksi' ? 'V' : '',
                    "P{$row}" => $item->sus === 'Terdeteksi' ? 'V' : '',
                    "W{$row}" => $item->username ?? '-',
                    "X{$row}" => $item->nama_produksi ?? '-',
                ];

                foreach ($values as $cell => $value) {
                    $sheet->setCellValue($cell, $value);

                    $sheet->getStyle($cell)
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                        ->setVertical(Alignment::VERTICAL_CENTER);
                }
            }

            $namaSpv = $data->pluck('nama_spv')
                ->filter()
                ->unique()
                ->first();

            $sheet->setCellValue('U41', '(' . ($namaSpv ?? '-') . ')');

            $sheet->getStyle('U41')
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);

            $sheet->getStyle('U41')
                ->getFont()
                ->setUnderline(true);

            $noDokumen = List_form::where('plant', $userPlant)
                ->where('laporan', 'Pemeriksaan Metal Detector')
                ->value('no_dokumen');

            $sheet->setCellValue('X35', $noDokumen ?? '-');

            $sheet->getStyle('X35')
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_RIGHT)
                ->setVertical(Alignment::VERTICAL_CENTER);

            $sheet->getStyle('X35')
                ->getFont()
                ->setItalic(true);

            $catatan = $data->pluck('catatan')
                ->filter()
                ->unique()
                ->implode(', ');

            $sheet->setCellValue('B40', $catatan ?: ': -');

            $sheet->getStyle('B40')
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_LEFT)
                ->setVertical(Alignment::VERTICAL_TOP)
                ->setWrapText(true);

            if (!empty($catatan)) {
                $sheet->getStyle('B40')
                    ->getFont()
                    ->setUnderline(true);
            }


            $filename = 'Pengecekan_Metal_Detector_' .
                ($date ? Carbon::parse($date)->format('d-m-Y') : now()->format('d-m-Y')) .
                '.xlsx';

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
