<?php

namespace App\Http\Controllers;

use App\Models\Chamber;
use App\Models\Produk;
use App\Models\Operator;
use App\Models\Mesin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\List_form;
use TCPDF;

class ChamberController extends Controller
{

    private const LAPORAN_CHAMBER = 'Verifikasi Timer Chamber';

    public function index(Request $request)
    {
        if (auth()->user()->isAuditView()) {
            return redirect()->route('chamber.audit');
        }

        $search    = $request->input('search');
        $month     = $request->input('month');
        $date      = $request->input('date');
        $shift     = $request->input('shift');
        $userPlant = Auth::user()->plant;

        if ($month) {
            $date = null;
        }

        if ($date) {
            $month = null;
        }

        $data = Chamber::query()
            ->where('plant', $userPlant)
            ->with('auditVersion')
            ->where('is_audit', false)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
                        ->orWhere('nama_operator', 'like', "%{$search}%");
                });
            })
            ->when($month, function ($query) use ($month) {
                [$year, $monthNumber] = explode('-', $month);

                $query->whereYear('date', $year)
                    ->whereMonth('date', $monthNumber);
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

        return view(
            'form.chamber.index',
            compact(
                'data',
                'search',
                'month',
                'date',
                'shift'
            )
        );
    }

    public function auditIndex(Request $request)
    {
        if (!auth()->user()->isAuditView()) {
            return redirect()->route('chamber.index');
        }

        $search    = $request->input('search');
        $month     = $request->input('month');
        $date      = $request->input('date');
        $shift     = $request->input('shift');
        $userPlant = Auth::user()->plant;

        if ($month) {
            $date = null;
        }

        if ($date) {
            $month = null;
        }

        $data = Chamber::query()
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
                        ->orWhere('nama_operator', 'like', "%{$search}%");
                });
            })
            ->when($month, function ($query) use ($month) {
                [$year, $monthNumber] = explode('-', $month);

                $query->whereYear('date', $year)
                    ->whereMonth('date', $monthNumber);
            })
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->when($shift, function ($query) use ($shift) {
                $query->where('shift', $shift);
            })
            ->orderByRaw('COALESCE((SELECT original.date FROM chambers AS original WHERE original.uuid = chambers.source_uuid LIMIT 1), chambers.date) DESC')
            ->orderByRaw('CAST(COALESCE((SELECT original.shift FROM chambers AS original WHERE original.uuid = chambers.source_uuid LIMIT 1), chambers.shift) AS UNSIGNED) DESC')
            ->orderByRaw('COALESCE((SELECT original.created_at FROM chambers AS original WHERE original.uuid = chambers.source_uuid LIMIT 1), chambers.created_at) DESC')
            ->paginate(10)
            ->appends($request->all());

        return view(
            'form.chamber.index_audit',
            compact(
                'data',
                'search',
                'month',
                'date',
                'shift'
            )
        );
    }

    public function duplicateToAudit($uuid)
    {
        $chamber = Chamber::where('uuid', $uuid)
            ->where('is_audit', false)
            ->firstOrFail();

        if ($audit = $chamber->auditVersion) {
            return redirect()->route('chamber.edit.form', $audit->uuid);
        }

        $audit = $this->createCorrectedAudit($chamber);

        return redirect()->route('chamber.edit.form', $audit->uuid)
            ->with('info', 'Salinan data audit berhasil dibuat. Silakan lakukan penyesuaian.');
    }

    private function createCorrectedAudit(Chamber $chamber): Chamber
    {
        return DB::transaction(function () use ($chamber) {
            $clone = $chamber->copyToAudit();

            $this->autoCorrectFromMaster(
                $clone,
                self::LAPORAN_CHAMBER
            );

            return $clone;
        });
    }

    private function bulkBase(Request $request): array
    {
        abort_if(Auth::user()->hasRole('auditor'), 403);

        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $start = Carbon::parse($request->start_date)->startOfDay();
        $end = Carbon::parse($request->end_date)->startOfDay();

        $plant = Auth::user()->plant;

        $master = List_form::where('plant', $plant)
            ->where('laporan', self::LAPORAN_CHAMBER)
            ->first();

        abort_if(
            empty($master?->standar_ref),
            422,
            'Master auto correct belum diterapkan untuk plant ini.'
        );

        $base = Chamber::where('plant', $plant)
            ->where('is_audit', false)
            ->whereDate('date', '>=', $start->toDateString())
            ->whereDate('date', '<=', $end->toDateString());

        return [$base];
    }

    public function auditBulkPreview(Request $request)
    {
        [$base] = $this->bulkBase($request);

        $total = (clone $base)->count();
        $sudah = (clone $base)->whereHas('auditVersion')->count();

        return response()->json([
            'total' => $total,
            'sudah_ada' => $sudah,
            'akan_dibuat' => $total - $sudah,
        ]);
    }

    public function auditBulkExecute(Request $request)
    {
        [$base] = $this->bulkBase($request);

        $total = (clone $base)->count();

        if ($total === 0) {
            return redirect()->route('chamber.index')
                ->with('warning', 'Tidak ada entry di tanggal ini.');
        }

        $targets = (clone $base)
            ->whereDoesntHave('auditVersion')
            ->get();

        $skipped = $total - $targets->count();
        $created = 0;
        $failed = 0;

        foreach ($targets as $chamber) {
            try {
                $this->createCorrectedAudit($chamber);
                $created++;
            } catch (\Throwable $e) {
                $failed++;
                report($e);
            }
        }

        if ($created === 0 && $failed === 0) {
            return redirect()->route('chamber.index')
                ->with('info', "0 data baru. {$skipped} data dilewati karena sudah ada audit.");
        }

        $msg = "{$created} data diduplikasi dan disesuaikan sesuai master. {$skipped} dilewati karena sudah ada.";

        if ($failed) {
            $msg .= " {$failed} gagal diproses (cek log).";
        }

        return redirect()
            ->route('chamber.index')
            ->with($failed ? 'warning' : 'success', $msg);
    }

    
    private function autoCorrectFromMaster($model, string $laporan): void
    {
        $master = List_form::where('plant', $model->plant)
            ->where('laporan', $laporan)
            ->first();

        $std = $master?->standar_ref;

        if (is_string($std)) {
            $std = json_decode($std, true) ?? [];
        }

        if (!is_array($std) || empty($std)) {
            return;
        }

        $verifikasi = $model->verifikasi;

        if (is_string($verifikasi)) {
            $verifikasi = json_decode($verifikasi, true) ?? [];
        }

        if (!is_array($verifikasi)) {
            return;
        }

        $durasi = [5, 10, 20, 30, 60];

        $fields = [
            'plc_menit',
            'plc_detik',
            'stopwatch_menit',
            'stopwatch_detik',
            'faktor_koreksi',
        ];

        foreach ($durasi as $menit) {
            foreach ($fields as $field) {
                $key = $field . '_' . $menit;

                if (!array_key_exists($key, $std)) {
                    continue;
                }

                [$apply, $value] = $this->acResolve($std[$key], $key);

                if (!$apply) {
                    continue;
                }

                foreach ($verifikasi as $index => $row) {
                    if (is_array($row)) {
                        $verifikasi[$index][$key] = $value;
                    }
                }
            }
        }

        // Terapkan catatan ke setiap baris verifikasi.
        $catatan = $std['catatan'] ?? null;

        if ($catatan !== null && $catatan !== '') {
            foreach ($verifikasi as $index => $row) {
                if (is_array($row)) {
                    $verifikasi[$index]['catatan'] = $catatan;
                }
            }
        }

        $model->verifikasi = $verifikasi;

        // Jika tabel chambers juga memiliki kolom catatan,
        // terapkan nilai master ke kolom tersebut.
        if (
            $catatan !== null &&
            $catatan !== '' &&
            array_key_exists('catatan', $model->getAttributes())
        ) {
            $model->catatan = $catatan;
        }

        $model->save();
    }


    private function acResolve($rule, string $field = ''): array
    {
        if ($this->acIsRange($rule)) {
            return [
                true,
                $this->acRandomize(
                    $rule,
                    str_contains($field, 'menit') || str_contains($field, 'detik')
                ),
            ];
        }

        if (is_array($rule) || $rule === null || $rule === '') {
            return [false, null];
        }

        return [true, $rule];
    }

    private function acIsRange($rule): bool
    {
        return is_array($rule)
            && (
                array_key_exists('min', $rule)
                || array_key_exists('max', $rule)
            );
    }

    private function acRandomize(array $range, bool $integer = false): int|float|null
    {
        if (
            !isset($range['min'], $range['max'])
            || $range['min'] === ''
            || $range['max'] === ''
        ) {
            return null;
        }

        $minValue = (float) $range['min'];
        $maxValue = (float) $range['max'];

        if ($minValue > $maxValue) {
            return null;
        }

        if ($integer) {
            $min = (int) ceil($minValue);
            $max = (int) floor($maxValue);

            if ($min > $max) {
                return null;
            }

            return mt_rand($min, $max);
        }

        $min = (int) round($minValue * 100);
        $max = (int) round($maxValue * 100);

        return round(mt_rand($min, $max) / 100, 2);
    }

    public function exportPdf(Request $request)
    {
        $search    = $request->input('search');
        $date      = $request->input('date');
        $shift     = $request->input('shift');
        $userPlant = Auth::user()->plant;
        $month = $request->input('month');

        // Ambil data tanpa pagination untuk PDF
        $items = Chamber::query()
            ->where('plant', $userPlant)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
                        ->orWhere('nama_operator', 'like', "%{$search}%");
                });
            })
            // ->when($date, function ($query) use ($date) {
            //     $query->whereDate('date', $date);
            // })
            ->when($shift, function ($query) use ($shift) {
                $query->where('shift', $shift);
            })
            ->when($month, function ($query) use ($month) {
                [$year, $month] = explode('-', $month);

                $query->whereYear('date', $year)
                    ->whereMonth('date', $month);
            })
            ->orderBy('date', 'asc')
            ->orderBy('shift', 'asc')
            ->get();

        if (ob_get_length()) {
            ob_end_clean();
        }

        $noDokumen = List_form::where('plant', $userPlant)
            ->where('laporan', 'Verifikasi Timer Chamber')
            ->value('no_dokumen');

        // Setup PDF Landscape A4
        $pdf = new \TCPDF('L', PDF_UNIT, 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle('Laporan Verifikasi Timer Chamber');

        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);

        $pdf->SetMargins(5, 5, 5);
        $pdf->SetAutoPageBreak(TRUE, 5);
        $pdf->SetFont('helvetica', '', 7);

        $pdf->AddPage();

        $html = view('reports.chamber', compact('items', 'request', 'noDokumen'))->render();

        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Output('Laporan_Chamber_' . date('d-m-Y_His') . '.pdf', 'I');
        exit();
    }

    public function create()
    {
        $userPlant = Auth::user()->plant;

        $produks = Produk::where('plant', $userPlant)->get();
        $list_chambers = Mesin::where('plant', $userPlant)
            ->where('jenis_mesin', 'Chamber')
            ->orderBy('nama_mesin')
            ->get(['uuid', 'nama_mesin']);

        $operators = Operator::where('plant', $userPlant)
            ->where('bagian', 'Operator')
            ->orderBy('nama_karyawan')
            ->get();

        return view('form.chamber.create', compact('produks', 'list_chambers', 'operators'));
    }

    public function store(Request $request)
    {
        $username   = Auth::user()->username ?? 'User RTM';
        $userPlant  = Auth::user()->plant;
        $nama_produksi = session()->has('selected_produksi')
            ? \App\Models\User::where('uuid', session('selected_produksi'))->first()->name
            : 'Produksi RTT';

        $request->validate([
            'date'        => 'required|date',
            'shift'       => 'required',
            'nama_operator' => 'required',
            'catatan'         => 'nullable|string',
            'verifikasi'       => 'nullable|array',
        ]);

        $data = $request->only(['date', 'shift', 'nama_operator', 'catatan']);
        $data['username']        = $username;
        $data['plant']           = $userPlant;
        $data['status_operator']     = "1";
        $data['tgl_update_operator'] = now()->addHour();
        $data['nama_produksi']       = $nama_produksi;
        $data['status_produksi']     = "1";
        $data['tgl_update_produksi'] = now()->addHour();
        $data['status_spv']          = "0";
        $data['verifikasi']         = json_encode($request->input('verifikasi', []), JSON_UNESCAPED_UNICODE);

        Chamber::create($data);

        return redirect()->route('chamber.index')
            ->with('success', 'Verifikasi Timer Chamber berhasil disimpan');
    }

    public function update(string $uuid)
    {
        $chamber = Chamber::where('uuid', $uuid)->firstOrFail();
        $userPlant = Auth::user()->plant;

        $produks = Produk::where('plant', $userPlant)->get();
        $list_chambers = Mesin::where('plant', $userPlant)
            ->where('jenis_mesin', 'Chamber')
            ->orderBy('nama_mesin')
            ->get(['uuid', 'nama_mesin']);
        $operators = Operator::where('plant', $userPlant)
            ->where('bagian', 'Operator')
            ->orderBy('nama_karyawan')
            ->get();

        $chamberData = !empty($chamber->verifikasi) ? json_decode($chamber->verifikasi, true) : [];

        return view('form.chamber.update', compact('chamber', 'produks', 'chamberData', 'list_chambers', 'operators'));
    }

    public function update_qc(Request $request, string $uuid)
    {
        $chamber = Chamber::where('uuid', $uuid)->firstOrFail();
        $username_updated = Auth::user()->username ?? 'User RTM';

        $request->validate([
            'date'        => 'required|date',
            'shift'       => 'required',
            'nama_operator' => 'required',
            'catatan'         => 'nullable|string',
            'verifikasi'       => 'nullable|array',
        ]);

        $verifikasi = $request->input('verifikasi', []);

        $data = [
            'date' => $request->date,
            'shift' => $request->shift,
            'nama_operator' => $request->nama_operator,
            'catatan' => $request->catatan,
            'username_updated' => $username_updated,
            'verifikasi' => json_encode($verifikasi, JSON_UNESCAPED_UNICODE),
        ];

        $data['tgl_update_operator'] = now()->addHour();
        $data['tgl_update_produksi'] = now()->addHour();

        $chamber->update($data);

        return redirect()->route('chamber.index')->with('success', 'Verifikasi Timer Chamber berhasil diperbarui');
    }

    public function edit(string $uuid)
    {
        $chamber = Chamber::where('uuid', $uuid)->firstOrFail();
        $userPlant = Auth::user()->plant;

        $produks = Produk::where('plant', $userPlant)->get();
        $list_chambers = Mesin::where('plant', $userPlant)
            ->where('jenis_mesin', 'Chamber')
            ->orderBy('nama_mesin')
            ->get(['uuid', 'nama_mesin']);
        $operators = Operator::where('plant', $userPlant)
            ->where('bagian', 'Operator')
            ->orderBy('nama_karyawan')
            ->get();

        $chamberData = !empty($chamber->verifikasi)
            ? (is_string($chamber->verifikasi)
                ? json_decode($chamber->verifikasi, true)
                : $chamber->verifikasi)
            : [];

        return view('form.chamber.edit', compact('chamber', 'produks', 'chamberData', 'list_chambers', 'operators'));
    }

    public function edit_spv(Request $request, string $uuid)
    {
        $chamber = Chamber::where('uuid', $uuid)->firstOrFail();
        $username_updated = Auth::user()->username ?? 'User RTM';

        $request->validate([
            'date'        => 'required|date',
            'shift'       => 'required',
            'nama_operator' => 'required',
            'catatan'         => 'nullable|string',
            'verifikasi'       => 'nullable|array',
        ]);

        $verifikasi = $request->input('verifikasi', []);

        $data = [
            'date' => $request->date,
            'shift' => $request->shift,
            'nama_operator' => $request->nama_operator,
            'catatan' => $request->catatan,
            'username_updated' => $username_updated,
            'verifikasi' => json_encode($verifikasi, JSON_UNESCAPED_UNICODE),
        ];

        $data['tgl_update_operator'] = now()->addHour();
        $data['tgl_update_produksi'] = now()->addHour();

        $chamber->update($data);

        return redirect()->route('chamber.index')->with('success', 'Verifikasi Timer Chamber berhasil diperbarui');
    }

    public function verification(Request $request)
    {
        $search     = $request->input('search');
        $date = $request->input('date');
        $userPlant  = Auth::user()->plant;

        $data = Chamber::query()
            ->where('plant', $userPlant)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
                        ->orWhere('no_chamber', 'like', "%{$search}%");
                });
            })
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->all());

        return view('form.chamber.verification', compact('data', 'search', 'date'));
    }

    public function updateVerification(Request $request, $uuid)
    {
        $request->validate([
            'status_spv' => 'required|in:1,2',
            'catatan_spv' => 'nullable|string|max:255',
        ]);

        $chamber = Chamber::where('uuid', $uuid)->firstOrFail();

        $chamber->update([
            'status_spv' => $request->status_spv,
            'catatan_spv' => $request->catatan_spv,
            'nama_spv' => Auth::user()->username,
            'tgl_update_spv' => now(),
        ]);

        return redirect()->route('chamber.index', [
            'page' => $request->input('page', 1),
            'search' => $request->input('search'),
            'date' => $request->input('date'),
        ])->with('success', 'Status Verifikasi Timer Chamber berhasil diperbarui.');
    }

    public function destroy($uuid)
    {
        $chamber = Chamber::where('uuid', $uuid)->firstOrFail();
        $chamber->delete();
        return redirect()->route('chamber.index')->with('success', 'Chamber berhasil dihapus');
    }

    public function recyclebin()
    {
        $chamber = Chamber::onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->paginate(10);

        return view('form.chamber.recyclebin', compact('chamber'));
    }
    public function restore($uuid)
    {
        $chamber = Chamber::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $chamber->restore();

        return redirect()->route('chamber.recyclebin')
            ->with('success', 'Data berhasil direstore.');
    }
    public function deletePermanent($uuid)
    {
        $chamber = Chamber::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $chamber->forceDelete();

        return redirect()->route('chamber.recyclebin')
            ->with('success', 'Data berhasil dihapus permanen.');
    }
}
