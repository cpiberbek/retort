<?php

namespace App\Http\Controllers;

use App\Models\Pvdc;
use App\Models\Mincing;
use App\Models\Produk;
use App\Models\Mesin;
use App\Models\Supplier;
use App\Models\List_form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use TCPDF;

class PvdcController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->user()->isAuditView()) {
            return redirect()->route('pvdc.audit');
        }

        $search     = $request->input('search');
        $date       = $request->input('date');
        $shift      = $request->input('shift');
        $namaProduk = $request->input('nama_produk');
        $userPlant  = Auth::user()->plant;

        // Ambil list produk untuk dropdown
        $produks = Pvdc::where('plant', $userPlant)
            ->where('is_audit', false)
            ->select('nama_produk')
            ->distinct()
            ->orderBy('nama_produk')
            ->get();

        // Query utama PVDC
        $data = Pvdc::query()
            ->where('plant', $userPlant)
            ->with('auditVersion')
            ->where('is_audit', false)

            // Filter pencarian bebas
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
                        ->orWhere('nama_produk', 'like', "%{$search}%")
                        ->orWhere('nama_supplier', 'like', "%{$search}%")
                        ->orWhere('catatan', 'like', "%{$search}%");
                });
            })

            // Filter berdasarkan tanggal
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date', $date);
            })

            // Filter shift
            ->when($shift, function ($query) use ($shift) {
                $query->where('shift', $shift);
            })

            // Filter nama produk
            ->when($namaProduk, function ($query) use ($namaProduk) {
                $query->where('nama_produk', $namaProduk);
            })

            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->all());

        $pvdc = Pvdc::first();

        return view(
            'form.pvdc.index',
            compact(
                'data',
                'produks',
                'search',
                'date',
                'shift',
                'namaProduk'
            )
        );
    }

    public function auditIndex(Request $request)
    {
        if (!auth()->user()->isAuditView()) {                   
            return redirect()->route('pvdc.index');
        }
        $search     = $request->input('search');
        $date       = $request->input('date');
        $shift      = $request->input('shift');
        $namaProduk = $request->input('nama_produk');
        $userPlant  = Auth::user()->plant;

        $produks = Pvdc::where('plant', $userPlant)
            ->select('nama_produk')
            ->distinct()
            ->orderBy('nama_produk')
            ->get();

        $data = Pvdc::query()
            ->where('plant', $userPlant)
            ->where(function ($query) {
                $query->where('is_audit', true)
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('is_audit', false)
                            ->whereDoesntHave('auditVersion');
                    });
            })

            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
                        ->orWhere('nama_produk', 'like', "%{$search}%")
                        ->orWhere('nama_supplier', 'like', "%{$search}%")
                        ->orWhere('catatan', 'like', "%{$search}%");
                });
            })

            ->when($date, function ($query) use ($date) {
                $query->whereDate('date', $date);
            })

            ->when($shift, function ($query) use ($shift) {
                $query->where('shift', $shift);
            })

            ->when($namaProduk, function ($query) use ($namaProduk) {
                $query->where('nama_produk', $namaProduk);
            })
            ->orderByRaw('COALESCE((SELECT original.date FROM pvdcs AS original WHERE original.uuid = pvdcs.source_uuid LIMIT 1), pvdcs.date) DESC')
            ->orderByRaw('CAST(COALESCE((SELECT original.shift FROM pvdcs AS original WHERE original.uuid = pvdcs.source_uuid LIMIT 1), pvdcs.shift) AS UNSIGNED) DESC')
            ->orderByRaw('COALESCE((SELECT original.created_at FROM pvdcs AS original WHERE original.uuid = pvdcs.source_uuid LIMIT 1), pvdcs.created_at) DESC')
            ->paginate(10)
            ->appends($request->all());

        return view(
            'form.pvdc.index_audit',
            compact(
                'data',
                'produks',
                'search',
                'date',
                'shift',
                'namaProduk'
            )
        );
    }

    public function duplicateToAudit($uuid)
    {
        $pvdc = Pvdc::where('uuid', $uuid)
            ->where('is_audit', false)
            ->firstOrFail();

        $audit = $pvdc->auditVersion;

        if (!$audit) {
            $audit = $pvdc->copyToAudit();
        }

        return redirect()->route(
            'pvdc.edit.form',
            $audit->uuid
        );
    }

    public function autoDuplicateToAudit($uuid)
    {
        $created = false;

        DB::transaction(function () use ($uuid, &$created) {
            $pvdc = Pvdc::where('uuid', $uuid)
                ->where('plant', Auth::user()->plant)
                ->where('is_audit', false)
                ->lockForUpdate()
                ->firstOrFail();

            if (!$pvdc->auditVersion) {
                $pvdc->copyToAudit();
                $created = true;
            }
        });

        return back()->with(
            'success',
            $created
                ? 'Data berhasil diduplikasi menjadi data Audit.'
                : 'Data Audit sudah tersedia, tidak diduplikasi ulang.'
        );
    }

    private function bulkAuditBaseQuery(string $start, string $end)
    {
        return Pvdc::query()
            ->where('plant', Auth::user()->plant)
            ->where('is_audit', false)
            ->whereDate('date', '>=', $start)
            ->whereDate('date', '<=', $end);
    }

    public function auditBulkPreview(Request $request)
    {
        $request->validate([
            'start_date' => ['required', 'date'],
            'end_date'   => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        $base = fn () => $this->bulkAuditBaseQuery($request->start_date, $request->end_date);

        $total    = $base()->count();
        $sudahAda = $base()->whereHas('auditVersion')->count();

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

        $rows = $this->bulkAuditBaseQuery($request->start_date, $request->end_date)
            ->whereDoesntHave('auditVersion')
            ->get();

        if ($rows->isEmpty()) {
            return back()->with('info', 'Tidak ada data yang perlu diduplikasi.');
        }

        $created = 0;
        $failed  = 0;

        foreach ($rows as $row) {
            try {
                DB::transaction(function () use ($row, &$created) {
                    // Cek ulang di dalam transaksi supaya tidak dobel
                    $fresh = Pvdc::where('uuid', $row->uuid)->lockForUpdate()->first();

                    if ($fresh && !$fresh->auditVersion()->exists()) {
                        $fresh->copyToAudit();
                        $created++;
                    }
                });
            } catch (\Throwable $e) {
                report($e);
                $failed++;
            }
        }

        if ($failed > 0) {
            return back()->with('warning', "{$created} data berhasil diduplikasi, {$failed} gagal (cek log).");
        }

        return back()->with('success', "{$created} data berhasil diduplikasi menjadi data Audit.");
    }

    public function create()
    {
        $userPlant = Auth::user()->plant;

        $produks = Produk::where('plant', $userPlant)->get();
        $mesins = Mesin::where('plant', $userPlant)
            ->where('jenis_mesin', 'Stuffing')
            ->orderBy('nama_mesin')
            ->get();

        $suppliers = Supplier::where('plant', $userPlant)
            ->where('jenis_barang', 'Packaging')
            ->orderBy('nama_supplier')
            ->get();

        return view('form.pvdc.create', compact('produks', 'mesins', 'suppliers'));
    }

    public function store(Request $request)
    {
        $username   = Auth::user()->username ?? 'User RTM';
        $userPlant  = Auth::user()->plant;

        $request->validate([
            'date'        => 'required|date',
            'shift'       => 'required',
            'nama_produk' => 'required',
            'nama_supplier' => 'required',
            'tgl_kedatangan'  => 'required|date',
            'tgl_expired'     => 'required|date',
            'catatan'         => 'nullable|string',
            'data_pvdc'       => 'nullable|array',
        ]);

        $data = $request->only(['date', 'shift', 'nama_produk', 'nama_supplier', 'tgl_kedatangan', 'tgl_expired', 'catatan']);
        $data['username']        = $username;
        $data['plant']           = $userPlant;
        $data['status_spv']      = "0";
        $data['data_pvdc']       = json_encode($request->input('data_pvdc', []), JSON_UNESCAPED_UNICODE);

        Pvdc::create($data);

        return redirect()->route('pvdc.index')
            ->with('success', 'Data No. Lot PVDC berhasil disimpan');
    }

    public function update(string $uuid)
    {
        $pvdc = Pvdc::where('uuid', $uuid)->firstOrFail();
        $userPlant = Auth::user()->plant;

        $produks = Produk::where('plant', $userPlant)->get();
        $mesins = Mesin::where('plant', $userPlant)
            ->where('jenis_mesin', 'Stuffing')
            ->orderBy('nama_mesin')
            ->get();

        $suppliers = Supplier::where('plant', $userPlant)
            ->where('jenis_barang', 'Packaging')
            ->orderBy('nama_supplier')
            ->get();

        $pvdcData = !empty($pvdc->data_pvdc) ? json_decode($pvdc->data_pvdc, true) : [];

        foreach ($pvdcData as &$mesinRow) {
            if (empty($mesinRow['detail'])) {
                continue;
            }
            // Paksa detail jadi array numerik (buang key aneh 1.3333 dst)
            $details = is_array($mesinRow['detail'])
                ? array_values($mesinRow['detail'])
                : [];

            foreach ($details as &$detailRow) {
                if (empty($detailRow['batch'])) {
                    continue;
                }
                $mincing = Mincing::where('uuid', $detailRow['batch'])->first();
                if ($mincing) {
                    $detailRow['batch_display'] = $mincing->kode_produksi;
                }
            }
            $mesinRow['detail'] = $details;
        }

        return view('form.pvdc.update', compact('pvdc', 'produks', 'pvdcData', 'mesins', 'suppliers'));
    }

    public function update_qc(Request $request, string $uuid)
    {
        $pvdc = Pvdc::where('uuid', $uuid)->firstOrFail();
        $username_updated = Auth::user()->username ?? 'User RTT';

        $request->validate([
            'date'            => 'required|date',
            'shift'           => 'required',
            'nama_produk'     => 'required',
            'nama_supplier'   => 'required',
            'tgl_kedatangan'  => 'required|date',
            'tgl_expired'     => 'required|date',
            'catatan'         => 'nullable|string',
            'data_pvdc'       => 'nullable|array',
            'data_pvdc_old'   => 'nullable|array',
        ]);

        $data_pvdc_old = $request->input('data_pvdc_old', []);
        $data_pvdc_new = $request->input('data_pvdc', []);

        $combined_pvdc = array_merge($data_pvdc_old, $data_pvdc_new);

        $data = [
            'date'             => $request->date,
            'shift'            => $request->shift,
            'nama_produk'      => $request->nama_produk,
            'nama_supplier'    => $request->nama_supplier,
            'tgl_kedatangan'   => $request->tgl_kedatangan,
            'tgl_expired'      => $request->tgl_expired,
            'catatan'          => $request->catatan,
            'username_updated' => $username_updated,
            'data_pvdc'        => json_encode($combined_pvdc, JSON_UNESCAPED_UNICODE),
        ];

        $pvdc->update($data);

        return redirect()->route('pvdc.index')->with('success', 'Data No. Lot PVDC berhasil diperbarui');
    }

    public function edit(string $uuid)
    {
        $pvdc = Pvdc::where('uuid', $uuid)->firstOrFail();
        $userPlant = Auth::user()->plant;

        $produks = Produk::where('plant', $userPlant)->get();
        $mesins = Mesin::where('plant', $userPlant)
            ->where('jenis_mesin', 'Stuffing')
            ->orderBy('nama_mesin')
            ->get();

        $suppliers = Supplier::where('plant', $userPlant)
            ->where('jenis_barang', 'Packaging')
            ->orderBy('nama_supplier')
            ->get();

        $pvdcData = !empty($pvdc->data_pvdc) ? json_decode($pvdc->data_pvdc, true) : [];

        foreach ($pvdcData as &$mesinRow) {
            if (empty($mesinRow['detail'])) {
                continue;
            }
            $details = is_array($mesinRow['detail']) ? array_values($mesinRow['detail']) : [];

            foreach ($details as &$detailRow) {
                if (empty($detailRow['batch'])) {
                    continue;
                }
                $mincing = Mincing::where('uuid', $detailRow['batch'])->first();
                if ($mincing) {
                    $detailRow['batch_display'] = $mincing->kode_produksi;
                }
            }
            $mesinRow['detail'] = $details;
        }

        return view('form.pvdc.edit', compact('pvdc', 'produks', 'pvdcData', 'mesins', 'suppliers'));
    }

    public function edit_spv(Request $request, string $uuid)
    {
        $pvdc = Pvdc::where('uuid', $uuid)->firstOrFail();

        $request->validate([
            'date'            => 'required|date',
            'shift'           => 'required',
            'nama_produk'     => 'required',
            'nama_supplier'   => 'required',
            'tgl_kedatangan'  => 'required|date',
            'tgl_expired'     => 'required|date',
            'catatan'         => 'nullable|string',
            'data_pvdc'       => 'nullable|array',
            'data_pvdc_old'   => 'nullable|array',
        ]);

        $data_pvdc_old = $request->input('data_pvdc_old', []);
        $data_pvdc_new = $request->input('data_pvdc', []);

        $combined_pvdc = array_merge($data_pvdc_old, $data_pvdc_new);

        $data = [
            'date'             => $request->date,
            'shift'            => $request->shift,
            'nama_produk'      => $request->nama_produk,
            'nama_supplier'    => $request->nama_supplier,
            'tgl_kedatangan'   => $request->tgl_kedatangan,
            'tgl_expired'      => $request->tgl_expired,
            'catatan'          => $request->catatan,
            'data_pvdc'        => json_encode($combined_pvdc, JSON_UNESCAPED_UNICODE),
        ];

        $pvdc->update($data);

        return redirect()->route('pvdc.index')->with('success', 'Data No. Lot PVDC berhasil diperbarui');
    }

    public function verification(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');
        $userPlant  = Auth::user()->plant;

        $data = Pvdc::query()
            ->where('plant', $userPlant)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
                        ->orWhere('nama_produk', 'like', "%{$search}%")
                        ->orWhere('kode_produksi', 'like', "%{$search}%")
                        ->orWhere('nama_supplier', 'like', "%{$search}%")
                        ->orWhere('no_lot', 'like', "%{$search}%");
                });
            })
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->all());

        return view('form.pvdc.index', compact('data', 'search', 'date'));
    }

    public function updateVerification(Request $request, $uuid)
    {
        $request->validate([
            'status_spv' => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:255',
            'tgl_update_spv'
        ]);

        $pvdc = Pvdc::where('uuid', $uuid)->firstOrFail();

        $pvdc->update([
            'status_spv' => $request->status_spv,
            'catatan_spv' => $request->catatan_spv,
            'nama_spv' => Auth::user()->username,
            'tgl_update_spv' => now(),
        ]);

        return redirect()->route('pvdc.index', [
            'page' => $request->input('page', 1),
            'search' => $request->input('search'),
            'date' => $request->input('date'),
        ])->with('success', 'Status Verifikasi Data No. Lot PVDC berhasil diperbarui.');
    }

    public function multiVerification(Request $request)
    {
        $request->validate([
            'startdate' => 'required|date',
            'enddate'   => 'required|date|after_or_equal:startdate',
        ]);

        $updated = Pvdc::where('is_audit', 0)
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

        return redirect()->route('pvdc.index')
            ->with('success', "Multi Verification Data No. Lot PVDC berhasil. {$updated} data diperbarui.");
    }

    public function destroy($uuid)
    {
        $pvdc = Pvdc::where('uuid', $uuid)->firstOrFail();
        $pvdc->delete();
        return redirect()->route('pvdc.index')->with('success', 'Pvdc berhasil dihapus');
    }

    public function recyclebin()
    {
        $pvdc = Pvdc::onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->paginate(10);

        return view('form.pvdc.recyclebin', compact('pvdc'));
    }
    public function restore($uuid)
    {
        $pvdc = Pvdc::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $pvdc->restore();

        return redirect()->route('pvdc.recyclebin')
            ->with('success', 'Data berhasil direstore.');
    }
    public function deletePermanent($uuid)
    {
        $pvdc = Pvdc::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $pvdc->forceDelete();

        return redirect()->route('pvdc.recyclebin')
            ->with('success', 'Data berhasil dihapus permanen.');
    }

    public function exportPdf(Request $request)
    {
        $search    = $request->input('search');
        $date      = $request->input('date');
        $shift     = $request->input('shift');
        $userPlant = Auth::user()->plant;

        $headers = Pvdc::query()
            ->where('plant', $userPlant)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
                        ->orWhere('nama_produk', 'like', "%{$search}%")
                        ->orWhere('nama_supplier', 'like', "%{$search}%")
                        ->orWhere('catatan', 'like', "%{$search}%");
                });
            })
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->when($shift, function ($query) use ($shift) {
                $query->where('shift', $shift);
            })
            ->orderBy('date', 'asc')
            ->orderBy('shift', 'asc')
            ->get();

        if ($headers->isEmpty()) {
            return back()->with('error', 'Data tidak ditemukan, pastikan tanggal & varian dengan benar sebelum melakukan export');
        }

        $noDokumen = List_form::where('plant', $userPlant)
            ->where('laporan', 'Data No. Lot PVDC')
            ->value('no_dokumen');

        $decoded    = [];
        $batchUuids = collect();

        foreach ($headers as $index => $header) {
            $data = is_array($header->data_pvdc)
                ? $header->data_pvdc
                : json_decode($header->data_pvdc, true);

            $decoded[$index] = $data ?? [];

            foreach ($decoded[$index] as $mesinRow) {
                foreach ($mesinRow['detail'] ?? [] as $row) {
                    if (!empty($row['batch'])) {
                        $batchUuids->push($row['batch']);
                    }
                }
            }
        }

        $mincingMap = Mincing::whereIn('uuid', $batchUuids->unique())
            ->pluck('kode_produksi', 'uuid');

        $mesinRows = [];

        foreach ($headers as $index => $header) {
            foreach ($decoded[$index] as $mesinRow) {
                $namaMesin = $mesinRow['mesin'] ?? '-';

                foreach (array_values($mesinRow['detail'] ?? []) as $row) {
                    $mesinRows[$namaMesin][] = [
                        'batch' => $mincingMap[$row['batch'] ?? null] ?? '-',
                        'lot'   => $row['no_lot'] ?? '-',
                        'waktu' => $row['waktu'] ?? '-',
                    ];
                }
            }
        }

        $bands = [];

        foreach (array_chunk($mesinRows, 3, true) as $group) {
            $segments = [];
            foreach ($group as $namaMesin => $rows) {
                $segments[$namaMesin] = array_chunk($rows, 20);
            }

            $maxSegment = max(array_map('count', $segments));

            for ($s = 0; $s < $maxSegment; $s++) {
                $band = [];
                foreach ($segments as $namaMesin => $chunks) {
                    $band[] = isset($chunks[$s])
                        ? ['mesin' => $namaMesin, 'rows' => $chunks[$s]]
                        : null;
                }
                while (count($band) < 3) {
                    $band[] = null;
                }
                $bands[] = $band;
            }
        }

        $pages       = [];
        $currentPage = [];
        $currentCost = 0;

        foreach ($bands as $band) {
            $maxRows = max(array_map(fn($m) => $m ? count($m['rows']) : 0, $band));
            $cost    = $maxRows + 2;

            if (!empty($currentPage) && $currentCost + $cost > 22) {
                $pages[]     = $currentPage;
                $currentPage = [];
                $currentCost = 0;
            }

            $currentPage[] = $band;
            $currentCost  += $cost;
        }

        $pages[] = $currentPage;

        $tanggal = $headers->pluck('date')
            ->unique()
            ->map(fn($d) => \Carbon\Carbon::parse($d)->translatedFormat('l, d-m-Y'))
            ->implode(', ');

        $shiftText = $headers->pluck('shift')->unique()->implode(', ');

        $namaProduk = $headers->pluck('nama_produk')->unique()->implode(', ');

        $tglKedatangan = $headers->pluck('tgl_kedatangan')
            ->filter()
            ->unique()
            ->map(fn($d) => \Carbon\Carbon::parse($d)->format('d-m-Y'))
            ->implode(', ');

        $firstExpired = $headers->first()->tgl_expired;
        $tglExpired   = $firstExpired
            ? \Carbon\Carbon::parse($firstExpired)->format('d-m-Y')
            : '';

        $usernames = $headers
            ->flatMap(fn($h) => [$h->username, $h->nama_spv])
            ->filter()
            ->unique()
            ->values();

        $userMap  = \App\Models\User::whereIn('username', $usernames)->pluck('name', 'username');
        $namaUser = fn($username) => $username ? ($userMap[$username] ?? $username) : '';

        $first        = $headers->first();
        $pemeriksa    = $namaUser(trim((string) $first->username));
        $tglPemeriksa = $first->created_at
            ? \Carbon\Carbon::parse($first->created_at)->format('d-m-Y')
            : '';

        $semuaDisetujui = $headers->every(fn($h) => (int) $h->status_spv === 1);

        $spv    = '';
        $tglSpv = '';

        if ($semuaDisetujui) {
            $spvRow = $headers->first(fn($h) => !empty($h->nama_spv));

            if ($spvRow) {
                $spv    = $namaUser(trim((string) $spvRow->nama_spv));
                $tglSpv = $spvRow->tgl_update_spv
                    ? \Carbon\Carbon::parse($spvRow->tgl_update_spv)->format('d-m-Y')
                    : '';
            }
        }

        if (ob_get_length()) {
            ob_end_clean();
        }

        $pdf = new \TCPDF('L', 'mm', [330, 210], true, 'UTF-8', false);

        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle('Laporan Data No. Lot PVDC');

        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);

        $pdf->SetMargins(5, 5, 5);
        $pdf->SetAutoPageBreak(true, 5);

        $pdf->SetFont('helvetica', '', 6);
        $pdf->AddPage();

        $html = view('reports.pvdc', compact(
            'pages',
            'tanggal',
            'shiftText',
            'namaProduk',
            'tglKedatangan',
            'tglExpired',
            'noDokumen',
            'pemeriksa',
            'tglPemeriksa',
            'spv',
            'tglSpv',
            'semuaDisetujui'
        ))->render();

        $pdf->writeHTML($html, true, false, true, false, '');

        $pdf->Output('Laporan_PVDC_' . date('Ymd_His') . '.pdf', 'I');

        exit();
    }
}
