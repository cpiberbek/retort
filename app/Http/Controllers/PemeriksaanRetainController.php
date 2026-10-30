<?php

namespace App\Http\Controllers;

use App\Models\PemeriksaanRetain;
use App\Models\List_form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Produk;
use Carbon\Carbon;

class PemeriksaanRetainController extends Controller
{
    private const LAPORAN_RETAIN = 'Pemeriksaan Sampel Retain';

    private const AC_RANGE_ITEM = ['panjang', 'diameter', 'lab_garam', 'lab_air', 'lab_mikro'];
    private const AC_SCORE_ITEM = ['sensori_rasa', 'sensori_warna', 'sensori_aroma', 'sensori_texture'];
    private const AC_CHECK_ITEM = ['temuan_jamur', 'temuan_lendir', 'temuan_pinehole', 'temuan_kejepit', 'temuan_seal'];

    public function index(Request $request)
    {
        if (Auth::user()->isAuditView()) {
            return redirect()->route('pemeriksaan_retain.audit');
        }

        $query = PemeriksaanRetain::withCount('items')
            ->with(['creator', 'items'])
            ->where('plant_uuid', Auth::user()->plant)
            ->where('is_audit', false)
            ->latest();

        $query->when($request->date, fn($q) => $q->whereDate('tanggal', $request->date));

        $query->when($request->kode_produksi, function ($q) use ($request) {
            $q->whereHas('items', function ($item) use ($request) {
                $item->where('kode_produksi', 'like', '%' . $request->kode_produksi . '%');
            });
        });

        $pemeriksaanRetains = $query->paginate(15);

        return view('pemeriksaan_retain.index', compact('pemeriksaanRetains'));
    }

    public function auditIndex(Request $request)
    {
        if (!Auth::user()->isAuditView()) {
            return redirect()->route('pemeriksaan_retain.index');
        }

        $auditedSourceUuids = PemeriksaanRetain::where('is_audit', true)
            ->whereNotNull('source_uuid')
            ->pluck('source_uuid')
            ->toArray();

        $query = PemeriksaanRetain::withCount('items')
            ->with(['creator', 'items', 'auditVersion'])
            ->where('plant_uuid', Auth::user()->plant)
            ->where(function ($q) use ($auditedSourceUuids) {
                $q->where('is_audit', true)
                    ->orWhere(function ($sub) use ($auditedSourceUuids) {
                        $sub->where('is_audit', false);
                        if (!empty($auditedSourceUuids)) {
                            $sub->whereNotIn('uuid', $auditedSourceUuids);
                        }
                    });
            });

        $query->when($request->date, fn($q) => $q->whereDate('tanggal', $request->date));

        $query->when($request->kode_produksi, function ($q) use ($request) {
            $q->whereHas('items', function ($item) use ($request) {
                $item->where('kode_produksi', 'like', '%' . $request->kode_produksi . '%');
            });
        });

        $pemeriksaanRetains = $query
            ->orderByRaw('COALESCE((SELECT original.tanggal FROM pemeriksaan_retains AS original WHERE original.uuid = pemeriksaan_retains.source_uuid LIMIT 1), pemeriksaan_retains.tanggal) DESC')
            ->orderByRaw('COALESCE((SELECT original.created_at FROM pemeriksaan_retains AS original WHERE original.uuid = pemeriksaan_retains.source_uuid LIMIT 1), pemeriksaan_retains.created_at) DESC')
            ->paginate(15)
            ->withQueryString();

        return view('pemeriksaan_retain.index_audit', compact('pemeriksaanRetains'));
    }

