<?php

namespace App\Http\Controllers;

use App\Models\PemeriksaanKekuatanMagnetTrap; // Model diubah
use App\Models\List_form; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use TCPDF;


class PemeriksaanKekuatanMagnetTrapController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->user()->isAuditView()) {
            return redirect()->route('pemeriksaan-kekuatan-magnet-trap.audit');
        }

        if ($request->filled('date') && $request->filled('month')) {
            return redirect()->route('pemeriksaan-kekuatan-magnet-trap.index', [
                'date' => $request->date
            ]);
        }

        if ($request->filled('month') && $request->filled('date')) {
            return redirect()->route('pemeriksaan-kekuatan-magnet-trap.index', [
                'month' => $request->month
            ]);
        }

        $query = PemeriksaanKekuatanMagnetTrap::with(['creator', 'updater', 'auditVersion'])
            ->where('plant_uuid', auth()->user()->plant)
            ->where('is_audit', false);

        if ($request->filled('date')) {
            $query->whereDate('tanggal', $request->date);

        } elseif ($request->filled('month')) {
            [$year, $month] = explode('-', $request->month);

            $query->whereYear('tanggal', $year)
                ->whereMonth('tanggal', $month);
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('kondisi_magnet_trap', 'like', "%{$search}%")
                    ->orWhere('petugas_qc', 'like', "%{$search}%")
                    ->orWhereHas('creator', function ($subQuery) use ($search) {
                        $subQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $pemeriksaanKekuatanMagnetTraps = $query->latest()
            ->paginate(15)
            ->withQueryString();

        return view(
            'pemeriksaan-kekuatan-magnet-trap.index',
            compact('pemeriksaanKekuatanMagnetTraps')
        );
    }

    public function auditIndex(Request $request)
    {
        if (!auth()->user()->isAuditView()) {                                 
            return redirect()->route('pemeriksaan-kekuatan-magnet-trap.index');
        }

        $query = PemeriksaanKekuatanMagnetTrap::with(['creator', 'updater'])
            ->where('plant_uuid', auth()->user()->plant)
            ->where(function ($q) {
                $q->where('is_audit', true)
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('is_audit', false)
                            ->whereDoesntHave('auditVersion');
                    });
            });

        if ($request->filled('date')) {
            $query->whereDate('tanggal', $request->date);

        } elseif ($request->filled('month')) {
            [$year, $month] = explode('-', $request->month);

            $query->whereYear('tanggal', $year)
                ->whereMonth('tanggal', $month);
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('kondisi_magnet_trap', 'like', "%{$search}%")
                    ->orWhere('petugas_qc', 'like', "%{$search}%")
                    ->orWhereHas('creator', function ($subQuery) use ($search) {
                        $subQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $pemeriksaanKekuatanMagnetTraps = $query
            ->orderByDesc('tanggal')
            ->orderByDesc('created_at')
            ->paginate(10)
            ->appends($request->all());

        return view(
            'pemeriksaan-kekuatan-magnet-trap.index_audit',
            compact('pemeriksaanKekuatanMagnetTraps')
        );
    }

    
    public function duplicateToAudit($uuid)
    {
        $record = PemeriksaanKekuatanMagnetTrap::where('uuid', $uuid)
            ->where('plant_uuid', auth()->user()->plant)
            ->where('is_audit', false)
            ->firstOrFail();

        $audit = $record->auditVersion;

        if (!$audit) {
            $audit = $record->copyToAudit();
        }

        $this->autoCorrectFromMaster($audit);

        return redirect()->route(
            'pemeriksaan-kekuatan-magnet-trap.edit',
            $audit->id
        );
    }


    public function duplicateToAuditAuto($uuid)
    {
        $record = PemeriksaanKekuatanMagnetTrap::where('uuid', $uuid)
            ->where('plant_uuid', auth()->user()->plant)
            ->where('is_audit', false)
            ->firstOrFail();

        $audit = $record->auditVersion;

        if (!$audit) {
            $audit = $record->copyToAudit();
        }

        $audit = $this->autoCorrectFromMaster($audit);
        $audit->refresh();

        return redirect()->route(
            'pemeriksaan-kekuatan-magnet-trap.edit',
            $audit->id
        );
    }


    public function autoCorrectFromMaster($audit)
    {
        $master = List_form::where('plant', auth()->user()->plant)
            ->where('laporan', 'Pemeriksaan Kekuatan Magnet Trap')
            ->first();

        if (!$master || $master->standar_ref === null || $master->standar_ref === '') {
            return $audit;
        }

        $standarRef = $master->standar_ref;

        if (is_string($standarRef)) {
            $standarRef = json_decode($standarRef, true);

            if (!is_array($standarRef)) {
                return $audit;
            }
        }

        if (!is_array($standarRef)) {
            return $audit;
        }

        for ($i = 1; $i <= 6; $i++) {
            $field = 'kekuatan_median_' . $i;

            $min = data_get($standarRef, "$field.min");
            $max = data_get($standarRef, "$field.max");

            if (
                $min === null || $min === '' ||
                $max === null || $max === ''
            ) {
                continue;
            }

            if (
                !is_numeric($min) ||
                !is_numeric($max) ||
                (float) $min > (float) $max
            ) {
                continue;
            }

            $audit->{$field} = mt_rand(
                (int) round((float) $min * 100),
                (int) round((float) $max * 100)
            ) / 100;
        }

        $parameterSesuai = data_get($standarRef, 'parameter_sesuai');

        // Master null/kosong: jangan update parameter_sesuai.
        if (
            $parameterSesuai !== null &&
            $parameterSesuai !== '' &&
            in_array((string) $parameterSesuai, ['0', '1'], true)
        ) {
            $audit->parameter_sesuai = (int) $parameterSesuai;
        }

        $audit->save();

        return $audit;
    }


    public function createCorrectedAudit($uuid)
    {
        $record = PemeriksaanKekuatanMagnetTrap::where('uuid', $uuid)
            ->where('plant_uuid', auth()->user()->plant)
            ->where('is_audit', false)
            ->firstOrFail();

        $audit = $record->auditVersion;

        if (!$audit) {
            $audit = $record->copyToAudit();
        }

        $this->autoCorrectFromMaster($audit);

        return redirect()->route(
            'pemeriksaan-kekuatan-magnet-trap.edit',
            $audit->id
        );
    }


    public function auditBulkPreview(Request $request)
    {
        $request->validate([
            'start_date' => ['required', 'date'],
            'end_date'   => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        $plant = auth()->user()->plant;

        $master = List_form::where('plant', $plant)
            ->where('laporan', 'Pemeriksaan Kekuatan Magnet Trap')
            ->first();

        if (!$master || $master->standar_ref === null || $master->standar_ref === '') {
            return response()->json([
                'message' => 'Master standar belum tersedia.',
            ], 422);
        }

        $query = PemeriksaanKekuatanMagnetTrap::where('plant_uuid', $plant)
            ->where('is_audit', false)
            ->whereBetween('tanggal', [
                $request->start_date,
                $request->end_date,
            ]);

        $total = (clone $query)->count();

        $sudahAda = (clone $query)
            ->whereHas('auditVersion')
            ->count();

        return response()->json([
            'total'       => $total,
            'sudah_ada'   => $sudahAda,
            'akan_dibuat' => $total - $sudahAda,
        ]);
    }


    public function auditBulkExecute(Request $request)
    {
        $request->validate([
            'start_date' => ['required', 'date'],
            'end_date'   => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        if (!auth()->user()->isAuditView()) {
            abort(403);
        }

        $plant = auth()->user()->plant;

        $master = List_form::where('plant', $plant)
            ->where('laporan', 'Pemeriksaan Kekuatan Magnet Trap')
            ->first();

        if (!$master || $master->standar_ref === null || $master->standar_ref === '') {
            return back()->with('error', 'Master standar belum tersedia.');
        }

        $records = PemeriksaanKekuatanMagnetTrap::where('plant_uuid', $plant)
            ->where('is_audit', false)
            ->whereBetween('tanggal', [
                $request->start_date,
                $request->end_date,
            ])
            ->with('auditVersion')
            ->get();

        $created = 0;
        $updated = 0;

        foreach ($records as $record) {
            $audit = $record->auditVersion;

            if (!$audit) {
                $audit = $record->copyToAudit();
                $created++;
            } else {
                $updated++;
            }

            // Field dengan standar valid diperbarui.
            // Field dengan standar null/kosong tidak di-update.
            $this->autoCorrectFromMaster($audit);
        }

        return redirect()
            ->route('pemeriksaan-kekuatan-magnet-trap.audit')
            ->with(
                'success',
                "Audit selesai. Dibuat: {$created}, diproses ulang: {$updated}."
            );
    }

    public function create()
    {
        // View path diubah
        return view('pemeriksaan-kekuatan-magnet-trap.create');
    }

    /**
     * Menyimpan data baru.
     */
    public function store(Request $request)
    {
        $validatedData = $request->all();
        $validatedData['created_by'] = Auth::user()->uuid;
        $validatedData['parameter_sesuai'] = $request->input('parameter_sesuai', 0) == '1';

        // Model diubah
        PemeriksaanKekuatanMagnetTrap::create($validatedData);

        // Route name diubah
        return redirect()->route('pemeriksaan-kekuatan-magnet-trap.index')
                         ->with('success', 'Pemeriksaan Kekuatan Magnet Trap berhasil dibuat.');
    }

    /**
     * Menampilkan detail data.
     */
    // Variabel & Type-hint diubah
    public function show(PemeriksaanKekuatanMagnetTrap $pemeriksaanKekuatanMagnetTrap)
    {
        // View path & variabel diubah
        return view('pemeriksaan-kekuatan-magnet-trap.show', compact('pemeriksaanKekuatanMagnetTrap'));
    }

    /**
     * Menampilkan form edit.
     */
    // Variabel & Type-hint diubah
    public function edit(PemeriksaanKekuatanMagnetTrap $pemeriksaanKekuatanMagnetTrap)
    {
        // View path & variabel diubah
        return view('pemeriksaan-kekuatan-magnet-trap.edit', compact('pemeriksaanKekuatanMagnetTrap'));
    }

    /**
     * Update data.
     */
    // Variabel & Type-hint diubah
    public function update(Request $request, PemeriksaanKekuatanMagnetTrap $pemeriksaanKekuatanMagnetTrap)
    {
        $validatedData = $request->all();
        $validatedData['parameter_sesuai'] = $request->input('parameter_sesuai', 0) == '1';

        $pemeriksaanKekuatanMagnetTrap->update($validatedData);

        // Route name diubah
        return redirect()->route('pemeriksaan-kekuatan-magnet-trap.index')
                         ->with('success', 'Pemeriksaan Kekuatan Magnet Trap berhasil diperbarui.');
    }

    /**
     * Menghapus data (Soft Delete).
     */
    // Variabel & Type-hint diubah
    public function destroy(PemeriksaanKekuatanMagnetTrap $pemeriksaanKekuatanMagnetTrap)
    {
        $pemeriksaanKekuatanMagnetTrap->delete();
        // Route name diubah
        return redirect()->route('pemeriksaan-kekuatan-magnet-trap.index')
                         ->with('success', 'Pemeriksaan Kekuatan Magnet Trap berhasil dihapus.');
    }

    public function recyclebin()
    {
        $pemeriksaanKekuatanMagnetTrap = PemeriksaanKekuatanMagnetTrap::onlyTrashed()
        ->orderBy('deleted_at', 'desc')
        ->paginate(10);

        return view('pemeriksaan-kekuatan-magnet-trap.recyclebin', compact('pemeriksaanKekuatanMagnetTrap'));
    }
    public function restore($uuid)
    {
        $pemeriksaanKekuatanMagnetTrap = PemeriksaanKekuatanMagnetTrap::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $pemeriksaanKekuatanMagnetTrap->restore();

        return redirect()->route('pemeriksaan-kekuatan-magnet-trap.recyclebin')
        ->with('success', 'Data berhasil direstore.');
    }
    public function deletePermanent($uuid)
    {
        $pemeriksaanKekuatanMagnetTrap = PemeriksaanKekuatanMagnetTrap::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $pemeriksaanKekuatanMagnetTrap->forceDelete();

        return redirect()->route('pemeriksaan-kekuatan-magnet-trap.recyclebin')
        ->with('success', 'Data berhasil dihapus permanen.');
    }

    // --- METODE VERIFIKASI (SPV QC) ---

    /**
     * Menampilkan halaman verifikasi untuk SPV (menampilkan semua data).
     */
    public function verificationSpv(Request $request)
    {
        // Model diubah
        $query = PemeriksaanKekuatanMagnetTrap::query()->latest(); 

        // Filter Tanggal
        if ($request->filled('start_date')) {
            $query->where('tanggal', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('tanggal', '<=', $request->end_date);
        }

        // Filter Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
             $query->where(function ($q) use ($search) {
                $q->Where('kondisi_magnet_trap', 'like', "%{$search}%")
                    ->orWhere('petugas_qc', 'like', "%{$search}%");
            });
        }

        // Variabel diubah
        $pemeriksaanKekuatanMagnetTraps = $query->with('creator', 'verifier')
            ->paginate(15)
            ->appends($request->query());

        // View path & variabel diubah
        return view('pemeriksaan-kekuatan-magnet-trap.verification-spv', compact('pemeriksaanKekuatanMagnetTraps'));
    }

    /**
     * Memproses verifikasi dari SPV.
     */
    // Variabel & Type-hint diubah
    public function verifySpv(Request $request, PemeriksaanKekuatanMagnetTrap $pemeriksaanKekuatanMagnetTrap)
    {
        $validatedData = $request->validate([
            'status_spv' => ['required', Rule::in([1, 2])],
            'catatan_spv' => ['nullable', 'required_if:status_spv,2', 'string'],
        ]);

        try {
            $pemeriksaanKekuatanMagnetTrap->status_spv = $validatedData['status_spv'];
            $pemeriksaanKekuatanMagnetTrap->catatan_spv = $validatedData['catatan_spv'];
            $pemeriksaanKekuatanMagnetTrap->verified_by = Auth::user()->uuid;
            $pemeriksaanKekuatanMagnetTrap->verified_at = now();
            $pemeriksaanKekuatanMagnetTrap->save();

            $message = $validatedData['status_spv'] == 1
                ? 'Data berhasil diverifikasi.'
                : 'Data ditandai untuk revisi.';

            return redirect()->route('pemeriksaan-kekuatan-magnet-trap.index', [
                'page' => $request->input('page', 1),
                'month' => $request->input('month'),
                'date' => $request->input('date'),
                'search' => $request->input('search'),
            ])->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Error verifikasi SPV Magnet Trap: ' . $e->getMessage());

            return redirect()->route('pemeriksaan-kekuatan-magnet-trap.index', [
                'page' => $request->input('page', 1),
                'month' => $request->input('month'),
                'date' => $request->input('date'),
                'search' => $request->input('search'),
            ])->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    public function showUpdateForm(PemeriksaanKekuatanMagnetTrap $pemeriksaanKekuatanMagnetTrap)
    {
        // Menggunakan view baru khusus update
        return view('pemeriksaan-kekuatan-magnet-trap.update_view', compact('pemeriksaanKekuatanMagnetTrap'));
    }

    public function exportPdf(Request $request)
    {
        $month = $request->input('month');
        $userPlant = Auth::user()->plant;

        $noDokumen = List_form::where('plant', $userPlant)
            ->where('laporan', 'Pemeriksaan Kekuatan Magnet Trap')
            ->value('no_dokumen');

        $magnetTraps = PemeriksaanKekuatanMagnetTrap::query()
            ->where('plant_uuid', $userPlant)
            ->when($month, function ($query) use ($month) {
                $query->whereYear('created_at', substr($month, 0, 4))
                    ->whereMonth('created_at', substr($month, 5, 2));
            })
            ->orderBy('created_at', 'asc')
            ->get();

        if (ob_get_length()) {
            ob_end_clean();
        }

        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('CPI');
        $pdf->SetTitle('Verifikasi Magnet Trap');
        $pdf->SetSubject('Verifikasi Magnet Trap');

        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);

        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        $pdf->SetMargins(PDF_MARGIN_LEFT, 10, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
            require_once(dirname(__FILE__) . '/lang/eng.php');
            $pdf->setLanguageArray($l);
        }

        $pdf->SetFont('helvetica', '', 8);

        $pdf->AddPage('L', 'F4');

        $html = view('reports.checkingpowermagnettrap', compact(
            'magnetTraps',
            'request',
            'month',
            'noDokumen'
        ))->render();

        $pdf->writeHTML($html, true, false, true, false, '');

        $pdf->Output('Verifikasi_Magnet_Trap_' . date('Ymd_His') . '.pdf', 'I');

        exit();
    }

}