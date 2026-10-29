<?php

namespace App\Http\Controllers;

use App\Models\Suhu;
use App\Models\Produk;
use App\Models\Area_suhu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Exports\SuhuExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\List_form;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SuhuController extends Controller
{
    private const LAPORAN_SUHU = 'Pemeriksaan Suhu dan RH Ruangan';

    public function index(Request $request)
    {
        if (Auth::user()->isAuditView()) {
            return redirect()->route('suhu.audit');
        }

        $search    = $request->input('search');
        $date      = $request->input('date');
        $shift     = $request->input('shift');
        $userPlant  = Auth::user()->plant;
        $area_suhus = Area_suhu::where('plant', $userPlant)->get();

        $data = Suhu::query()
            ->where('plant', $userPlant)
            ->where('is_audit', false)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%");
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

        return view('form.suhu.index', compact('data', 'search', 'date', 'shift', 'area_suhus'));
    }

    public function auditIndex(Request $request)
    {
        if (!Auth::user()->isAuditView()) {
            return redirect()->route('suhu.index');
        }

        $date       = $request->input('date');
        $shift      = $request->input('shift');
        $userPlant  = Auth::user()->plant;
        $area_suhus = Area_suhu::where('plant', $userPlant)->get();

        $auditedSourceUuids = Suhu::where('is_audit', true)
            ->whereNotNull('source_uuid')
            ->pluck('source_uuid')
            ->toArray();

        $data = Suhu::query()
            ->where('plant', $userPlant)
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
            ->when($date, fn ($q) => $q->whereDate('date', $date))
            ->when($shift, fn ($q) => $q->where('shift', $shift))
            ->orderByRaw('COALESCE((SELECT original.date FROM suhus AS original WHERE original.uuid = suhus.source_uuid LIMIT 1), suhus.date) DESC')
            ->orderByRaw('CAST(COALESCE((SELECT original.shift FROM suhus AS original WHERE original.uuid = suhus.source_uuid LIMIT 1), suhus.shift) AS UNSIGNED) DESC')
            ->orderByRaw('COALESCE((SELECT original.created_at FROM suhus AS original WHERE original.uuid = suhus.source_uuid LIMIT 1), suhus.created_at) DESC')
            ->paginate(10)
            ->appends($request->all());

        return view('form.suhu.index_audit', compact('data', 'date', 'shift', 'area_suhus'));
    }

    public function duplicateToAudit(string $uuid)
    {
        abort_if(Auth::user()->hasRole('auditor'), 403);

        $row = Suhu::where('uuid', $uuid)
            ->where('plant', Auth::user()->plant)
            ->where('is_audit', false)
            ->firstOrFail();

        if ($existing = $row->auditVersion) {
            return redirect()->route('suhu.edit.form', $existing->uuid);
        }

        $clone = $this->createCorrectedAudit($row);

        return redirect()->route('suhu.edit.form', $clone->uuid)
            ->with('info', 'Salinan data audit berhasil dibuat. Silakan lakukan penyesuaian.');
    }

    private function createCorrectedAudit(Suhu $row): Suhu
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
            ->where('laporan', self::LAPORAN_SUHU)
            ->first();

        abort_if(empty($master?->standar_ref), 422, 'Master auto correct belum diterapkan untuk plant ini.');

        $base = Suhu::where('plant', $plant)
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
            return redirect()->route('suhu.audit')->with('warning', 'Tidak ada entry di tanggal ini.');
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
            return redirect()->route('suhu.audit')
                ->with('info', "0 data baru. {$skipped} data dilewati karena sudah ada audit.");
        }

        $msg = "{$created} data diduplikasi dan disesuaikan sesuai master. {$skipped} dilewati karena sudah ada.";
        if ($failed) $msg .= " {$failed} gagal diproses (cek log).";

        return redirect()->route('suhu.audit')->with($failed ? 'warning' : 'success', $msg);
    }

    private function decodeRows($value): array
    {
        $guard = 0;
        while (is_string($value) && $guard++ < 3) {
            $value = json_decode($value, true);
        }

        return is_array($value) ? array_values($value) : [];
    }

    private function autoCorrectFromMaster(Suhu $model): void
    {
        $master = List_form::where('plant', $model->plant)
            ->where('laporan', self::LAPORAN_SUHU)
            ->first();

        $std = $master?->standar_ref;
        if (is_string($std)) $std = json_decode($std, true);
        if (!is_array($std) || empty($std)) return;

        $norm = fn ($s) => preg_replace('/[^a-z0-9]/', '', mb_strtolower((string) $s));

        $buildRules = function (string $group) use ($std, $norm) {
            $rules = [];

            foreach ((array) ($std[$group] ?? []) as $r) {
                $min = $r['min'] ?? null;
                $max = $r['max'] ?? null;
                if (empty($r['area']) || $min === null || $min === '' || $max === null || $max === '') continue;

                $rules[$norm($r['area'])] = [(float) $min, (float) $max];
            }

            return $rules;
        };

        $suhuRules = $buildRules('suhu_area');
        $rhRules   = $buildRules('rh_area');

        if (!empty($suhuRules) || !empty($rhRules)) {
            $original = $model->hasil_suhu;
            $rows     = $this->decodeRows($original);

            $filled = fn ($v) => $v !== null && $v !== '' && $v !== '-';

            $rand = function (array $r) {
                $lo = min($r[0], $r[1]);
                $hi = max($r[0], $r[1]);

                return round(mt_rand((int) round($lo * 10), (int) round($hi * 10)) / 10, 1);
            };

            foreach ($rows as &$row) {
                $key = $norm($row['area'] ?? '');

                if (isset($suhuRules[$key]) && $filled($row['suhu'] ?? null)) {
                    $row['suhu'] = $rand($suhuRules[$key]);
                }

                if (isset($rhRules[$key]) && $filled($row['rh'] ?? null)) {
                    $row['rh'] = $rand($rhRules[$key]);
                }
            }
            unset($row);

            $model->hasil_suhu = is_array($original)
                ? $rows
                : json_encode($rows, JSON_UNESCAPED_UNICODE);
        }

        foreach (['keterangan', 'catatan'] as $col) {
            $v = $std[$col] ?? null;
            if (is_string($v) && trim($v) !== '') {
                $model->$col = trim($v);
            }
        }

        $model->save();
    }

    public function create(Request $request)
    {
        $userPlant = Auth::user()->plant;

        $date = $request->input('date', now()->toDateString());
        $pukul = $request->input('pukul');

        if ($pukul) {
            $existing = Suhu::where('date', $date)
                ->where('pukul', $pukul)
                ->where('plant', $userPlant)
                ->first();

            if ($existing) {
                return redirect()->route('suhu.update.form', $existing->uuid);
            }
        }

        $area_suhus = Area_suhu::where('plant', $userPlant)
            ->orderBy('area')
            ->get(['area', 'standar_min', 'standar_max', 'rh_min', 'rh_max']);

        $existing = Suhu::where('date', $date)
            ->where('plant', $userPlant)
            ->get();

        $suhuData = [];

        foreach ($existing as $row) {
            $raw = json_decode($row->hasil_suhu, true) ?? [];

            foreach ($raw as $item) {
                $suhuData[$item['area']] = [
                    'suhu' => $item['suhu'] ?? $item['nilai'] ?? null,
                    'rh' => $item['rh'] ?? null,
                ];
            }
        }

        return view('form.suhu.create', compact(
            'area_suhus',
            'suhuData',
            'date',
            'pukul'
        ));
    }

    public function store(Request $request)
    {
        $username = Auth::user()->username ?? 'User RTM';
        $userPlant = Auth::user()->plant;

        $nama_produksi = session()->has('selected_produksi')
            ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
            : 'Produksi RTT';

        $request->validate([
            'date' => 'required|date',
            'shift' => 'required',
            'pukul' => 'required',
            'keterangan' => 'nullable|string',
            'catatan' => 'nullable|string',
            'hasil_suhu' => 'nullable|array',
            'hasil_rh' => 'nullable|array',
        ]);

        $existing = Suhu::where('plant', $userPlant)
            ->where('date', $request->date)
            ->where('pukul', $request->pukul)
            ->first();

        $hasil_lama = [];

        if ($existing) {
            $hasil_lama = is_string($existing->hasil_suhu)
                ? json_decode($existing->hasil_suhu, true) ?? []
                : ($existing->hasil_suhu ?? []);
        }

        $hasil_lama = collect($hasil_lama)
            ->keyBy('area')
            ->toArray();

        foreach ($request->input('hasil_suhu', []) as $item) {
            $area = $item['area'] ?? '';

            if ($area === '') {
                continue;
            }

            if (!isset($hasil_lama[$area])) {
                $hasil_lama[$area] = [
                    'area' => $area,
                    'suhu' => null,
                    'rh' => null,
                ];
            }

            if (isset($item['nilai']) && $item['nilai'] !== '') {
                $nilai = trim((string) $item['nilai']);

                $hasil_lama[$area]['suhu'] = $nilai === '-'
                    ? null
                    : (float) str_replace(',', '.', $nilai);
            }
        }

        foreach ($request->input('hasil_rh', []) as $item) {
            $area = $item['area'] ?? '';

            if ($area === '') {
                continue;
            }

            if (!isset($hasil_lama[$area])) {
                $hasil_lama[$area] = [
                    'area' => $area,
                    'suhu' => null,
                    'rh' => null,
                ];
            }

            if (isset($item['nilai']) && $item['nilai'] !== '') {
                $nilai = trim((string) $item['nilai']);

                $hasil_lama[$area]['rh'] = $nilai === '-'
                    ? null
                    : (float) str_replace(',', '.', $nilai);
            }
        }

        $area_order = Area_suhu::where('plant', $userPlant)
            ->orderBy('area')
            ->pluck('area')
            ->toArray();

        $hasil_terstruktur = collect($hasil_lama)
            ->sortBy(function ($item) use ($area_order) {
                $index = array_search($item['area'], $area_order);

                return $index === false ? PHP_INT_MAX : $index;
            })
            ->values()
            ->toArray();

        $data = [
            'date' => $request->date,
            'shift' => $request->shift,
            'pukul' => $request->pukul,
            'plant' => $userPlant,
            'hasil_suhu' => json_encode($hasil_terstruktur, JSON_UNESCAPED_UNICODE),
            'username' => $username,
            'nama_produksi' => $nama_produksi,
            'status_produksi' => '1',
            'tgl_update_produksi' => now()->addHour(),
            'status_spv' => '0',
        ];

        if ($existing) {
            if ($request->input('keterangan') !== null) {
                $data['keterangan'] = $request->input('keterangan');
            }

            if ($request->input('catatan') !== null) {
                $data['catatan'] = $request->input('catatan');
            }

            $existing->update($data);
        } else {
            $data['keterangan'] = $request->input('keterangan');
            $data['catatan'] = $request->input('catatan');

            Suhu::create($data);
        }

        return redirect()->route('suhu.index')
            ->with('success', 'Pemeriksaan Suhu dan RH berhasil disimpan');
    }

    public function update(string $uuid)
    {
        $suhu = Suhu::where('uuid', $uuid)->firstOrFail();
        $userPlant = Auth::user()->plant;
        $area_suhus = Area_suhu::where('plant', $userPlant)
            ->orderBy('area')
            ->get();

        $raw = !empty($suhu->hasil_suhu) ? json_decode($suhu->hasil_suhu, true) : [];
        $suhuData = collect($raw)->mapWithKeys(function ($item) {
            return [
                $item['area'] => [
                    'suhu' => $item['suhu'] ?? $item['nilai'] ?? null,
                    'rh'   => $item['rh'] ?? null,
                ]
            ];
        })->toArray();

        return view('form.suhu.update', compact('suhu', 'suhuData', 'area_suhus'));
    }

    public function update_qc(Request $request, string $uuid)
    {
        $suhu = Suhu::where('uuid', $uuid)->firstOrFail();

        $request->validate([
            'date' => 'required|date',
            'shift' => 'required',
            'pukul' => 'required',
            'keterangan' => 'nullable|string',
            'catatan' => 'nullable|string',
            'hasil_suhu' => 'nullable|array',
            'hasil_rh' => 'nullable|array',
        ]);

        $hasil_suhu_request = collect($request->input('hasil_suhu', []));
        $hasil_rh_request = collect($request->input('hasil_rh', []))->keyBy('area');

        $hasil_lama = is_string($suhu->hasil_suhu)
            ? json_decode($suhu->hasil_suhu, true) ?? []
            : ($suhu->hasil_suhu ?? []);

        $hasil_lama = collect($hasil_lama)->keyBy('area');

        $hasil_terstruktur = [];

        foreach ($hasil_suhu_request as $item) {
            $area = $item['area'] ?? '';

            $nilai_suhu_raw = $item['nilai'] ?? null;
            $nilai_rh_raw = $hasil_rh_request[$area]['nilai'] ?? null;

            $nilai_suhu = ($nilai_suhu_raw === null || $nilai_suhu_raw === '' || $nilai_suhu_raw === '-')
                ? null
                : (float) str_replace(',', '.', $nilai_suhu_raw);

            $nilai_rh = ($nilai_rh_raw === null || $nilai_rh_raw === '' || $nilai_rh_raw === '-')
                ? null
                : (float) str_replace(',', '.', $nilai_rh_raw);

            $lama = $hasil_lama->get($area, []);

            if ($nilai_suhu === null) {
                $nilai_suhu = $lama['suhu'] ?? null;
            }

            if ($nilai_rh === null) {
                $nilai_rh = $lama['rh'] ?? null;
            }

            $hasil_terstruktur[] = [
                'area' => $area,
                'suhu' => $nilai_suhu,
                'rh' => $nilai_rh,
            ];
        }

        foreach ($hasil_lama as $area => $item) {
            if (!collect($hasil_terstruktur)->contains('area', $area)) {
                $hasil_terstruktur[] = $item;
            }
        }

        $area_order = Area_suhu::where('plant', $suhu->plant)
            ->orderBy('area')
            ->pluck('area')
            ->toArray();

        $hasil_terstruktur = collect($hasil_terstruktur)
            ->sortBy(fn($item) => array_search($item['area'], $area_order))
            ->values()
            ->toArray();

        $data = [
            'date' => $request->date,
            'shift' => $request->shift,
            'pukul' => $request->pukul,
            'hasil_suhu' => json_encode($hasil_terstruktur, JSON_UNESCAPED_UNICODE),
        ];

        if ($request->filled('keterangan')) {
            $data['keterangan'] = $request->keterangan;
        }

        if ($request->filled('catatan')) {
            $data['catatan'] = $request->catatan;
        }

        $suhu->update($data);

        return redirect()->route('suhu.index')
            ->with('success', 'Data Pemeriksaan Suhu dan RH berhasil diperbarui');
    }

    public function edit(string $uuid)
    {
        $suhu = Suhu::where('uuid', $uuid)->firstOrFail();
        $userPlant = Auth::user()->plant;
        $area_suhus = Area_suhu::where('plant', $userPlant)
            ->orderBy('area')
            ->get();

        $raw = !empty($suhu->hasil_suhu) ? json_decode($suhu->hasil_suhu, true) : [];
        $suhuData = collect($raw)->mapWithKeys(function ($item) {
            return [
                $item['area'] => [
                    'suhu' => $item['suhu'] ?? $item['nilai'] ?? null,
                    'rh'   => $item['rh'] ?? null,
                ]
            ];
        })->toArray();;

        return view('form.suhu.edit', compact('suhu', 'suhuData', 'area_suhus'));
    }

    public function edit_spv(Request $request, string $uuid)
    {
        $suhu = Suhu::where('uuid', $uuid)->firstOrFail();

        $request->validate([
            'date' => 'required|date',
            'shift' => 'required',
            'pukul' => 'required',
            'keterangan' => 'nullable|string',
            'catatan' => 'nullable|string',
            'hasil_suhu' => 'nullable|array',
            'hasil_rh' => 'nullable|array',
        ]);

        $hasil_suhu = collect($request->input('hasil_suhu', []));
        $hasil_rh = collect($request->input('hasil_rh', []))->keyBy('area');

        $hasil_terstruktur = [];

        foreach ($hasil_suhu as $item) {

            $area = $item['area'] ?? '';

            $nilai_suhu_raw = $item['nilai'] ?? '';
            $nilai_rh_raw = $hasil_rh[$area]['nilai'] ?? '';

            $nilai_suhu = $nilai_suhu_raw === ''
                ? null
                : ($nilai_suhu_raw === '-' ? null : (float) str_replace(',', '.', $nilai_suhu_raw));

            $nilai_rh = $nilai_rh_raw === ''
                ? null
                : ($nilai_rh_raw === '-' ? null : (float) str_replace(',', '.', $nilai_rh_raw));

            $hasil_terstruktur[] = [
                'area' => $area,
                'suhu' => $nilai_suhu,
                'rh' => $nilai_rh,
            ];
        }

        $area_order = Area_suhu::where('plant', $suhu->plant)
            ->orderBy('area')
            ->pluck('area')
            ->toArray();

        $hasil_terstruktur = collect($hasil_terstruktur)
            ->sortBy(fn($item) => array_search($item['area'], $area_order))
            ->values()
            ->toArray();

        $suhu->update([
            'date' => $request->date,
            'shift' => $request->shift,
            'pukul' => $request->pukul,
            'keterangan' => $request->keterangan,
            'catatan' => $request->catatan,
            'hasil_suhu' => json_encode($hasil_terstruktur, JSON_UNESCAPED_UNICODE),
        ]);

        return redirect()->route('suhu.index')
            ->with('success', 'Data Pemeriksaan Suhu dan RH berhasil diperbarui');
    }

    public function verification(Request $request)
    {
        $search    = $request->input('search');
        $date      = $request->input('date');
        $userPlant  = Auth::user()->plant;
        $area_suhus = Area_suhu::where('plant', $userPlant)->get();

        $data = Suhu::query()
            ->where('plant', $userPlant)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%");
                });
            })
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->all());

        return view('form.suhu.index', compact('data', 'search', 'date', 'area_suhus'));
    }

    public function updateVerification(Request $request, $uuid)
    {
        $request->validate([
            'status_spv'  => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:255',
        ]);

        $suhu = Suhu::where('uuid', $uuid)->firstOrFail();

        $suhu->update([
            'status_spv'      => $request->status_spv,
            'catatan_spv'     => $request->catatan_spv,
            'nama_spv'        => Auth::user()->username,
            'tgl_update_spv'  => now(),
        ]);

        return redirect()->route('suhu.index', [
            'page'   => $request->input('page', 1),
            'search' => $request->input('search'),
            'date'   => $request->input('date'),
        ])->with('success', 'Status Verifikasi Pemeriksaan Suhu dan RH berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $suhu = Suhu::where('uuid', $uuid)->firstOrFail();
        $suhu->delete();
        return redirect()->route('suhu.index')->with('success', 'Suhu berhasil dihapus');
    }

    public function recyclebin()
    {
        $suhu = Suhu::onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->paginate(10);

        return view('form.suhu.recyclebin', compact('suhu'));
    }
    public function restore($uuid)
    {
        $suhu = Suhu::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $suhu->restore();

        return redirect()->route('suhu.recyclebin')
            ->with('success', 'Data berhasil direstore.');
    }
    public function deletePermanent($uuid)
    {
        $suhu = Suhu::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $suhu->forceDelete();

        return redirect()->route('suhu.recyclebin')
            ->with('success', 'Data berhasil dihapus permanen.');
    }

    public function exportPdf(Request $request)
    {
        // 1. Ambil Data
        $date      = $request->input('date');
        $shift     = $request->input('shift');
        $userPlant = Auth::user()->plant;

        $items = Suhu::query()
            ->where('plant', $userPlant)
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->when($shift, function ($query) use ($shift) {
                $query->where('shift', $shift);
            })
            ->orderBy('pukul', 'asc')
            ->get();

        if (ob_get_length()) {
            ob_end_clean();
        }

        // 2. Setup PDF (Landscape, A4)
        $pdf = new \TCPDF('L', PDF_UNIT, 'A4', true, 'UTF-8', false);

        // Metadata
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle('Pemeriksaan Suhu dan RH');

        // Hilangkan Header/Footer Bawaan
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);

        // Set Margin
        $pdf->SetMargins(5, 5, 5);
        $pdf->SetAutoPageBreak(TRUE, 5);

        // Set Font Default
        $pdf->SetFont('helvetica', '', 7);

        $pdf->AddPage();

        // 3. Render
        $html = view('reports.pemeriksaan-suhu-rh', compact('items', 'request'))->render();
        $pdf->writeHTML($html, true, false, true, false, '');

        $filename = 'Pemeriksaan_Suhu_RH_' . date('d-m-Y_His') . '.pdf';
        $pdf->Output($filename, 'I');
        exit();
    }

    public function exportExcel(Request $request)
    {
        $date      = $request->input('date');
        $shift     = $request->input('shift');
        $userPlant = Auth::user()->plant;

        $items = Suhu::query()
            ->where('plant', $userPlant)
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->when($shift, function ($query) use ($shift) {
                $query->where('shift', $shift);
            })
            ->orderBy('pukul', 'asc')
            ->get();

        $areaSuhus = Area_suhu::where('plant', $userPlant)
            ->orderBy('area')
            ->get();

        $dateLabel  = $date  ? \Carbon\Carbon::parse($date)->format('d-m-Y') : 'Semua_Tanggal';
        $shiftLabel = $shift ? 'Shift' . $shift : 'Semua_Shift';
        $filename   = "Pemeriksaan_Suhu_RH_{$dateLabel}_{$shiftLabel}.xlsx";

        return Excel::download(new SuhuExport($items, $areaSuhus, $date, $shift), $filename);
    }
}
