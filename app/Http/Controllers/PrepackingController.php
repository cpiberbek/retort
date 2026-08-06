<?php

namespace App\Http\Controllers;

use App\Models\Prepacking;
use App\Models\Produk;
use App\Models\Mincing;
use App\Models\List_form;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use TCPDF;

class PrepackingController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $date = $request->input('date');
        $userPlant = Auth::user()->plant;

        $data = Prepacking::query()
            ->where('plant', $userPlant)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
                        ->orWhere('nama_produk', 'like', "%{$search}%")
                        ->orWhere('kode_produksi', 'like', "%{$search}%")
                        ->orWhereExists(function ($sub) use ($search) {
                            $sub->selectRaw(1)
                                ->from('mincings')
                                ->whereColumn('mincings.uuid', 'prepackings.kode_produksi')
                                ->where('mincings.kode_produksi', 'like', "%{$search}%");
                        });
                });
            })
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->all());

        $data->getCollection()->transform(function ($item) {
            $item->kode_produksi = Mincing::where('uuid', $item->kode_produksi)
                ->value('kode_produksi') ?? $item->kode_produksi;

            return $item;
        });

        return view('form.prepacking.index', compact('data', 'search', 'date'));
    }

    public function create()
    {
        $userPlant = Auth::user()->plant;
        $produks = Produk::where('plant', $userPlant)->get();

        return view('form.prepacking.create', compact('produks'));
    }

    public function store(Request $request)
    {
        $username   = Auth::user()->username ?? 'User RTM';
        $userPlant  = Auth::user()->plant;

        $request->validate([
            'date'          => 'required|date',
            'nama_produk'   => 'required',
            'kode_produksi' => 'required|string',
            'conveyor'      => 'nullable|string',
            'berat_produk'  => 'nullable|array',
            'suhu_produk'   => 'nullable|array',
            'kondisi_produk' => 'nullable|array',
            'catatan'       => 'nullable|string',
        ]);

        $data = $request->only([
            'date',
            'nama_produk',
            'kode_produksi',
            'catatan',
            'conveyor',
        ]);

        $data['username']            = $username;
        $data['plant']               = $userPlant;
        $data['status_spv']          = "0";
        $data['berat_produk']        = json_encode($request->input('berat_produk', []), JSON_UNESCAPED_UNICODE);
        $data['suhu_produk']         = json_encode($request->input('suhu_produk', []), JSON_UNESCAPED_UNICODE);
        $data['kondisi_produk']      = json_encode($request->input('kondisi_produk', []), JSON_UNESCAPED_UNICODE);

        Prepacking::create($data);

        return redirect()->route('prepacking.index')->with('success', 'Pengecekan Pre Packing berhasil disimpan');
    }

    public function update(string $uuid)
    {
        $prepacking = Prepacking::where('uuid', $uuid)->firstOrFail();
        $userPlant = Auth::user()->plant;
        $produks = Produk::where('plant', $userPlant)->get();

        $beratData = !empty($prepacking->berat_produk)
            ? json_decode($prepacking->berat_produk, true)
            : [];
        $suhuData = !empty($prepacking->suhu_produk)
            ? json_decode($prepacking->suhu_produk, true)
            : [];
        $kondisiData = !empty($prepacking->kondisi_produk)
            ? json_decode($prepacking->kondisi_produk, true)
            : [];

        return view('form.prepacking.update', compact(
            'prepacking',
            'produks',
            'beratData',
            'suhuData',
            'kondisiData'
        ));
    }

    public function update_qc(Request $request, string $uuid)
    {
        $prepacking = Prepacking::where('uuid', $uuid)->firstOrFail();
        $username_updated = Auth::user()->username ?? 'User QC';

        $request->validate([
            'date'          => 'required|date',
            'nama_produk'   => 'required',
            'kode_produksi' => 'required|string',
            'conveyor'      => 'nullable|string',
            'berat_produk'  => 'nullable|array',
            'suhu_produk'   => 'nullable|array',
            'kondisi_produk' => 'nullable|array',
            'catatan'       => 'nullable|string',
        ]);

        $data = [
            'date'             => $request->date,
            'nama_produk'      => $request->nama_produk,
            'kode_produksi'    => $request->kode_produksi,
            'catatan'          => $request->catatan,
            'username_updated' => $username_updated,
            'conveyor'         => $request->conveyor,
            'berat_produk'     => json_encode($request->input('berat_produk', []), JSON_UNESCAPED_UNICODE),
            'suhu_produk'      => json_encode($request->input('suhu_produk', []), JSON_UNESCAPED_UNICODE),
            'kondisi_produk'   => json_encode($request->input('kondisi_produk', []), JSON_UNESCAPED_UNICODE),
        ];

        $prepacking->update($data);

        return redirect()->route('prepacking.index')->with('success', 'Data Pengecekan Prepacking berhasil diperbarui');
    }

    public function edit(string $uuid)
    {
        $prepacking = Prepacking::where('uuid', $uuid)->firstOrFail();
        $userPlant = Auth::user()->plant;
        $produks = Produk::where('plant', $userPlant)->get();

        $beratData = !empty($prepacking->berat_produk)
            ? json_decode($prepacking->berat_produk, true)
            : [];
        $suhuData = !empty($prepacking->suhu_produk)
            ? json_decode($prepacking->suhu_produk, true)
            : [];
        $kondisiData = !empty($prepacking->kondisi_produk)
            ? json_decode($prepacking->kondisi_produk, true)
            : [];

        return view('form.prepacking.edit', compact('prepacking', 'produks', 'beratData', 'suhuData', 'kondisiData'));
    }

    public function edit_spv(Request $request, string $uuid)
    {
        $prepacking = Prepacking::where('uuid', $uuid)->firstOrFail();

        $request->validate([
            'date'          => 'required|date',
            'nama_produk'   => 'required',
            'kode_produksi' => 'required|string',
            'conveyor'      => 'nullable|string',
            'berat_produk'  => 'nullable|array',
            'suhu_produk'   => 'nullable|array',
            'kondisi_produk' => 'nullable|array',
            'catatan'       => 'nullable|string',
        ]);

        $data = [
            'date'             => $request->date,
            'nama_produk'      => $request->nama_produk,
            'kode_produksi'    => $request->kode_produksi,
            'catatan'          => $request->catatan,
            'conveyor'         => $request->conveyor,
            'berat_produk'     => json_encode($request->input('berat_produk', []), JSON_UNESCAPED_UNICODE),
            'suhu_produk'      => json_encode($request->input('suhu_produk', []), JSON_UNESCAPED_UNICODE),
            'kondisi_produk'   => json_encode($request->input('kondisi_produk', []), JSON_UNESCAPED_UNICODE),
        ];

        $prepacking->update($data);

        return redirect()->route('prepacking.index')->with('success', 'Data Pengecekan Prepacking berhasil diperbarui');
    }

    public function verification(Request $request)
    {
        $search     = $request->input('search');
        $date       = $request->input('date');
        $userPlant  = Auth::user()->plant;

        $data = Prepacking::query()
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

        return view('form.prepacking.index', compact('data', 'search', 'date'));
    }

    public function updateVerification(Request $request, $uuid)
    {
        $request->validate([
            'status_spv'  => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:255',
        ]);

        $prepacking = Prepacking::where('uuid', $uuid)->firstOrFail();

        $prepacking->update([
            'status_spv'      => $request->status_spv,
            'catatan_spv'     => $request->catatan_spv,
            'nama_spv'        => Auth::user()->username,
            'tgl_update_spv'  => now(),
        ]);

        return redirect()->route('prepacking.index')
            ->with('success', 'Status Verifikasi Pengecekan Pre Packing berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $prepacking = Prepacking::where('uuid', $uuid)->firstOrFail();
        $prepacking->delete();
        return redirect()->route('prepacking.index')->with('success', 'Prepacking berhasil dihapus');
    }

    public function recyclebin()
    {
        $prepacking = Prepacking::onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->paginate(10);

        return view('form.prepacking.recyclebin', compact('prepacking'));
    }
    public function restore($uuid)
    {
        $prepacking = Prepacking::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $prepacking->restore();

        return redirect()->route('prepacking.recyclebin')
            ->with('success', 'Data berhasil direstore.');
    }
    public function deletePermanent($uuid)
    {
        $prepacking = Prepacking::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $prepacking->forceDelete();

        return redirect()->route('prepacking.recyclebin')
            ->with('success', 'Data berhasil dihapus permanen.');
    }

    public function exportPdf(Request $request)
    {
        $date = $request->input('date');
        $userPlant = Auth::user()->plant;
        $search = $request->input('search');

        $prepackings = Prepacking::query()
            ->where('plant', $userPlant)
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->when($search, function ($query) use ($search) {
                $uuidMincing = Mincing::where('kode_produksi', 'like', "%{$search}%")
                    ->pluck('uuid')
                    ->toArray();

                $query->where(function ($q) use ($search, $uuidMincing) {
                    $q->where('username', 'like', "%{$search}%")
                        ->orWhere('nama_produk', 'like', "%{$search}%");

                    if (!empty($uuidMincing)) {
                        $q->orWhereIn('kode_produksi', $uuidMincing);
                    } else {
                        $q->orWhere('kode_produksi', 'like', "%{$search}%");
                    }
                });
            })
            ->orderBy('date', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();

        $noDokumen = List_form::where('plant', $userPlant)
            ->where('laporan', 'Pemeriksaan Pre Packing')
            ->value('no_dokumen');

        if (ob_get_length()) {
            ob_end_clean();
        }

        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Your Name/Company');
        $pdf->SetTitle('Pengecekan Pre Packing');
        $pdf->SetSubject('Pengecekan Pre Packing');

        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);

        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        $pdf->SetMargins(PDF_MARGIN_LEFT, 8, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
            require_once(dirname(__FILE__) . '/lang/eng.php');
            $pdf->setLanguageArray($l);
        }

        $pdf->SetFont('helvetica', '', 6);

        $chunks = $prepackings->chunk(6);

        if ($chunks->isEmpty()) {
            $chunks = collect([collect()]);
        }

        foreach ($chunks as $index => $chunk) {
            $pdf->AddPage('L', 'F4');

            $html = view('reports.pengecekan-pre-packing', [
                'prepackings' => $chunk,
                'request' => $request,
                'noDokumen' => $noDokumen,
                'isFirstPage' => $index === 0,
                'isLastPage' => $index === $chunks->count() - 1,
            ])->render();

            $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
        }

        $pdf->Output('Pengecekan_Pre_Packing_' . date('Ymd_His') . '.pdf', 'I');

        exit();
    }

    public function exportExcel(Request $request)
    {
        try {
            $date = $request->input('date');
            $userPlant = Auth::user()->plant;
            $search = $request->input('search');

            $templatePath = app_path('templates/pre_packing.xlsx');

            $spreadsheet = IOFactory::load($templatePath);
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->getStyle($sheet->calculateWorksheetDimension())
                ->getFont()
                ->setName('Times New Roman');

            $prepackings = Prepacking::query()
                ->where('plant', $userPlant)
                ->when($date, function ($query) use ($date) {
                    $query->whereDate('date', $date);
                })
                ->when($search, function ($query) use ($search) {

                    $uuidMincing = Mincing::where('kode_produksi', 'like', "%{$search}%")
                        ->pluck('uuid')
                        ->toArray();

                    $query->where(function ($q) use ($search, $uuidMincing) {

                        $q->where('username', 'like', "%{$search}%")
                            ->orWhere('nama_produk', 'like', "%{$search}%");

                        if (!empty($uuidMincing)) {
                            $q->orWhereIn('kode_produksi', $uuidMincing);
                        }

                    });

                })
                ->orderBy('date', 'asc')
                ->orderBy('created_at', 'asc')
                ->get();

            if ($prepackings->isEmpty()) {
                return back()->with('error', 'Data tidak ditemukan');
            }

            $noDokumen = List_form::where('plant', $userPlant)
                ->where('laporan', 'Pemeriksaan Pre Packing')
                ->value('no_dokumen');

            $row = 10;
            $no = 1;

            foreach ($prepackings as $prepacking) {

                $startRow = $row;
                $endRow = $row + 2;

                $kodeProduksi = Mincing::where('uuid', $prepacking->kode_produksi)
                    ->value('kode_produksi') ?? '-';

                $suhu = is_array($prepacking->suhu_produk)
                    ? $prepacking->suhu_produk
                    : json_decode($prepacking->suhu_produk, true);

                $suhu = is_array($suhu) ? $suhu : [];

                $suhuText = implode(' | ', $suhu);

                $kondisi = is_array($prepacking->kondisi_produk)
                    ? $prepacking->kondisi_produk
                    : json_decode($prepacking->kondisi_produk, true);

                $kondisi = is_array($kondisi) ? $kondisi : [];

                $berat = is_array($prepacking->berat_produk)
                    ? $prepacking->berat_produk
                    : json_decode($prepacking->berat_produk, true);

                $berat = is_array($berat) ? $berat : [];

                $pcs = implode(' | ', [
                    $berat['pcs_1'] ?? 0,
                    $berat['pcs_2'] ?? 0,
                    $berat['pcs_3'] ?? 0
                ]);

                $toples = implode(' | ', [
                    $berat['toples_1'] ?? 0,
                    $berat['toples_2'] ?? 0,
                    $berat['toples_3'] ?? 0
                ]);

                $data = [
                    'airBasahUjung' => $kondisi['basah_air_ujung'] ?? 0,
                    'airKeringUjung' => $kondisi['kering_air_ujung'] ?? 0,
                    'minyakBasahUjung' => $kondisi['basah_minyak_ujung'] ?? 0,
                    'minyakKeringUjung' => $kondisi['kering_minyak_ujung'] ?? 0,

                    'airBasahSeal' => $kondisi['basah_air_seal'] ?? 0,
                    'airKeringSeal' => $kondisi['kering_air_seal'] ?? 0,
                    'minyakBasahSeal' => $kondisi['basah_minyak_seal'] ?? 0,
                    'minyakKeringSeal' => $kondisi['kering_minyak_seal'] ?? 0,
                ];

                $sheet->mergeCells("A{$startRow}:A{$endRow}");
                $sheet->mergeCells("B{$startRow}:B{$endRow}");
                $sheet->mergeCells("C{$startRow}:C{$endRow}");
                $sheet->mergeCells("D{$startRow}:D{$endRow}");
                $sheet->mergeCells("E{$startRow}:E{$endRow}");
                $sheet->mergeCells("M{$startRow}:M{$endRow}");

                $sheet->setCellValue("A{$startRow}", $no++);
                $sheet->setCellValue("B{$startRow}", $prepacking->nama_produk ?? '-');
                $sheet->setCellValue("C{$startRow}", $kodeProduksi);
                $sheet->setCellValue("D{$startRow}", $prepacking->conveyor ?? '-');
                $sheet->setCellValue("E{$startRow}", $suhuText);
                $sheet->setCellValue("M{$startRow}", $prepacking->username ?? '-');

                $sheet->setCellValue("F{$startRow}", 'Ujung');
                $sheet->setCellValue("G{$startRow}", $data['airBasahUjung']);
                $sheet->setCellValue("H{$startRow}", $data['airKeringUjung']);
                $sheet->setCellValue("I{$startRow}", $data['minyakBasahUjung']);
                $sheet->setCellValue("J{$startRow}", $data['minyakKeringUjung']);
                $sheet->setCellValue("K{$startRow}", $pcs);
                $sheet->setCellValue("L{$startRow}", $toples);

                $sheet->setCellValue("F" . ($startRow + 1), 'Seal');
                $sheet->setCellValue("G" . ($startRow + 1), $data['airBasahSeal']);
                $sheet->setCellValue("H" . ($startRow + 1), $data['airKeringSeal']);
                $sheet->setCellValue("I" . ($startRow + 1), $data['minyakBasahSeal']);
                $sheet->setCellValue("J" . ($startRow + 1), $data['minyakKeringSeal']);

                $sheet->setCellValue("F" . ($startRow + 2), 'Total');
                $sheet->setCellValue("G" . ($startRow + 2), $data['airBasahUjung'] + $data['airBasahSeal']);
                $sheet->setCellValue("H" . ($startRow + 2), $data['airKeringUjung'] + $data['airKeringSeal']);
                $sheet->setCellValue("I" . ($startRow + 2), $data['minyakBasahUjung'] + $data['minyakBasahSeal']);
                $sheet->setCellValue("J" . ($startRow + 2), $data['minyakKeringUjung'] + $data['minyakKeringSeal']);

                $sheet->getStyle("A{$startRow}:M{$endRow}")
                    ->getAlignment()
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                    ->setWrapText(true);

                $sheet->getStyle("A{$startRow}:M{$endRow}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                $row += 3;
            }

            $lastRow = $row - 1;

            $footerStart = max(37, $lastRow + 1);

            $sheet->setCellValue("M{$footerStart}", $noDokumen);

            $sheet->getStyle("M{$footerStart}")
                ->getFont()
                ->setItalic(true);

            $sheet->getStyle("M{$footerStart}")
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);


            $catatanRow = $footerStart + 5;

            $catatan = $prepackings->pluck('catatan')->filter()->first();

            $sheet->mergeCells("A{$catatanRow}:E" . ($catatanRow + 2));
            $sheet->setCellValue("A{$catatanRow}", "CATATAN :\n" . ($catatan ?? '-'));

            $sheet->getStyle("A{$catatanRow}:E" . ($catatanRow + 2))
                ->getAlignment()
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP)
                ->setWrapText(true);


            $allApproved = $prepackings->every(fn($item) => !empty($item->nama_spv));

            $namaSpv = $allApproved && $prepackings->isNotEmpty()
                ? $prepackings->first()->nama_spv
                : 'Belum Semua Entry Disetujui Oleh SPV';


            $ttdRow = $footerStart + 8;

            $sheet->mergeCells("M{$ttdRow}:M" . ($ttdRow + 3));

            $sheet->setCellValue(
                "M{$ttdRow}",
                "Disetujui Oleh,\n\n\n(" . $namaSpv . ")\nQC SPV"
            );

            $sheet->getStyle("M{$ttdRow}:M" . ($ttdRow + 3))
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)
                ->setWrapText(true);


            $sheet->getPageSetup()
                ->setFitToWidth(1)
                ->setFitToHeight(0);

            $sheet->getPageMargins()
                ->setTop(0.3)
                ->setBottom(0.3)
                ->setLeft(0.3)
                ->setRight(0.3);

            $filename = 'Pemeriksaan_Pre_Packing_' . ($date ? date('d-m-Y', strtotime($date)) : date('d-m-Y')) . '.xlsx';

            return response()->streamDownload(function () use ($spreadsheet) {

                if (ob_get_length()) {
                    ob_end_clean();
                }

                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');

            }, $filename);

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
    
    public function getBatch($nama_produk)
    {
        $data = DB::table('mincings')
            ->where('nama_produk', $nama_produk)
            ->select('uuid', 'kode_produksi')
            ->get();

        return response()->json($data);
    }
}
