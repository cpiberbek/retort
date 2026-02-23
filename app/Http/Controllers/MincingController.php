<?php

namespace App\Http\Controllers;

use App\Models\Mincing;
use App\Models\Produk;
use App\Models\Mesin;
use App\Models\Master_Raw_Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use TCPDF; // Import TCPDF

class MincingController extends Controller 
{
    public function index(Request $request)
    {
     $search    = $request->input('search');
     $date      = $request->input('date');
     $shift     = $request->input('shift');
     $userPlant  = Auth::user()->plant;

     $data = Mincing::query()
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
     ->when($shift, function ($query) use ($shift) {
        $query->where('shift', $shift);
    })
     ->orderBy('date', 'desc')
     ->orderBy('created_at', 'desc')
     ->paginate(10)
     ->appends($request->all());

     return view('form.mincing.index', compact('data', 'search', 'date', 'shift'));
 }

 public function exportPdf(Request $request)
 {
    $search    = $request->input('search');
    $date      = $request->input('date');
    $shift     = $request->input('shift');
    $userPlant = Auth::user()->plant;

    $produks = Mincing::query()
    ->where('plant', $userPlant)
    ->when($search, function ($query) use ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('nama_produk', 'like', "%{$search}%")
            ->orWhere('kode_produksi', 'like', "%{$search}%");
        });
    })
    ->when($date, function ($query) use ($date) {
        $query->whereDate('date', $date);
    })
    ->when($shift, function ($query) use ($shift) {
        $query->where('shift', $shift);
    })
    ->orderBy('date', 'asc')
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
    $pdf->SetTitle('Pemeriksaan Mincing - Emulsifying - Aging');
    $pdf->SetSubject('Pemeriksaan Mincing');

    $pdf->SetPrintHeader(false);
    $pdf->SetPrintFooter(false);

        // // Set default header data
        // $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, 'Pemeriksaan Mincing - Emulsifying - Aging', 'Tanggal: ' . date('d M Y'));

        // // Set header and footer fonts
        // $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        // $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

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
        $pdf->AddPage('L', 'A4'); // Landscape A4

        // Convert the Blade view to HTML
        $html = view('reports.mincing-emulsifying-aging', compact('produks', 'request'))->render();

        // Print text using writeHTMLCell()
        $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

        // Close and output PDF document (Inline/Preview)
        $pdf->Output('Pemeriksaan_Mincing_Emulsifying_Aging_' . date('Ymd_His') . '.pdf', 'I');

        exit();
    }

 public function create()
 {
    $userPlant = Auth::user()->plant;
    $produks = Produk::where('plant', $userPlant)->get();
    $rawMaterials = Master_Raw_Material::where('plant_uuid', $userPlant)->get();
    return view('form.mincing.create', compact('produks', 'rawMaterials'));
}

