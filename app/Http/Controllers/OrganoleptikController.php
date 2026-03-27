<?php

namespace App\Http\Controllers;

use App\Models\Organoleptik;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use TCPDF;

class OrganoleptikController extends Controller
{
    // ========================= INDEX =========================
    public function index(Request $request)
    {
        $search     = $request->input('search');
        $date       = $request->input('date');
        $shift      = $request->input('shift');
        $nama_produk = $request->input('nama_produk');
        $userPlant  = Auth::user()->plant;

        $data = Organoleptik::query()
        ->where('plant', $userPlant)
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                ->orWhere('nama_produk', 'like', "%{$search}%");
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
        
        // $row = $data->first();
        // dd(
        //     $row->organoleptik_detail,
        //     $row->organoleptik_detail->first(),
        //     $row->organoleptik_detail->first()['mincing']
        // );


        // foreach ($data as $row) {
        //     dump(
        //         $row->organoleptik_detail->first()['mincing']
        //     );
        // }

        return view('form.organoleptik.index', compact('data', 'search', 'date', 'shift', 'nama_produk'));
    }

    public function create()
    {
        $userPlant = Auth::user()->plant;
        $produks = Produk::where('plant', $userPlant)->get();

        return view('form.organoleptik.create', compact('produks'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $username  = Auth::user()->username ?? 'User RTM';
        $userPlant = Auth::user()->plant;

        $validated = $request->validate([
            'date'                      => 'required|date',
            'shift'                     => 'required',
            'nama_produk'               => 'required|string',
            'sensori'                   => 'required|array|min:1',
            'sensori.*.kode_produksi'   => 'required|string',
            // --- FIELD SENSORI TAMBAHAN ---
            'sensori.*.penampilan'      => 'nullable|numeric|between:0,3',
            'sensori.*.aroma'           => 'nullable|numeric|between:0,3',
            'sensori.*.kekenyalan'      => 'nullable|numeric|between:0,3',
            'sensori.*.rasa_asin'       => 'nullable|numeric|between:0,3',
            'sensori.*.rasa_gurih'      => 'nullable|numeric|between:0,3',
            'sensori.*.rasa_manis'      => 'nullable|numeric|between:0,3',
            'sensori.*.rasa_daging'     => 'nullable|numeric|between:0,3',
            'sensori.*.rasa_keseluruhan'=> 'nullable|numeric|between:0,3',
            'sensori.*.rata_score'      => 'nullable|numeric',
            'sensori.*.release'         => 'nullable|string',
            // ------------------------------
        ]);

        $sensoris = $validated['sensori']; 
        
        foreach ($sensoris as $index => $item) {

            $sensoris[$index]['kode_produksi']      = $item['kode_produksi'];
            $sensoris[$index]['penampilan']         = $item['penampilan'] ?? null;
            $sensoris[$index]['aroma']              = $item['aroma'] ?? null;
            $sensoris[$index]['kekenyalan']         = $item['kekenyalan'] ?? null;
            $sensoris[$index]['rasa_asin']          = $item['rasa_asin'] ?? null;
            $sensoris[$index]['rasa_gurih']         = $item['rasa_gurih'] ?? null;
            $sensoris[$index]['rasa_manis']         = $item['rasa_manis'] ?? null;
            $sensoris[$index]['rasa_daging']        = $item['rasa_daging'] ?? null;
            $sensoris[$index]['rasa_keseluruhan']   = $item['rasa_keseluruhan'] ?? null;
            $sensoris[$index]['rata_score']         = $item['rata_score'] ?? null;
            $sensoris[$index]['release']            = $item['release'] ?? null;
        }

        unset($item);
        Organoleptik::create([
            'uuid'          => (string) Str::uuid(),
            'date'          => $request->date,
            'shift'         => $request->shift,
            'nama_produk'   => $request->nama_produk,
            'username'      => $username,
            'plant'         => $userPlant,
            'status_spv'    => '0',
            'sensori'       => json_encode($sensoris, JSON_UNESCAPED_UNICODE),
        ]);

        return redirect()->route('organoleptik.index')
        ->with('success', '✅ Pemeriksaan Organoleptik berhasil disimpan.');
    }

    public function update(string $uuid)
    {
        $organoleptik = Organoleptik::where('uuid', $uuid)->firstOrFail();
        $userPlant = Auth::user()->plant;
        $produks = Produk::where('plant', $userPlant)->get();
        $organoleptikData = json_decode($organoleptik->sensori, true) ?? [];

        // Tambahkan kode_produksi_text untuk Select2
        foreach ($organoleptikData as $index => &$data) {
            if (!empty($data['kode_produksi'])) {
                $batch = \App\Models\Mincing::find($data['kode_produksi']);
                $data['kode_produksi_text'] = $batch ? $batch->kode_produksi : '';
            }
        }

        return view('form.organoleptik.update', compact('organoleptik', 'produks', 'organoleptikData'));
    }

    public function update_qc(Request $request, string $uuid)
    {
        // dd($request->all());
        $organoleptik = Organoleptik::where('uuid', $uuid)->firstOrFail();
        $username_updated = Auth::user()->username ?? 'User RTM';

        $validated = $request->validate([
            'date'                      => 'required|date',
            'shift'                     => 'required',
            'nama_produk'               => 'required|string',
            'sensori'                   => 'required|array|min:1',
            'sensori.*.kode_produksi'   => 'required|string',
            // --- FIELD SENSORI TAMBAHAN ---
            'sensori.*.penampilan'      => 'nullable|numeric|between:0,3',
            'sensori.*.aroma'           => 'nullable|numeric|between:0,3',
            'sensori.*.kekenyalan'      => 'nullable|numeric|between:0,3',
            'sensori.*.rasa_asin'       => 'nullable|numeric|between:0,3',
            'sensori.*.rasa_gurih'      => 'nullable|numeric|between:0,3',
            'sensori.*.rasa_manis'      => 'nullable|numeric|between:0,3',
            'sensori.*.rasa_daging'     => 'nullable|numeric|between:0,3',
            'sensori.*.rasa_keseluruhan'=> 'nullable|numeric|between:0,3',
            'sensori.*.rata_score'      => 'nullable|numeric',
            'sensori.*.release'         => 'nullable|string',
            // ------------------------------
        ]);

        $sensoris = $validated['sensori'];

        foreach ($sensoris as $index => &$item) {

            $item = [
                'kode_produksi'     => $item['kode_produksi'],
                'penampilan'        => $item['penampilan'] ?? null,
                'aroma'             => $item['aroma'] ?? null,
                'kekenyalan'        => $item['kekenyalan'] ?? null,
                'rasa_asin'         => $item['rasa_asin'] ?? null,
                'rasa_gurih'        => $item['rasa_gurih'] ?? null,
                'rasa_manis'        => $item['rasa_manis'] ?? null,
                'rasa_daging'       => $item['rasa_daging'] ?? null,
                'rasa_keseluruhan'  => $item['rasa_keseluruhan'] ?? null,
                'rata_score'        => $item['rata_score'] ?? null,
                'release'           => $item['release'] ?? null,
            ];
        }
        unset($item);

        $organoleptik->update([
            'date'             => $request->date,
            'shift'            => $request->shift,
            'nama_produk'      => $request->nama_produk,
            'username_updated' => $username_updated,
            'sensori'          => json_encode($sensoris, JSON_UNESCAPED_UNICODE),
        ]);

        return redirect()->route('organoleptik.index')
        ->with('success', '✅ Pemeriksaan Organoleptik berhasil diperbarui.');
    }

    public function edit(string $uuid)
    {
        $organoleptik = Organoleptik::where('uuid', $uuid)->firstOrFail();
        $userPlant = Auth::user()->plant;
        $produks = Produk::where('plant', $userPlant)->get();
        $organoleptikData = json_decode($organoleptik->sensori, true) ?? [];

        return view('form.organoleptik.edit', compact('organoleptik', 'produks', 'organoleptikData'));
    }

    public function edit_spv(Request $request, string $uuid)
    {
        $organoleptik = Organoleptik::where('uuid', $uuid)->firstOrFail();
        $username_updated = Auth::user()->username ?? 'User RTM';

        $validated = $request->validate([
            'date'                      => 'required|date',
            'shift'                     => 'required',
            'nama_produk'               => 'required|string',
            'sensori'                   => 'required|array|min:1',
            'sensori.*.kode_produksi'   => 'required|string|size:10',
            'sensori.*.penampilan'      => 'nullable|numeric|between:0,3',
            'sensori.*.aroma'           => 'nullable|numeric|between:0,3',
            'sensori.*.kekenyalan'      => 'nullable|numeric|between:0,3',
            'sensori.*.rasa_asin'       => 'nullable|numeric|between:0,3',
            'sensori.*.rasa_gurih'      => 'nullable|numeric|between:0,3',
            'sensori.*.rasa_manis'      => 'nullable|numeric|between:0,3',
            'sensori.*.rasa_daging'     => 'nullable|numeric|between:0,3',
            'sensori.*.rasa_keseluruhan'=> 'nullable|numeric|between:0,3',
            'sensori.*.rata_score'      => 'nullable|numeric',
            'sensori.*.release'         => 'nullable|string',
        ]);

        $sensoris = $validated['sensori'];

        foreach ($sensoris as $index => &$item) {
            $kode = strtoupper(trim($item['kode_produksi']));

            if (!preg_match('/^[A-Z0-9]+$/', $kode)) {
                return back()->withErrors(["sensori.$index.kode_produksi" => "Kode produksi hanya boleh huruf besar dan angka."])->withInput();
            }

            $bulanChar = substr($kode, 1, 1);
            if (!preg_match('/^[A-L]$/', $bulanChar)) {
                return back()->withErrors(["sensori.$index.kode_produksi" => "Karakter ke-2 harus huruf bulan (A–L)."])->withInput();
            }

            $hariStr = substr($kode, 2, 2);
            $hari = intval($hariStr);
            if ($hari < 1 || $hari > 31) {
                return back()->withErrors(["sensori.$index.kode_produksi" => "Karakter ke-3 dan ke-4 harus tanggal valid (01–31)."])->withInput();
            }

            $item = [
                'kode_produksi'     => $kode,
                'penampilan'        => $item['penampilan'] ?? null,
                'aroma'             => $item['aroma'] ?? null,
                'kekenyalan'        => $item['kekenyalan'] ?? null,
                'rasa_asin'         => $item['rasa_asin'] ?? null,
                'rasa_gurih'        => $item['rasa_gurih'] ?? null,
                'rasa_manis'        => $item['rasa_manis'] ?? null,
                'rasa_daging'       => $item['rasa_daging'] ?? null,
                'rasa_keseluruhan'  => $item['rasa_keseluruhan'] ?? null,
                'rata_score'        => $item['rata_score'] ?? null,
                'release'           => $item['release'] ?? null,
            ];
        }
        unset($item);

        $organoleptik->update([
            'date'             => $request->date,
            'shift'            => $request->shift,
            'nama_produk'      => $request->nama_produk,
            'username_updated' => $username_updated,
            'sensori'          => json_encode($sensoris, JSON_UNESCAPED_UNICODE),
        ]);

        return redirect()->route('organoleptik.index')
        ->with('success', '✅ Pemeriksaan Organoleptik berhasil diperbarui.');
    }

    public function verification(Request $request)
    {
        $search     = $request->input('search');
        $start_date = $request->input('start_date');
        $end_date   = $request->input('end_date');
        $userPlant  = Auth::user()->plant;

        $data = Organoleptik::query()
        ->where('plant', $userPlant)
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                ->orWhere('nama_produk', 'like', "%{$search}%");
            });
        })
        ->when($start_date && $end_date, function ($query) use ($start_date, $end_date) {
            $query->whereBetween('date', [$start_date, $end_date]);
        })
        ->orderBy('date', 'desc')
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->appends($request->all());

        return view('form.organoleptik.index', compact('data', 'search', 'start_date', 'end_date'));
    }

    public function updateVerification(Request $request, $uuid)
    {
        $request->validate([
            'status_spv' => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:255',
        ]);

        $organoleptik = Organoleptik::where('uuid', $uuid)->firstOrFail();

        $organoleptik->update([
            'status_spv'  => $request->status_spv,
            'catatan_spv' => $request->catatan_spv,
            'nama_spv'    => Auth::user()->username,
            'tgl_update_spv' => now(),
        ]);

        return redirect()->route('organoleptik.index')
        ->with('success', '✅ Status Verifikasi Pemeriksaan Organoleptik berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $organoleptik = Organoleptik::where('uuid', $uuid)->firstOrFail();
        $organoleptik->delete();
        return redirect()->route('organoleptik.index')->with('success', 'Pemeriksaan Organoleptik berhasil dihapus');
    }

    public function recyclebin()
    {
        $organoleptik = Organoleptik::onlyTrashed()
        ->orderBy('deleted_at', 'desc')
        ->paginate(10);

        return view('form.organoleptik.recyclebin', compact('organoleptik'));
    }
    public function restore($uuid)
    {
        $organoleptik = Organoleptik::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $organoleptik->restore();

        return redirect()->route('organoleptik.recyclebin')
        ->with('success', 'Data berhasil direstore.');
    }
    public function deletePermanent($uuid)
    {
        $organoleptik = Organoleptik::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $organoleptik->forceDelete();

        return redirect()->route('organoleptik.recyclebin')
        ->with('success', 'Data berhasil dihapus permanen.');
    }
    
    public function exportPdf(Request $request)
    {
        $date = $request->input('date');
        $shift = $request->input('shift');
        $nama_produk = $request->input('nama_produk');
        $userPlant = Auth::user()->plant;

        $organoleptiks = Organoleptik::query()
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

        // Create new TCPDF object
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Your Name/Company');
        $pdf->SetTitle('Pemeriksaan Organoleptik');
        $pdf->SetSubject('Pemeriksaan Organoleptik');

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
        $pdf->AddPage('L', 'A4'); // Landscape A4

        // Convert the Blade view to HTML
        $html = view('reports.organoleptik', compact('organoleptiks', 'request'))->render();

        // Print text using writeHTMLCell()
        $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

        // Close and output PDF document (Inline/Preview)
        $pdf->Output('Pemeriksaan_Organoleptik_' . date('Ymd_His') . '.pdf', 'I');

        exit();
    }
}