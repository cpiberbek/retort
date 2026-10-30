<?php

namespace App\Http\Controllers;

use App\Models\Organoleptik;
use App\Models\Produk;
use App\Models\List_form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use TCPDF;
use App\Models\Mincing;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OrganoleptikController extends Controller
{
    private const LAPORAN_ORGANO = 'Pemeriksaan Organoleptik';

    private const SCORE_KEYS = [
        'penampilan', 'aroma', 'kekenyalan', 'rasa_asin',
        'rasa_gurih', 'rasa_manis', 'rasa_daging', 'rasa_keseluruhan',
    ];

    // ========================= INDEX =========================
    public function index(Request $request)
    {
        if (Auth::user()->isAuditView()) {
            return redirect()->route('organoleptik.audit');
        }

        $search     = $request->input('search');
        $date       = $request->input('date');
        $shift      = $request->input('shift');
        $nama_produk = $request->input('nama_produk');
        $userPlant  = Auth::user()->plant;

        $data = Organoleptik::query()
            ->where('plant', $userPlant)
            ->where('is_audit', false)
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

    public function auditIndex(Request $request)
    {
        if (!Auth::user()->isAuditView()) {
            return redirect()->route('organoleptik.index');
        }

        $search      = $request->input('search');
        $date        = $request->input('date');
        $shift       = $request->input('shift');
        $nama_produk = $request->input('nama_produk');

        $auditedSourceUuids = Organoleptik::where('is_audit', true)
            ->whereNotNull('source_uuid')
            ->pluck('source_uuid')
            ->toArray();

        $data = Organoleptik::query()
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
                $uuids = Mincing::where('kode_produksi', 'like', "%{$search}%")
                    ->limit(200)->pluck('uuid')->toArray();

                $query->where(function ($q) use ($search, $uuids) {
                    $q->where('username', 'like', "%{$search}%")
                        ->orWhere('nama_produk', 'like', "%{$search}%");

                    foreach ($uuids as $u) {
                        $q->orWhere('sensori', 'like', "%{$u}%");
                    }
                });
            })
            ->when($date, fn ($q) => $q->whereDate('date', $date))
            ->when($shift, fn ($q) => $q->where('shift', $shift))
            ->when($nama_produk, fn ($q) => $q->where('nama_produk', $nama_produk))
            ->orderByRaw('COALESCE((SELECT original.date FROM organoleptiks AS original WHERE original.uuid = organoleptiks.source_uuid LIMIT 1), organoleptiks.date) DESC')
            ->orderByRaw('CAST(COALESCE((SELECT original.shift FROM organoleptiks AS original WHERE original.uuid = organoleptiks.source_uuid LIMIT 1), organoleptiks.shift) AS UNSIGNED) DESC')
            ->orderByRaw('COALESCE((SELECT original.created_at FROM organoleptiks AS original WHERE original.uuid = organoleptiks.source_uuid LIMIT 1), organoleptiks.created_at) DESC')
            ->paginate(10)
            ->appends($request->all());

        $allUuids = [];
        foreach ($data as $row) {
            foreach ($this->decodeRows($row->sensori) as $item) {
                if (!empty($item['kode_produksi'])) {
                    $allUuids[] = $item['kode_produksi'];
                }
            }
        }
        $kodeMap = Mincing::whereIn('uuid', array_unique($allUuids))->pluck('kode_produksi', 'uuid');

        return view('form.organoleptik.index_audit', compact('data', 'search', 'date', 'shift', 'nama_produk', 'kodeMap'));
    }

    public function duplicateToAudit(string $uuid)
    {
        abort_if(Auth::user()->hasRole('auditor'), 403);

        $row = Organoleptik::where('uuid', $uuid)
            ->where('plant', Auth::user()->plant)
            ->where('is_audit', false)
            ->firstOrFail();

        if ($existing = $row->auditVersion) {
            return redirect()->route('organoleptik.edit.form', $existing->uuid);
        }

        $clone = $this->createCorrectedAudit($row);

        return redirect()->route('organoleptik.edit.form', $clone->uuid)
            ->with('info', 'Salinan data audit berhasil dibuat. Silakan lakukan penyesuaian.');
    }

    private function createCorrectedAudit(Organoleptik $row): Organoleptik
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
            ->where('laporan', self::LAPORAN_ORGANO)
            ->first();

        abort_if(empty($master?->standar_ref), 422, 'Master auto correct belum diterapkan untuk plant ini.');

        $base = Organoleptik::where('plant', $plant)
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
            return redirect()->route('organoleptik.audit')->with('warning', 'Tidak ada entry di tanggal ini.');
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
            return redirect()->route('organoleptik.audit')
                ->with('info', "0 data baru. {$skipped} data dilewati karena sudah ada audit.");
        }

        $msg = "{$created} data diduplikasi dan disesuaikan sesuai master. {$skipped} dilewati karena sudah ada.";
        if ($failed) $msg .= " {$failed} gagal diproses (cek log).";

        return redirect()->route('organoleptik.audit')->with($failed ? 'warning' : 'success', $msg);
    }

    /**
     * sensori bisa: array, JSON string, atau JSON yang di-encode dua kali.
     */
    private function decodeRows($value): array
    {
        $guard = 0;
        while (is_string($value) && $guard++ < 3) {
            $value = json_decode($value, true);
        }

        return is_array($value) ? array_values($value) : [];
    }

    private function autoCorrectFromMaster(Organoleptik $model): void
    {
        $master = List_form::where('plant', $model->plant)
            ->where('laporan', self::LAPORAN_ORGANO)
            ->first();

        $std = $master?->standar_ref;
        if (is_string($std)) $std = json_decode($std, true);
        if (!is_array($std) || empty($std)) return;

        // hanya parameter yang min & max-nya terisi
        $rules = [];
        foreach (self::SCORE_KEYS as $key) {
            $min = $std[$key]['min'] ?? null;
            $max = $std[$key]['max'] ?? null;
            if ($min === null || $min === '' || $max === null || $max === '') continue;
            $rules[$key] = [(int) $min, (int) $max];
        }
        if (empty($rules)) return;

        $original = $model->sensori;
        $rows     = $this->decodeRows($original);
        if (empty($rows)) return;

        foreach ($rows as &$row) {
            // 1. acak per baris, per parameter (disimpan sebagai string, sama seperti data asli)
            foreach ($rules as $key => [$min, $max]) {
                $row[$key] = (string) mt_rand($min, $max);
            }

            // 2. hitung ulang rata_score: sama dengan JS (nilai > 0 saja, 1 desimal)
            $nilai = [];
            foreach (self::SCORE_KEYS as $key) {
                $v = (float) ($row[$key] ?? 0);
                if ($v > 0) $nilai[] = $v;
            }

            if (count($nilai) > 0) {
                $avg = round(array_sum($nilai) / count($nilai), 1);
                $row['rata_score'] = number_format($avg, 1, '.', '');

                if ($avg >= 1 && $avg <= 1.5) {
                    $row['release'] = 'Tidak Release';
                } elseif ($avg >= 1.6 && $avg <= 3) {
                    $row['release'] = 'Release';
                } else {
                    $row['release'] = '';
                }
            }
        }
        unset($row);

        // pertahankan format penyimpanan asli (string JSON -> tetap string JSON)
        $model->sensori = is_array($original)
            ? $rows
            : json_encode($rows, JSON_UNESCAPED_UNICODE);

        $model->save();
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
            'sensori.*.rasa_keseluruhan' => 'nullable|numeric|between:0,3',
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
            'sensori.*.rasa_keseluruhan' => 'nullable|numeric|between:0,3',
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
            'sensori.*.rasa_keseluruhan' => 'nullable|numeric|between:0,3',
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

        return redirect()->route('organoleptik.index', [
            'page'       => $request->input('page', 1),
            'search'     => $request->input('search'),
            'start_date' => $request->input('start_date'),
            'end_date'   => $request->input('end_date'),
        ])->with('success', '✅ Status Verifikasi Pemeriksaan Organoleptik berhasil diperbarui.');
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

        $noDokumen = List_form::where('plant', $userPlant)
            ->where('laporan', 'Pemeriksaan Organoleptik')
            ->value('no_dokumen');

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
        $pdf->SetMargins(PDF_MARGIN_LEFT, 8, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // Set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // Set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // Set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
            require_once(dirname(__FILE__) . '/lang/eng.php');
            $pdf->setLanguageArray($l);
        }

        // Set font
        $pdf->SetFont('helvetica', '', 10);

        // Add a page
        $pdf->AddPage('L', 'F4'); // Landscape A4

        // Convert the Blade view to HTML
        $html = view('reports.organoleptik', compact('organoleptiks', 'request', 'noDokumen'))->render();

        // Print text using writeHTMLCell()
        $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

        // Close and output PDF document (Inline/Preview)
        $pdf->Output('Pemeriksaan_Organoleptik_' . date('Ymd_His') . '.pdf', 'I');

        exit();
    }
}