public function store(Request $request)
{
    $username   = Auth::user()->username ?? 'User RTM';
    $userPlant  = Auth::user()->plant;
    $nama_produksi = session()->has('selected_produksi')
    ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
    : 'Produksi RTT';

    $request->validate([
        'date'          => 'required|date',
        'shift'         => 'required',
        'nama_produk'   => 'required',
        'kode_produksi' => 'required|string',
        'waktu_mulai'   => 'required',
        'waktu_selesai' => 'nullable',
        'premix'        => 'nullable|array',
        'non_premix'    => 'nullable|array',
        'daging'        => 'nullable',
        'suhu_sebelum_grinding'      => 'nullable|numeric',
        'waktu_mixing_premix_awal'   => 'nullable',
        'waktu_mixing_premix_akhir'  => 'nullable',
        'waktu_bowl_cutter_awal'     => 'nullable',
        'waktu_bowl_cutter_akhir'    => 'nullable',
        'waktu_aging_emulsi_awal'    => 'nullable',
        'waktu_aging_emulsi_akhir'   => 'nullable',
        'suhu_akhir_emulsi_gel'      => 'nullable|numeric',
        'waktu_mixing'               => 'nullable',
        'suhu_akhir_mixing'          => 'nullable|numeric',
        'suhu_akhir_emulsi'          => 'nullable|numeric',
        'catatan'                    => 'nullable|string',
    ]);

    $data = $request->only([
        'date', 'shift', 'nama_produk', 'kode_produksi',
        'waktu_mulai', 'waktu_selesai', 'daging', 'suhu_sebelum_grinding',
        'waktu_mixing_premix_awal', 'waktu_mixing_premix_akhir', 'waktu_bowl_cutter_awal', 'waktu_bowl_cutter_akhir', 'waktu_aging_emulsi_awal', 'waktu_aging_emulsi_akhir', 'suhu_akhir_emulsi_gel', 'waktu_mixing', 'suhu_akhir_mixing', 'suhu_akhir_emulsi', 'catatan',
    ]);

    $data['username']            = $username;
    $data['plant']               = $userPlant;
    $data['nama_produksi']       = $nama_produksi;
    $data['status_produksi']     = "1";
    $data['tgl_update_produksi'] = now()->addHour();
    $data['status_spv']          = "0";
    $data['premix']             = json_encode($request->input('premix', []), JSON_UNESCAPED_UNICODE);
    $data['non_premix']             = json_encode($request->input('non_premix', []), JSON_UNESCAPED_UNICODE);

        Mincing::create($data);

        return redirect()->route('mincing.index')->with('success', 'Pengecekan mincing berhasil disimpan');
    }

    public function update(string $uuid)
    {
        $mincing = Mincing::where('uuid', $uuid)->firstOrFail();
        $userPlant = Auth::user()->plant;
        $produks = Produk::where('plant', $userPlant)->get();

    $premixData = !empty($mincing->premix)
    ? json_decode($mincing->premix, true)
    : [];

    $nonPremixData = !empty($mincing->non_premix)
    ? json_decode($mincing->non_premix, true)
    : [];

    return view('form.mincing.update', compact('mincing', 'produks', 'premixData', 'nonPremixData'));
}

    public function update_qc(Request $request, string $uuid)
    {
        $mincing = Mincing::where('uuid', $uuid)->firstOrFail();
        $username_updated = Auth::user()->username ?? 'User QC';

    $request->validate([
        'date'          => 'required|date',
        'shift'         => 'required',
        'nama_produk'   => 'required',
        'kode_produksi' => 'required|string',
        'waktu_mulai'   => 'required',
        'waktu_selesai' => 'nullable',
        'premix'        => 'nullable|array',
        'non_premix'    => 'nullable|array',
        'daging'        => 'nullable',
        'suhu_sebelum_grinding'      => 'nullable|numeric',
        'waktu_mixing_premix_awal'   => 'nullable',
        'waktu_mixing_premix_akhir'  => 'nullable',
        'waktu_bowl_cutter_awal'     => 'nullable',
        'waktu_bowl_cutter_akhir'    => 'nullable',
        'waktu_aging_emulsi_awal'    => 'nullable',
        'waktu_aging_emulsi_akhir'   => 'nullable',
        'suhu_akhir_emulsi_gel'      => 'nullable|numeric',
        'waktu_mixing'               => 'nullable',
        'suhu_akhir_mixing'          => 'nullable|numeric',
        'suhu_akhir_emulsi'          => 'nullable|numeric',
        'catatan'                    => 'nullable|string',
    ]);

    $data = [
        'date'             => $request->date,
        'shift'            => $request->shift,
        'nama_produk'      => $request->nama_produk,
        'kode_produksi'    => $request->kode_produksi,
        'waktu_mulai'      => $request->waktu_mulai,
        'waktu_selesai'    => $request->waktu_selesai,
        'daging'           => $request->daging,
        'suhu_sebelum_grinding'      => $request->suhu_sebelum_grinding,
        'waktu_mixing_premix_awal'   => $request->waktu_mixing_premix_awal,
        'waktu_mixing_premix_akhir'  => $request->waktu_mixing_premix_akhir,
        'waktu_bowl_cutter_awal'     => $request->waktu_bowl_cutter_awal,
        'waktu_bowl_cutter_akhir'    => $request->waktu_bowl_cutter_akhir,
        'waktu_aging_emulsi_awal'    => $request->waktu_aging_emulsi_awal,
        'waktu_aging_emulsi_akhir'   => $request->waktu_aging_emulsi_akhir,
        'suhu_akhir_emulsi_gel'      => $request->suhu_akhir_emulsi_gel,
        'waktu_mixing'               => $request->waktu_mixing,
        'suhu_akhir_mixing'          => $request->suhu_akhir_mixing,
        'suhu_akhir_emulsi'          => $request->suhu_akhir_emulsi,
        'catatan'                    => $request->catatan,
        'username_updated'           => $username_updated,
        'premix'                     => json_encode($request->input('premix', []), JSON_UNESCAPED_UNICODE),
        'non_premix'                 => json_encode($request->input('non_premix', []), JSON_UNESCAPED_UNICODE),
    ];

        $mincing->update($data);

        return redirect()->route('mincing.index')->with('success', 'Data QC berhasil diperbarui');
    }

    public function edit(string $uuid)
    {
        $mincing = Mincing::where('uuid', $uuid)->firstOrFail();
        $userPlant = Auth::user()->plant;
        $produks = Produk::where('plant', $userPlant)->get();

    $premixData = !empty($mincing->premix)
    ? json_decode($mincing->premix, true)
    : [];

    $nonPremixData = !empty($mincing->non_premix)
    ? json_decode($mincing->non_premix, true)
    : [];

    return view('form.mincing.edit', compact('mincing', 'produks', 'premixData', 'nonPremixData'));
}

