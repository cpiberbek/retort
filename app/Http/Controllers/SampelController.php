<?php

namespace App\Http\Controllers;

use App\Models\Sampel;
use App\Models\Produk;
use App\Models\Mincing;
use App\Models\List_form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SampelController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->isAuditView()) {
            return redirect()->route('sampel.audit');
        }

        $search = $request->input('search');
        $date = $request->input('date');
        $userPlant = Auth::user()->plant;

        $data = Sampel::query()
            ->where('plant', $userPlant)
            ->where('is_audit', false)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
                        ->orWhere('nama_produk', 'like', "%{$search}%")
                        ->orWhere('kode_produksi', 'like', "%{$search}%")
                        ->orWhere('jenis_sampel', 'like', "%{$search}%")
                        ->orWhereExists(function ($sub) use ($search) {
                            $sub->selectRaw(1)
                                ->from('mincings')
                                ->whereColumn('mincings.uuid', 'sampels.kode_produksi')
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

        return view('form.sampel.index', compact('data', 'search', 'date'));
    }

    public function auditIndex(Request $request)
    {
        if (!Auth::user()->isAuditView()) {
            return redirect()->route('sampel.index');
        }

        $search = $request->input('search');
        $date   = $request->input('date');

        $auditedSourceUuids = Sampel::where('is_audit', true)
            ->whereNotNull('source_uuid')
            ->pluck('source_uuid')
            ->toArray();

        $data = Sampel::query()
            ->where('plant', Auth::user()->plant)
            ->with('auditVersion')
            ->where(function ($q) use ($auditedSourceUuids) {
                $q->where('is_audit', true)
                    ->orWhere(function ($sub) use ($auditedSourceUuids) {
                        $sub->where('is_audit', false);
                        if (!empty($auditedSourceUuids)) {
                            $sub->whereNotIn('uuid', $auditedSourceUuids);
                        }
                    });
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
                        ->orWhere('nama_produk', 'like', "%{$search}%")
                        ->orWhere('kode_produksi', 'like', "%{$search}%")
                        ->orWhere('jenis_sampel', 'like', "%{$search}%")
                        ->orWhereExists(function ($sub) use ($search) {
                            $sub->selectRaw(1)
                                ->from('mincings')
                                ->whereColumn('mincings.uuid', 'sampels.kode_produksi')
                                ->where('mincings.kode_produksi', 'like', "%{$search}%");
                        });
                });
            })
            ->when($date, fn ($q) => $q->whereDate('date', $date))
            ->orderByRaw('COALESCE((SELECT original.date FROM sampels AS original WHERE original.uuid = sampels.source_uuid LIMIT 1), sampels.date) DESC')
            ->orderByRaw('COALESCE((SELECT original.created_at FROM sampels AS original WHERE original.uuid = sampels.source_uuid LIMIT 1), sampels.created_at) DESC')
            ->paginate(10)
            ->appends($request->all());

        $kodeMap = Mincing::whereIn('uuid', $data->pluck('kode_produksi')->filter()->unique())
            ->pluck('kode_produksi', 'uuid');

        return view('form.sampel.index_audit', compact('data', 'search', 'date', 'kodeMap'));
    }

    public function duplicateToAudit(string $uuid)
    {
        abort_if(Auth::user()->hasRole('auditor'), 403);

        $row = Sampel::where('uuid', $uuid)
            ->where('plant', Auth::user()->plant)
            ->where('is_audit', false)
            ->firstOrFail();

        if ($existing = $row->auditVersion) {
            return redirect()->route('sampel.edit.form', $existing->uuid);
        }

        $clone = DB::transaction(fn () => $row->copyToAudit());

        return redirect()->route('sampel.edit.form', $clone->uuid)
            ->with('info', 'Salinan data audit berhasil dibuat. Silakan lakukan penyesuaian.');
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

        $base = Sampel::where('plant', Auth::user()->plant)
            ->where('is_audit', false)
            ->whereDate('date', '>=', $start)
            ->whereDate('date', '<=', $end);

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
            return redirect()->route('sampel.audit')->with('warning', 'Tidak ada entry di tanggal ini.');
        }

        $targets = (clone $base)->whereDoesntHave('auditVersion')->get();
        $skipped = $total - $targets->count();
        $created = 0;
        $failed  = 0;

        foreach ($targets as $row) {
            try {
                DB::transaction(fn () => $row->copyToAudit());
                $created++;
            } catch (\Throwable $e) {
                $failed++;
                report($e);
            }
        }

        if ($created === 0 && $failed === 0) {
            return redirect()->route('sampel.audit')
                ->with('info', "0 data baru. {$skipped} data dilewati karena sudah ada audit.");
        }

        $msg = "{$created} data diduplikasi. {$skipped} dilewati karena sudah ada.";
        if ($failed) $msg .= " {$failed} gagal diproses (cek log).";

        return redirect()->route('sampel.audit')->with($failed ? 'warning' : 'success', $msg);
    }

    public function create()
    {
        $userPlant = Auth::user()->plant;
        $produks = Produk::where('plant', $userPlant)->get();

        return view('form.sampel.create', compact('produks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date'            => 'required|date',
            'nama_produk'     => 'required|string|max:255',
            'kode_produksi'   => 'required|string',
            'keterangan'      => 'nullable|string',
            'jenis_sampel'    => 'nullable|string|max:255',
            'jenis_sampel_select' => 'nullable|string|max:255',
        ]);

        $jenisSampel = $request->jenis_sampel ?: $request->jenis_sampel_select;
        if (!$jenisSampel) {
            return back()->withErrors(['jenis_sampel' => 'Jenis sampel wajib diisi.'])->withInput();
        }

        $username  = Auth::user()->username ?? 'None';
        $userPlant = Auth::user()->plant;

        $data = [
            'date'            => $request->date,
            'jenis_sampel'    => $jenisSampel,
            'nama_produk'     => $request->nama_produk,
            'kode_produksi'   => $request->kode_produksi,
            'keterangan'      => $request->keterangan,
            'username'        => $username,
            'plant'           => $userPlant,
            'status_spv'      => "0",
        ];

        Sampel::create($data);

        return redirect()->route('sampel.index')
            ->with('success', 'Pengambilan Sampel berhasil disimpan.');
    }

    public function update(string $uuid)
    {
        $sampel = Sampel::where('uuid', $uuid)->firstOrFail();
        $userPlant = Auth::user()->plant;
        $produks = Produk::where('plant', $userPlant)->get();

        return view('form.sampel.update', compact('sampel', 'produks'));
    }

    public function update_qc(Request $request, string $uuid)
    {
        $sampel = Sampel::where('uuid', $uuid)->firstOrFail();

        $request->validate([
            'date'            => 'required|date',
            'nama_produk'     => 'required|string|max:255',
            'kode_produksi'   => 'required|string',
            'keterangan'      => 'nullable|string',
            'jenis_sampel'    => 'nullable|string|max:255',
            'jenis_sampel_select' => 'nullable|string|max:255',
        ]);

        $jenisSampel = $request->jenis_sampel ?: $request->jenis_sampel_select;
        if (!$jenisSampel) {
            return back()->withErrors(['jenis_sampel' => 'Jenis sampel wajib diisi.'])->withInput();
        }

        $username_updated = Auth::user()->username ?? 'None';

        $updateData = [
            'date'            => $request->date,
            'jenis_sampel'    => $jenisSampel,
            'nama_produk'     => $request->nama_produk,
            'kode_produksi'   => $request->kode_produksi,
            'keterangan'      => $request->keterangan,
            'username_updated' => $username_updated,
        ];

        $sampel->update($updateData);

        return redirect()->route('sampel.index')
            ->with('success', 'Pengambilan Sampel berhasil diperbarui.');
    }

    public function edit(string $uuid)
    {
        $sampel = Sampel::where('uuid', $uuid)->firstOrFail();
        $userPlant = Auth::user()->plant;
        $produks = Produk::where('plant', $userPlant)->get();

        return view('form.sampel.edit', compact('sampel', 'produks'));
    }

    public function edit_spv(Request $request, string $uuid)
    {
        $sampel = Sampel::where('uuid', $uuid)->firstOrFail();

        $request->validate([
            'date'            => 'required|date',
            'nama_produk'     => 'required|string|max:255',
            'kode_produksi'   => 'required|string',
            'keterangan'      => 'nullable|string',
            'jenis_sampel'    => 'nullable|string|max:255',
            'jenis_sampel_select' => 'nullable|string|max:255',
        ]);

        $jenisSampel = $request->jenis_sampel ?: $request->jenis_sampel_select;
        if (!$jenisSampel) {
            return back()->withErrors(['jenis_sampel' => 'Jenis sampel wajib diisi.'])->withInput();
        }

        $updateData = [
            'date'            => $request->date,
            'jenis_sampel'    => $jenisSampel,
            'nama_produk'     => $request->nama_produk,
            'kode_produksi'   => $request->kode_produksi,
            'keterangan'      => $request->keterangan,
        ];

        $sampel->update($updateData);

        return redirect()->route('sampel.index')
            ->with('success', 'Pengambilan Sampel berhasil diperbarui.');
    }

    public function verification(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');
        $userPlant  = Auth::user()->plant;

        $data = Sampel::query()
            ->where('plant', $userPlant)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
                        ->orWhere('nama_produk', 'like', "%{$search}%")
                        ->orWhere('kode_produksi', 'like', "%{$search}%")
                        ->orWhere('jenis_sampel', 'like', "%{$search}%");
                });
            })
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->all());

        return view('form.sampel.index', compact('data', 'search', 'date'));
    }

    public function updateVerification(Request $request, $uuid)
    {
        $request->validate([
            'status_spv'  => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:255',
        ]);

        $sampel = Sampel::where('uuid', $uuid)->firstOrFail();

        $sampel->update([
            'status_spv'  => $request->status_spv,
            'catatan_spv' => $request->catatan_spv,
            'nama_spv'    => Auth::user()->username,
            'tgl_update_spv' => now(),
        ]);

        return redirect()->route('sampel.index', [
            'page'   => $request->input('page', 1),
            'search' => $request->input('search'),
            'date'   => $request->input('date'),
        ])->with('success', 'Status Verifikasi Pengambilan Sampel berhasil diperbarui.');
    }

    public function multiVerification(Request $request)
    {
        $request->validate([
            'startdate' => 'required|date',
            'enddate'   => 'required|date|after_or_equal:startdate',
        ]);

        $updated = Sampel::where('is_audit', 0)
            ->where('plant', Auth::user()->plant_active ?? Auth::user()->plant)
            ->where('status_spv', 0)
            ->whereBetween('date', [
                $request->startdate,
                $request->enddate,
            ])
            ->update([
                'status_spv'     => 1,
                'catatan_spv'    => null,
                'nama_spv'       => Auth::user()->username,
                'tgl_update_spv' => now(),
            ]);

        return redirect()->route('sampel.index')
            ->with('success', "Multi Verification berhasil. {$updated} data diperbarui.");
    }

    public function destroy($uuid)
    {
        $sampel = Sampel::where('uuid', $uuid)->firstOrFail();
        $sampel->delete();
        return redirect()->route('sampel.index')->with('success', 'Sampel berhasil dihapus');
    }

    public function recyclebin()
    {
        $sampel = Sampel::onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->paginate(10);

        return view('form.sampel.recyclebin', compact('sampel'));
    }
    public function restore($uuid)
    {
        $sampel = Sampel::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $sampel->restore();

        return redirect()->route('sampel.recyclebin')
            ->with('success', 'Data berhasil direstore.');
    }
    public function deletePermanent($uuid)
    {
        $sampel = Sampel::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $sampel->forceDelete();

        return redirect()->route('sampel.recyclebin')
            ->with('success', 'Data berhasil dihapus permanen.');
    }

    public function exportPdf(Request $request)
{
    $date      = $request->input('date');
    $userPlant = Auth::user()->plant;

    $items = Sampel::query()
        ->where('plant', $userPlant)
        ->when($date, function ($query) use ($date) {
            $query->whereDate('date', $date);
        })
        ->orderBy('date', 'asc')
        ->get();

    $noDokumen = List_form::where('plant', $userPlant)
        ->where('laporan', 'Pengambilan Sampel')
        ->value('no_dokumen');

    $kodeMap = \App\Models\Mincing::whereIn(
        'uuid',
        $items->pluck('kode_produksi')->filter()->unique()
    )->pluck('kode_produksi', 'uuid');

    $usernames = $items->pluck('username')
        ->merge($items->pluck('nama_spv'))
        ->filter()
        ->unique()
        ->values();

    $userMap = \App\Models\User::whereIn('username', $usernames)
        ->pluck('name', 'username');

    $first = $items->first();

    $pemeriksa    = '';
    $tglPemeriksa = '';
    $spv          = '';
    $tglSpv       = '';

    if ($first) {
        $pemeriksa = $userMap[$first->username] ?? $first->username ?? '';
        $tglPemeriksa = $first->created_at
            ? \Carbon\Carbon::parse($first->created_at)->format('d-m-Y')
            : '';
    }

    $semuaDisetujui = $items->isNotEmpty()
        && $items->every(fn ($i) => !empty($i->nama_spv));

    if ($semuaDisetujui) {
        $spvRaw = trim((string) $first->nama_spv);
        $spv = $userMap[$spvRaw] ?? $spvRaw;
        $tglSpv = $first->tgl_update_spv
            ? \Carbon\Carbon::parse($first->tgl_update_spv)->format('d-m-Y')
            : '';
    }

    if (ob_get_length()) {
        ob_end_clean();
    }

    $pdf = new \TCPDF('L', PDF_UNIT, 'A4', true, 'UTF-8', false);

    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetTitle('Pengambilan Sampel');

    $pdf->SetPrintHeader(false);
    $pdf->SetPrintFooter(false);

    $pdf->SetMargins(5, 5, 5);
    $pdf->SetAutoPageBreak(true, 5);

    $pdf->SetFont('helvetica', '', 7);
    $pdf->AddPage();

    $html = view('reports.pengambilan-sampel', compact(
        'items',
        'request',
        'noDokumen',
        'kodeMap',
        'userMap',
        'pemeriksa',
        'tglPemeriksa',
        'spv',
        'tglSpv',
        'semuaDisetujui'
    ))->render();

    $pdf->writeHTML($html, true, false, true, false, '');

    $pdf->Output('Pengambilan_Sampel_' . date('d-m-Y_His') . '.pdf', 'I');

    exit();
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