    public function duplicateToAudit(string $uuid)
    {
        abort_if(Auth::user()->hasRole('auditor'), 403);

        $row = PemeriksaanRetain::where('uuid', $uuid)
            ->where('plant_uuid', Auth::user()->plant)
            ->where('is_audit', false)
            ->firstOrFail();

        if ($existing = $row->auditVersion) {
            return redirect()->route('pemeriksaan_retain.edit', $existing->uuid);
        }

        $clone = $this->createCorrectedAudit($row);

        return redirect()->route('pemeriksaan_retain.edit', $clone->uuid)
            ->with('info', 'Salinan data audit berhasil dibuat. Silakan lakukan penyesuaian.');
    }

    private function createCorrectedAudit(PemeriksaanRetain $row): PemeriksaanRetain
    {
        return DB::transaction(function () use ($row) {
            $clone = $row->copyToAudit();
            $this->autoCorrectFromMaster($clone);
            return $clone;
        });
    }

    private function bulkBase(Request $request): array
    {
        abort_if(Auth::user()->hasRole('auditor'), 403);

        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        $start = Carbon::parse($request->start_date)->toDateString();
        $end   = Carbon::parse($request->end_date)->toDateString();
        $plant = Auth::user()->plant;

        $master = List_form::where('plant', $plant)
            ->where('laporan', self::LAPORAN_RETAIN)
            ->first();

        abort_if(empty($master?->standar_ref), 422, 'Master auto correct belum diterapkan untuk plant ini.');

        $base = PemeriksaanRetain::where('plant_uuid', $plant)
            ->where('is_audit', false)
            ->whereDate('tanggal', '>=', $start)
            ->whereDate('tanggal', '<=', $end);

        return [$base];
    }

    public function auditBulkPreview(Request $request)
    {
        [$base] = $this->bulkBase($request);

        $total = (clone $base)->count();
        $sudah = (clone $base)->whereHas('auditVersion')->count();

        return response()->json([
            'total'       => $total,
            'sudah_ada'   => $sudah,
            'akan_dibuat' => $total - $sudah,
        ]);
    }

    public function auditBulkExecute(Request $request)
    {
        [$base] = $this->bulkBase($request);

        $total = (clone $base)->count();
        if ($total === 0) {
            return redirect()->route('pemeriksaan_retain.audit')->with('warning', 'Tidak ada entry di tanggal ini.');
        }

        $targets = (clone $base)->whereDoesntHave('auditVersion')->get();
        $skipped = $total - $targets->count();
        $created = 0;
        $failed  = 0;

        foreach ($targets as $row) {
            try {
                $this->createCorrectedAudit($row);
                $created++;
            } catch (\Throwable $e) {
                $failed++;
                report($e);
            }
        }

        if ($created === 0 && $failed === 0) {
            return redirect()->route('pemeriksaan_retain.audit')
                ->with('info', "0 data baru. {$skipped} data dilewati karena sudah ada audit.");
        }

        $msg = "{$created} data diduplikasi dan disesuaikan sesuai master. {$skipped} dilewati karena sudah ada.";
        if ($failed) $msg .= " {$failed} gagal diproses (cek log).";

        return redirect()->route('pemeriksaan_retain.audit')->with($failed ? 'warning' : 'success', $msg);
    }

    private function autoCorrectFromMaster(PemeriksaanRetain $model): void
    {
        $master = List_form::where('plant', $model->plant_uuid)
            ->where('laporan', self::LAPORAN_RETAIN)
            ->first();

        $std = $master?->standar_ref;
        if (is_string($std)) $std = json_decode($std, true);
        if (!is_array($std) || empty($std)) return;

        $range = function ($rule) {
            if (!is_array($rule)) return null;
            $min = $rule['min'] ?? null;
            $max = $rule['max'] ?? null;
            if ($min === null || $min === '' || $max === null || $max === '') return null;
            return [(float) $min, (float) $max];
        };

        foreach ($model->items()->get() as $item) {
            foreach (self::AC_RANGE_ITEM as $col) {
                if ($r = $range($std[$col] ?? null)) {
                    $item->$col = round(mt_rand((int) round($r[0] * 100), (int) round($r[1] * 100)) / 100, 2);
                }
            }

            foreach (self::AC_SCORE_ITEM as $col) {
                if ($r = $range($std[$col] ?? null)) {
                    $item->$col = mt_rand((int) $r[0], (int) $r[1]);
                }
            }

            foreach (self::AC_CHECK_ITEM as $col) {
                $rule = $std[$col] ?? null;
                if (in_array((string) $rule, ['0', '1'], true)) {
                    $item->$col = (bool) (int) $rule;
                }
            }

            $item->save();
        }

        $keterangan = $std['keterangan'] ?? null;
        if (is_string($keterangan) && trim($keterangan) !== '') {
            $model->keterangan = trim($keterangan);
            $model->save();
        }
    }

