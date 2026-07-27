<?php
namespace App\Http\Controllers;

use App\Models\MagnetTrapModel;
use App\Models\Mincing;
use App\Models\List_form;
use App\Models\User;
use App\Models\Produksi;
use App\Models\Engineer;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Produk;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Operator;
use App\Exports\MagnetTrapExport;
use Maatwebsite\Excel\Facades\Excel;
use TCPDF;


class MagnetTrapController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Eager load updater untuk performa
        $query = MagnetTrapModel::query()->with(['updater', 'mincing', 'produksi', 'engineer']);

        // 0. Filter Plant (Data Isolation)
        // Menampilkan data hanya sesuai Plant user yang login
        if (Auth::check() && !empty(Auth::user()->plant)) {
            $query->where('plant_uuid', Auth::user()->plant);
        }

        // 1. Filter Pencarian
        $query->when($request->search, function ($q, $search) {
            return $q->where('nama_produk', 'like', "%{$search}%")
            ->orWhere('kode_batch', 'like', "%{$search}%");
        });

        // 2. Filter Tanggal
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->input('date'));
        }

        // 3. Get Data
        $data = $query->latest()->paginate(10)->withQueryString();

        return view('magnet_trap.IndexMagnetTrap', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
   //  public function create()
   //  {
   //     $userPlant = Auth::user()->plant;
   //     $produks = Produk::where('plant', $userPlant)->get();
   //     $produks = Produk::orderBy('nama_produk', 'asc')->get();
   //     $operators = Operator::where('bagian', 'Operator')
   //     ->orderBy('nama_karyawan', 'asc')
   //     ->get();
   //     $engineers = Operator::where('bagian', 'Engineer')
   //     ->orderBy('nama_karyawan', 'asc')->get();
   //     return view('magnet_trap.CreateMagnetTrap ', compact('produks', 'operators', 'engineers'));
   // }

    public function create()
    {
        // Ganti 'magnet_trap.create' jika nama file view Anda berbeda
        $userPlant = Auth::user()->plant;
        $produks = Produk::where('plant', $userPlant)
                         ->orderBy('nama_produk', 'asc')
                         ->get();
        $operators = Operator::where('bagian', 'Operator')
                             ->where('plant', $userPlant)
                             ->orderBy('nama_karyawan', 'asc')
                             ->get();
        $engineers = Operator::where('bagian', 'Engineer')
                         ->where('plant', $userPlant)
                         ->orderBy('nama_karyawan', 'asc')->get();
        return view('magnet_trap.CreateMagnetTrap ', compact('produks', 'operators', 'engineers'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $validatedData = $request->validate([
            'nama_produk' => 'required',
            'kode_batch' => 'required|string', // Max 10 sesuai request
            'pukul' => 'required',
            'jumlah_temuan' => 'required|integer|min:0',
            'status' => 'required|in:v,x',
            'keterangan' => 'nullable|string',
            'produksi_id' => 'required|integer',
            'engineer_id' => 'required|integer',
        ]);

        $user = Auth::user();

        // 1. Generate UUID Record
        $validatedData['uuid'] = (string) Str::uuid();

        // 2. Set User & Plant Info
        $validatedData['created_by'] = $user->uuid;
        $validatedData['updated_by'] = $user->uuid; 
        
        // Ambil plant dari user yg login (pastikan kolom 'plant' ada di tabel users)
        $validatedData['plant_uuid'] = $user->plant; 

        // 3. Simpan
        MagnetTrapModel::create($validatedData);

        return redirect()->route('checklistmagnettrap.index')
        ->with('success', 'Data berhasil ditambahkan.');
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MagnetTrapModel  $checklistmagnettrap
     * @return \Illuminate\Http\Response
     */
    public function show(MagnetTrapModel $checklistmagnettrap)
    {
        return view('magnet_trap.show', compact('checklistmagnettrap'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\MagnetTrapModel  $checklistmagnettrap
     * @return \Illuminate\Http\Response
     */
    // public function edit(MagnetTrapModel $checklistmagnettrap)
    // {
    //     $produks = Produk::orderBy('nama_produk', 'asc')->get();
    //     $operators = Operator::where('bagian', 'Operator')
    //     ->orderBy('nama_karyawan', 'asc')
    //     ->get();
    //     $engineers = Operator::where('bagian', 'Engineer')
    //     ->orderBy('nama_karyawan', 'asc')->get();
    //     return view('magnet_trap.EditMagnetTrap', compact('checklistmagnettrap', 'produks', 'operators', 'engineers'));
    // }

    public function edit(MagnetTrapModel $checklistmagnettrap)
    {
        $userPlant = Auth::user()->plant; 

        $produks = Produk::where('plant', $userPlant)
                         ->orderBy('nama_produk', 'asc')
                         ->get();
        $operators = Operator::where('bagian', 'Operator')
                             ->where('plant', $userPlant)
                             ->orderBy('nama_karyawan', 'asc')
                             ->get();
        $engineers = Operator::where('bagian', 'Engineer')
                         ->where('plant', $userPlant)
                         ->orderBy('nama_karyawan', 'asc')->get();
                         
        $mincings = Mincing::where('uuid', $userPlant)
        ->orderBy('kode_produksi', 'asc')
        ->get();
        return view('magnet_trap.EditMagnetTrap', compact('checklistmagnettrap', 'produks', 'operators', 'engineers', 'mincings'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MagnetTrapModel  $checklistmagnettrap
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MagnetTrapModel $checklistmagnettrap)
    {
       $request->validate([
        'nama_produk' => 'required',
        'kode_batch' => 'required|string',
        'pukul' => 'required',
        'jumlah_temuan' => 'required|integer|min:0',
        'status' => 'required|in:v,x',
        'keterangan' => 'nullable|string',
        'produksi_id' => 'required|integer',
        'engineer_id' => 'required|integer',
    ]);

       $dataToUpdate = $request->all();

        // Update updated_by dengan user yang login sekarang
       $dataToUpdate['updated_by'] = Auth::user()->uuid;

        // Plant UUID biasanya TIDAK diupdate agar history asalnya tetap terjaga
        // Kecuali ada requirement khusus untuk memindahkan data antar plant

       $checklistmagnettrap->update($dataToUpdate);

       return redirect()->route('checklistmagnettrap.index')
       ->with('success', 'Data berhasil diperbarui.');
   }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MagnetTrapModel  $checklistmagnettrap
     * @return \Illuminate\Http\Response
     */
    public function destroy($uuid)
    {
        $magnettrap = MagnetTrapModel::where('uuid', $uuid)->firstOrFail();
        $magnettrap->delete();

        return redirect()->route('checklistmagnettrap.index')
        ->with('success', 'Magnet Trap berhasil dihapus');
    }

    public function recyclebin()
    {
        $magnettrap = MagnetTrapModel::onlyTrashed()
        ->orderBy('deleted_at', 'desc')
        ->paginate(10);

        return view('magnet_trap.recyclebin', compact('magnettrap'));
    }

    public function restore($uuid)
    {
        $magnettrap = MagnetTrapModel::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $magnettrap->restore();

        return redirect()->route('checklistmagnettrap.recyclebin')
        ->with('success', 'Data berhasil direstore.');
    }
    public function deletePermanent($uuid)
    {
        $magnettrap = MagnetTrapModel::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $magnettrap->forceDelete();

        return redirect()->route('checklistmagnettrap.recyclebin')
        ->with('success', 'Data berhasil dihapus permanen.');
    }
    /**
     * Menampilkan halaman verifikasi untuk SPV.
     */
    public function showVerificationPage(Request $request)
    {
        // 1. Mulai query builder
        $query = MagnetTrapModel::query();
        if (Auth::check() && !empty(Auth::user()->plant)) {
            $query->where('plant_uuid', Auth::user()->plant);
        }
        // 2. Terapkan filter pencarian jika ada
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            
            // Ganti 'nama_produk' dan 'kode_batch' dengan nama kolom yang sesuai di database Anda
            $query->where(function($q) use ($searchTerm) {
                $q->where('nama_produk', 'like', '%' . $searchTerm . '%')
                ->orWhere('kode_batch', 'like', '%' . $searchTerm . '%');
                // Tambahkan ->orWhere() lain jika ingin mencari di kolom lain
            });
        }

        // 3. Terapkan filter tanggal awal (start_date) jika ada
        if ($request->filled('start_date')) {
            // Ganti 'created_at' dengan nama kolom tanggal Anda (misal: 'tanggal_inspeksi')
            $query->whereDate('created_at', '>=', $request->input('start_date'));
        }

        // 4. Terapkan filter tanggal akhir (end_date) jika ada
        if ($request->filled('end_date')) {
            // Ganti 'created_at' dengan nama kolom tanggal Anda (misal: 'tanggal_inspeksi')
            $query->whereDate('created_at', '<=', $request->input('end_date'));
        }

        // 5. Eksekusi query dengan urutan terbaru, paginasi, dan tambahkan parameter filter ke link paginasi
        $data = $query->latest() // Mengurutkan dari yang terbaru (sama seperti sebelumnya)
                       ->paginate(10) // Paginasi data
                       ->appends($request->all()); // <-- Ini penting agar filter tetap aktif saat pindah halaman

        // 6. Kirim data ke view
                       return view('magnet_trap.verification', compact('data'));
                   }

    /**
     * Handle the SPV verification update.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $uuid
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verify(Request $request, $uuid)
    {
        $request->validate([
            'status_spv' => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:1000',
        ]);

        // Cari record berdasarkan kolom 'uuid'
        $magnetTrap = MagnetTrapModel::where('uuid', $uuid)->firstOrFail();

        $magnetTrap->status_spv = $request->status_spv;
        
        // Hanya simpan catatan jika statusnya adalah 'Revision', jika tidak, kosongkan.
        $magnetTrap->catatan_spv = ($request->status_spv == 2) ? $request->catatan_spv : null;

        $magnetTrap->verified_by_spv_uuid = Auth::id(); // Asumsi user yang login adalah SPV
        $magnetTrap->verified_at_spv = now();

        $magnetTrap->save();

        return redirect()->back()->with('success', 'Data berhasil diverifikasi.');
    }

    /**
     * Menampilkan view khusus UpdateMagnetTrap.blade.php
     * Logic pengambilan datanya sama dengan edit() biasa.
     */
    public function showUpdateForm(MagnetTrapModel $checklistmagnettrap)
    {
        // Ambil data produk (sama seperti di method edit biasa)
        $userPlant = Auth::user()->plant; 

        $produks = Produk::where('plant', $userPlant)
                         ->orderBy('nama_produk', 'asc')
                         ->get();
        $operators = Operator::where('bagian', 'Operator')
                             ->where('plant', $userPlant)
                             ->orderBy('nama_karyawan', 'asc')
                             ->get();
        $engineers = Operator::where('bagian', 'Engineer')
                         ->where('plant', $userPlant)
                         ->orderBy('nama_karyawan', 'asc')->get();
        // Bedanya DISINI: Arahkan ke view 'UpdateMagnetTrap'
        return view('magnet_trap.UpdateMagnetTrap', compact('checklistmagnettrap', 'produks', 'operators', 'engineers'));
    }

    public function searchBatchMincing(Request $request)
    {
        $search = $request->get('q');
        $userPlant = Auth::user()->plant;

        // Pastikan ada input pencarian
        if($search){
            // Ambil data dari tabel 'mincings' kolom 'kode_produksi'
            // Mengambil 10 data teratas yang mirip agar query ringan
            $data = DB::table('mincings')
                        ->where('plant', $userPlant)
                        ->where('kode_produksi', 'like', '%' . $search . '%')
                        ->limit(10)
                        ->pluck('kode_produksi');

            return response()->json($data);
        }

        return response()->json([]);
    }

    public function exportPdf(Request $request)
    {
        $date = $request->input('date');
        $userPlant = Auth::user()->plant;

        $magnetTraps = MagnetTrapModel::query()
        ->where('plant_uuid', $userPlant)
        ->when($date, function ($query) use ($date) {
            $query->whereDate('created_at', $date);
        })
        ->orderBy('created_at', 'asc')
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
        $pdf->SetTitle('Checklist Cleaning Magnet Trap');
        $pdf->SetSubject('Checklist Cleaning Magnet Trap');

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
        $pdf->SetFont('helvetica', '', 8);

        // Add a page
        $pdf->AddPage('L', 'A3'); // Landscape A3 for many columns

        // Convert the Blade view to HTML
        $html = view('reports.cleaning-magnet-trap', compact('magnetTraps', 'request'))->render();

        // Print text using writeHTMLCell()
        $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

        // Close and output PDF document (Inline/Preview)
        $pdf->Output('Checklist_Cleaning_Magnet_Trap_' . date('Ymd_His') . '.pdf', 'I');

        exit();
    }

    public function exportExcel(Request $request)
    {
        try {
            $search = $request->input('search');
            $date = $request->input('date');
            $userPlant = Auth::user()->plant;

            $data = MagnetTrapModel::where('plant_uuid', $userPlant)
                ->when($search, function ($query) use ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('nama_produk', 'like', "%{$search}%")
                            ->orWhere('kode_batch', 'like', "%{$search}%")
                            ->orWhere('keterangan', 'like', "%{$search}%");
                    });
                })
                ->when($date, function ($query) use ($date) {
                    $query->whereDate('created_at', $date);
                })
                ->orderBy('created_at')
                ->get();

            if ($data->isEmpty()) {
                return back()->with('error', 'Data tidak ditemukan');
            }

            $template = base_path('form retort/FR-QC-61 cleaning magnet trap.xlsx');

            if (!file_exists($template)) {
                return back()->with('error', 'Template tidak ditemukan: ' . $template);
            }

            try {
                $spreadsheet = IOFactory::load($template);
            } catch (\Throwable $e) {
                return back()->with('error', 'Gagal membuka template: ' . $e->getMessage());
            }

            $sheet = $spreadsheet->getActiveSheet();

            $sheet->getStyle($sheet->calculateWorksheetDimension())
                ->getFont()
                ->setName('Times New Roman');

            $sheet->getStyle($sheet->calculateWorksheetDimension())
                ->getAlignment()
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

            $noDokumen = List_form::where('plant', $userPlant)
                ->where('laporan', 'Checklist Cleaning Magnet Trap')
                ->value('no_dokumen');

            $revisi = $noDokumen ? (string) intval(substr($noDokumen, -2)) : '-';

            $sheet->setCellValue('Z2', ': ' . ($noDokumen ?? '-'));
            $sheet->setCellValue('Z3', ': ' . $revisi);

            foreach ($data as $index => $item) {
                if ($index >= 24) {
                    break;
                }

                $row = $index + 10;

                $sheet->setCellValue(
                "C{$row}",
                Mincing::where('uuid', $item->kode_batch)->value('kode_produksi') ?? '-'
            );
                $sheet->setCellValue(
                    "F{$row}",
                    \Carbon\Carbon::parse($item->pukul)->format('H:i')
                );
                $sheet->setCellValue("K{$row}", $item->jumlah_temuan);
                $sheet->getStyle("K{$row}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->setCellValue("N{$row}", $item->keterangan ?: '-');
                $sheet->getStyle("N{$row}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->setCellValue(
                    "S{$row}",
                    User::where('uuid', $item->created_by)->value('username') ?? '-'
                );
                $sheet->setCellValue("W{$row}", optional($item->produksi)->nama_karyawan ?? '-');
                $sheet->setCellValue(
                    "Z{$row}",
                    Operator::find($item->engineer_id)->nama_karyawan ?? '-'
                );
            }

            $verifiedBySpv = User::whereIn(
                'uuid',
                $data->pluck('verified_by_spv_uuid')->filter()->unique()
            )->value('username');

            $sheet->setCellValue('X38', '(' . ($verifiedBySpv ?? '-') . ')');

            $sheet->getStyle('X38')
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);

            $sheet->getStyle('X38')
                ->getFont()
                ->setUnderline(true);

            $sheet->getStyle('Z2:Z3')
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_LEFT)
                ->setVertical(Alignment::VERTICAL_CENTER);

            $filename = 'Checklist_Cleaning_Magnet_Trap_' .
                ($date ? \Carbon\Carbon::parse($date)->format('d-m-Y') : now()->format('d-m-Y')) .
                '.xlsx';

            while (ob_get_level() > 0) {
                ob_end_clean();
            }

            return response()->streamDownload(function () use ($spreadsheet) {
                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                $writer->save('php://output');
            }, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'max-age=0',
                'Pragma' => 'public',
            ]);
        } catch (\Throwable $e) {
            \Log::error($e);

            return back()->with('error', 'Gagal export: ' . $e->getMessage());
        }
    }

}