public function edit_spv(Request $request, string $uuid)
{
    $mincing = Mincing::where('uuid', $uuid)->firstOrFail();
    $request->validate([
        'date'          => 'required|date',
        'shift'         => 'required',
        'nama_produk'   => 'required',
        'kode_produksi' => 'required|string',
        'waktu_mulai'   => 'required',
        'waktu_selesai' => 'nullable',
        'premix'        => 'nullable|array',
        'non_premix'    => 'nullable|array',
        'daging'        => 'nullable',
        'suhu_sebelum_grinding'      => 'nullable|numeric',
        'waktu_mixing_premix_awal'   => 'nullable',
        'waktu_mixing_premix_akhir'  => 'nullable',
        'waktu_bowl_cutter_awal'     => 'nullable',
        'waktu_bowl_cutter_akhir'    => 'nullable',
        'waktu_aging_emulsi_awal'    => 'nullable',
        'waktu_aging_emulsi_akhir'   => 'nullable',
        'suhu_akhir_emulsi_gel'      => 'nullable|numeric',
        'waktu_mixing'               => 'nullable',
        'suhu_akhir_mixing'          => 'nullable|numeric',
        'suhu_akhir_emulsi'          => 'nullable|numeric',
        'catatan'                    => 'nullable|string',
    ]);

    $data = [
        'date'             => $request->date,
        'shift'            => $request->shift,
        'nama_produk'      => $request->nama_produk,
        'kode_produksi'    => $request->kode_produksi,
        'waktu_mulai'      => $request->waktu_mulai,
        'waktu_selesai'    => $request->waktu_selesai,
        'daging'           => $request->daging,
        'suhu_sebelum_grinding'      => $request->suhu_sebelum_grinding,
        'waktu_mixing_premix_awal'   => $request->waktu_mixing_premix_awal,
        'waktu_mixing_premix_akhir'  => $request->waktu_mixing_premix_akhir,
        'waktu_bowl_cutter_awal'     => $request->waktu_bowl_cutter_awal,
        'waktu_bowl_cutter_akhir'    => $request->waktu_bowl_cutter_akhir,
        'waktu_aging_emulsi_awal'    => $request->waktu_aging_emulsi_awal,
        'waktu_aging_emulsi_akhir'   => $request->waktu_aging_emulsi_akhir,
        'suhu_akhir_emulsi_gel'      => $request->suhu_akhir_emulsi_gel,
        'waktu_mixing'               => $request->waktu_mixing,
        'suhu_akhir_mixing'          => $request->suhu_akhir_mixing,
        'suhu_akhir_emulsi'          => $request->suhu_akhir_emulsi,
        'catatan'                    => $request->catatan,
        'premix'                     => json_encode($request->input('premix', []), JSON_UNESCAPED_UNICODE),
        'non_premix'                 => json_encode($request->input('non_premix', []), JSON_UNESCAPED_UNICODE),
    ];
    $mincing->update($data);

    return redirect()->route('mincing.verification')->with('success', 'Data SPV berhasil diperbarui');
}

    public function verification(Request $request)
    {
        $search     = $request->input('search');
        $date       = $request->input('date');
        $userPlant  = Auth::user()->plant;

        $data = Mincing::query()
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

    return view('form.mincing.verification', compact('data', 'search', 'date'));
}

    public function updateVerification(Request $request, $uuid)
    {
        $request->validate([
            'status_spv'  => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:255',
        ]);

        $mincing = Mincing::where('uuid', $uuid)->firstOrFail();

        $mincing->update([
            'status_spv'      => $request->status_spv,
            'catatan_spv'     => $request->catatan_spv,
            'nama_spv'        => Auth::user()->username,
            'tgl_update_spv'  => now(),
        ]);

    return redirect()->route('mincing.verification')
    ->with('success', 'Status Verifikasi Pengecekan mincing berhasil diperbarui.');
}

    public function destroy($uuid)
    {
        $mincing = Mincing::where('uuid', $uuid)->firstOrFail();
        $mincing->delete();
        return redirect()->route('mincing.index')->with('success', 'Mincing berhasil dihapus');
    }

    public function recyclebin()
    {
        $mincing = Mincing::onlyTrashed()
        ->orderBy('deleted_at', 'desc')
        ->paginate(10);

        return view('form.mincing.recyclebin', compact('mincing'));
    }
    public function restore($uuid)
    {
        $mincing = Mincing::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $mincing->restore();

        return redirect()->route('mincing.recyclebin')
        ->with('success', 'Data berhasil direstore.');
    }
    public function deletePermanent($uuid)
    {
        $mincing = Mincing::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $mincing->forceDelete();

        return redirect()->route('mincing.recyclebin')
        ->with('success', 'Data berhasil dihapus permanen.');
    }
}