    public function create()
    {
        $userPlant = Auth::user()->plant;
        $produks = Produk::where('plant', $userPlant)->get();
        return view('pemeriksaan_retain.create', compact('produks'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hari' => 'required|string|max:100',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.kode_produksi' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            // Note: 'plant_uuid', 'created_by', 'updated_by' OTOMATIS DIISI OLEH MODEL (booted)
            // Jadi kita cukup kirim data form saja.
            $pemeriksaanRetain = PemeriksaanRetain::create($request->only('hari', 'tanggal', 'keterangan'));

            foreach ($request->items as $itemData) {
                // Mapping data item agar aman
                $itemPayload = [
                    'kode_produksi'   => $itemData['kode_produksi'] ?? null,
                    'exp_date'        => $itemData['exp_date'] ?? null,
                    'varian'          => $itemData['varian'] ?? null,
                    'panjang'         => $itemData['panjang'] ?? null,
                    'diameter'        => $itemData['diameter'] ?? null,
                    'sensori_rasa'    => $itemData['sensori_rasa'] ?? null,
                    'sensori_warna'   => $itemData['sensori_warna'] ?? null,
                    'sensori_aroma'   => $itemData['sensori_aroma'] ?? null,
                    'sensori_texture' => $itemData['sensori_texture'] ?? null,
                    'temuan_jamur'    => isset($itemData['temuan_jamur']),
                    'temuan_lendir'   => isset($itemData['temuan_lendir']),
                    'temuan_pinehole' => isset($itemData['temuan_pinehole']),
                    'temuan_kejepit'  => isset($itemData['temuan_kejepit']),
                    'temuan_seal'     => isset($itemData['temuan_seal']),
                    'lab_garam'       => $itemData['lab_garam'] ?? null,
                    'lab_air'         => $itemData['lab_air'] ?? null,
                    'lab_mikro'       => $itemData['lab_mikro'] ?? null,
                ];

                $pemeriksaanRetain->items()->create($itemPayload);
            }

            DB::commit();

            return redirect()->route('pemeriksaan_retain.index')
                ->with('success', 'Data pemeriksaan retain berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error storing pemeriksaan retain: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(PemeriksaanRetain $pemeriksaanRetain)
    {
        $pemeriksaanRetain->load('items', 'creator', 'verifiedBy', 'updater');
        return view('pemeriksaan_retain.show', compact('pemeriksaanRetain'));
    }

    public function edit(PemeriksaanRetain $pemeriksaanRetain)
    {
        $userPlant = Auth::user()->plant;
        $produks = Produk::where('plant', $userPlant)->get();
        $pemeriksaanRetain->load('items');
        return view('pemeriksaan_retain.edit', compact('pemeriksaanRetain', 'produks'));
    }

    public function update(Request $request, PemeriksaanRetain $pemeriksaanRetain)
    {
        $validator = Validator::make($request->all(), [
            'hari' => 'required|string|max:100',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string',
            'items' => 'required|array|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            // Update data header.
            // 'updated_by' akan otomatis terupdate oleh Model (booted -> updating)
            $pemeriksaanRetain->update($request->only('hari', 'tanggal', 'keterangan'));

            // Strategy: Hapus semua items lama, buat baru (Replacement)
            $pemeriksaanRetain->items()->delete();

            // Create ulang items
            foreach ($request->items as $itemData) {
                $itemPayload = [
                    'kode_produksi'   => $itemData['kode_produksi'] ?? null,
                    'exp_date'        => $itemData['exp_date'] ?? null,
                    'varian'          => $itemData['varian'] ?? null,
                    'panjang'         => $itemData['panjang'] ?? null,
                    'diameter'        => $itemData['diameter'] ?? null,
                    'sensori_rasa'    => $itemData['sensori_rasa'] ?? null,
                    'sensori_warna'   => $itemData['sensori_warna'] ?? null,
                    'sensori_aroma'   => $itemData['sensori_aroma'] ?? null,
                    'sensori_texture' => $itemData['sensori_texture'] ?? null,
                    'temuan_jamur'    => isset($itemData['temuan_jamur']),
                    'temuan_lendir'   => isset($itemData['temuan_lendir']),
                    'temuan_pinehole' => isset($itemData['temuan_pinehole']),
                    'temuan_kejepit'  => isset($itemData['temuan_kejepit']),
                    'temuan_seal'     => isset($itemData['temuan_seal']),
                    'lab_garam'       => $itemData['lab_garam'] ?? null,
                    'lab_air'         => $itemData['lab_air'] ?? null,
                    'lab_mikro'       => $itemData['lab_mikro'] ?? null,
                ];
                $pemeriksaanRetain->items()->create($itemPayload);
            }

            DB::commit();

            return redirect()->route('pemeriksaan_retain.index')
                ->with('success', 'Data pemeriksaan retain berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(PemeriksaanRetain $pemeriksaanRetain)
    {
        try {
            $pemeriksaanRetain->items()->delete();
            $pemeriksaanRetain->delete();
            return redirect()->route('pemeriksaan_retain.index')
                ->with('success', 'Data berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }

    public function recyclebin()
    {
        $pemeriksaanRetain = PemeriksaanRetain::onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->paginate(10);

        return view('pemeriksaan_retain.recyclebin', compact('pemeriksaanRetain'));
    }
    public function restore($uuid)
    {
        $pemeriksaanRetain = PemeriksaanRetain::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $pemeriksaanRetain->restore();

        return redirect()->route('pemeriksaan_retain.recyclebin')
            ->with('success', 'Data berhasil direstore.');
    }
    public function deletePermanent($uuid)
    {
        $pemeriksaanRetain = PemeriksaanRetain::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $pemeriksaanRetain->forceDelete();

        return redirect()->route('pemeriksaan_retain.recyclebin')
            ->with('success', 'Data berhasil dihapus permanen.');
    }
    // --- FITUR VERIFIKASI ---

    public function showVerificationPage(Request $request)
    {
        $baseQuery = PemeriksaanRetain::with('creator', 'items', 'verifiedBy')
            ->latest('tanggal');

        $baseQuery->when($request->start_date, fn($q, $d) => $q->where('tanggal', '>=', $d));
        $baseQuery->when($request->end_date, fn($q, $d) => $q->where('tanggal', '<=', $d));

        $baseQuery->when($request->search, function ($q, $search) {
            $q->where(function ($sub) use ($search) {
                $sub->where('hari', 'like', "%{$search}%")
                    ->orWhereHas('creator', function ($u) use ($search) {
                        $u->where('name', 'like', "%{$search}%");
                    });
            });
        });

        $pemeriksaanRetains = $baseQuery->paginate(20)->withQueryString();
        return view('pemeriksaan_retain.verification', compact('pemeriksaanRetains'));
    }

    public function submitVerification(Request $request, PemeriksaanRetain $pemeriksaanRetain)
    {
        $request->validate([
            'status_spv' => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|required_if:status_spv,2',
        ]);

        try {
            // Gunakan UUID user yang login untuk verified_by
            // Kolom 'updated_by' juga akan otomatis terupdate oleh Model
            $pemeriksaanRetain->update([
                'status_spv'  => $request->status_spv,
                'catatan_spv' => $request->catatan_spv,
                'verified_by' => Auth::user()->uuid,
                'verified_at' => now(),
            ]);

            return redirect()->route('pemeriksaan_retain.index', [
                'page'       => $request->input('page', 1),
                'search'     => $request->input('search'),
                'start_date' => $request->input('start_date'),
                'end_date'   => $request->input('end_date'),
            ])->with('success', 'Data berhasil diverifikasi.');
        } catch (\Exception $e) {
            Log::error('Verifikasi Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal verifikasi.');
        }
    }

    public function editForUpdate(PemeriksaanRetain $pemeriksaanRetain)
    {
        $userPlant = Auth::user()->plant;
        $produks = Produk::where('plant', $userPlant)->get();
        // Load items agar muncul di form
        $pemeriksaanRetain->load('items');

        // Return ke view baru: pemeriksaan_retain.update
        return view('pemeriksaan_retain.update', compact('pemeriksaanRetain', 'produks'));
    }

    public function exportPdf(Request $request)
    {
        $date = $request->input('date');
        $kodeProduksi = $request->input('kode_produksi');
        $userPlant = Auth::user()->plant;

        $query = PemeriksaanRetain::with(['items', 'creator']);

        if (Auth::check() && !empty($userPlant)) {
            $query->where('plant_uuid', $userPlant);
        }

        $query->when($date, function ($q) use ($date) {
            $q->whereDate('tanggal', $date);
        });

        $query->when($kodeProduksi, function ($q) use ($kodeProduksi) {
            $q->whereHas('items', function ($item) use ($kodeProduksi) {
                $item->where('kode_produksi', 'like', '%' . $kodeProduksi . '%');
            });
        });

        $retains = $query->orderBy('tanggal', 'asc')->get();

        $noDokumen = List_form::where('plant', $userPlant)
            ->where('laporan', 'Pemeriksaan Sampel Retain')
            ->value('no_dokumen');

        $perPage = 3;

        $pages = collect();
        $temp = collect();
        $countItem = 0;

        foreach ($retains as $retain) {
            $itemCount = $retain->items ? $retain->items->count() : 0;

            if ($countItem + $itemCount > $perPage && $temp->count() > 0) {
                $pages->push($temp);
                $temp = collect();
                $countItem = 0;
            }

            $temp->push($retain);
            $countItem += $itemCount;
        }

        if ($temp->count() > 0) {
            $pages->push($temp);
        }

        $totalPage = max($pages->count(), 1);

        if (ob_get_length()) {
            ob_end_clean();
        }

        $pdf = new \TCPDF('L', PDF_UNIT, 'F4', true, 'UTF-8', false);

        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle('Pengecekan Retain Sampel');

        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);

        $pdf->SetMargins(5, 5, 5);
        $pdf->SetAutoPageBreak(TRUE, 5);

        $pdf->SetFont('helvetica', '', 6);

        if ($pages->isEmpty()) {
            $pages = collect([collect()]);
            $totalPage = 1;
        }

        foreach ($pages as $pageIndex => $pageData) {

            $pdf->AddPage();

            $html = view('reports.pengecekan-retain-sampel', [
                'retains' => $pageData,
                'request' => $request,
                'noDokumen' => $noDokumen,
                'pageIndex' => $pageIndex,
                'totalPage' => $totalPage,
                'perPage' => $perPage,
                'lastPage' => ($pageIndex == $totalPage - 1),
            ])->render();

            $pdf->writeHTML($html, true, false, true, false, '');
        }

        $filename = 'Pengecekan_Retain_Sampel_' . date('d-m-Y_His') . '.pdf';
        $pdf->Output($filename, 'I');
        exit();
    }
}
