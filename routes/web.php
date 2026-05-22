<?php

use Illuminate\Support\Facades\Route;
use Spatie\LaravelPdf\Facades\Pdf;
use App\Http\Controllers\{
    AuthController,
    UserController,
    DashboardController,
    HaloController,
    ProdukController,
    MesinController,
    OperatorController,
    List_formController,
    // EngineerController,
    SupplierController,
    // Supplier_rmController,
    // KoordinatorController,
    // List_chamberController,
    Area_hygieneController,
    Area_suhuController,
    Area_sanitasiController,
    DepartemenController,
    PlantController,
    ProduksiController,
    GmpController,
    PvdcController,
    MagnetTrapController,
    RawMaterialInspectionController,
    PackagingInspectionController,
    PemeriksaanRetainController,
    LoadingProdukController,
    DispositionController,
    BeritaAcaraController,
    PemeriksaanKekuatanMagnetTrapController,
    PenyimpanganKualitasController,
    Labelisasi_pvdcController,
    MincingController,
    MetalController,
    StuffingController,
    SampelController,
    OrganoleptikController,
    KlorinController,
    PackingController,
    SamplingController,
    KartonController,
    TimbanganController,
    ThermometerController,
    ChamberController,
    WireController,
    Sampling_fgController,
    PemasakanController,
    PrepackingController,
    WashingController,
    PemusnahanController,
    Retain_rteController,
    Release_packing_rteController,
    Pemasakan_rteController,
    Release_packingController,
    SuhuController,
    SanitasiController,
    LookupController,
    WithdrawlController,
    TraceabilityController,
    RecallController,
    PermissionController,
    RoleController,
    MasterRawMaterialController,
    RetortController,
    MasterPremixController
};

require_once __DIR__ . '/helpers/routeHelper.php';

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/get-inspections', [MincingController::class, 'getInspections']);
Route::get('/lookup/batch/{nama_produk}', [MagnetTrapController::class, 'getBatch']);
Route::get('/retort', [RetortController::class, 'index'])->name('retort.index');
Route::post('/retort/cari', [RetortController::class, 'cari'])->name('retort.cari');


Route::resource('premix', MasterPremixController::class);

Route::middleware('auth')->group(function () {
    Route::resource('user', UserController::class);
    Route::prefix('access')->group(function () {
        Route::resource('permissions', PermissionController::class);
        Route::resource('roles', RoleController::class)
            ->middleware([
                'permission:can access edit button'
            ]);;
        Route::get('roles/{role}/manage-access', [RoleController::class, 'manageAccess'])
            ->middleware('permission:can access edit button')
            ->name('roles.manageAccess');
        Route::post('roles/{role}/manage-access', [RoleController::class, 'saveAccess'])
            ->middleware('permission:can access edit button')
            ->name('roles.saveAccess');
    });
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/logout', function () {
        // Kembalikan mereka ke dashboard atau halaman sebelumnya
        return redirect('/dashboard');

        // ATAU: Anda juga bisa menonaktifkannya dengan menampilkan halaman 404
        // abort(404);
    });
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/set-produksi', [DashboardController::class, 'setProduksi'])->name('set.produksi');

    // Halo test
    Route::get('/halo', [HaloController::class, 'index']);


    /*MASTER DATA*/
    // Departemen
    Route::resource('departemen', DepartemenController::class)->parameters([
        'departemen' => 'uuid'
    ]);

    // Plant
    Route::resource('plant', PlantController::class)->parameters([
        'plant' => 'uuid'
    ]);

    // Produk
    Route::resource('produk', ProdukController::class)->parameters([
        'produk' => 'uuid'
    ]);

    // Mesin
    Route::resource('mesin', MesinController::class)->parameters([
        'mesin' => 'uuid'
    ]);

    // Produksi (Karyawan Produksi)
    Route::resource('produksi', ProduksiController::class)->parameters([
        'produksi' => 'uuid'
    ]);

    // Operator
    Route::resource('operator', OperatorController::class)->parameters([
        'operator' => 'uuid'
    ]);

    // Supplier
    Route::resource('supplier', SupplierController::class)->parameters([
        'supplier' => 'uuid'
    ]);

    // Area
    // Route::resource('area_hygiene', Area_hygieneController::class)->parameters([
    //     'area_hygiene' => 'uuid'
    // ]);
    // 1. Daftarkan custom route untuk Recycle Bin DI ATAS Route::resource
    Route::get('area_hygiene/recyclebin', [Area_hygieneController::class, 'recyclebin'])->name('area_hygiene.recyclebin');
    Route::post('area_hygiene/{uuid}/restore', [Area_hygieneController::class, 'restore'])->name('area_hygiene.restore');
    Route::delete('area_hygiene/{uuid}/force-delete', [Area_hygieneController::class, 'deletePermanent'])->name('area_hygiene.deletePermanent');
    Route::resource('area_hygiene', Area_hygieneController::class)
        ->except(['show']);


    // Area Suhu
    Route::resource('area_suhu', Area_suhuController::class)->parameters([
        'area_suhu' => 'uuid'
    ]);

    // Area Sanitasi
    Route::resource('area_sanitasi', Area_sanitasiController::class)->parameters([
        'area_sanitasi' => 'uuid'
    ]);

    // UMUM
    Route::get('/lookup/batch/{nama_produk}', [LookupController::class, 'getBatchByProduk'])->name('lookup.batch');
    Route::get('/lookup/batch-packing/{nama_produk}', [LookupController::class, 'getAllBatchByProduk'])->name('lookup.batch_packing');

    // Form QC
    Route::resource('list_form', List_formController::class)->parameters([
        'list_form' => 'uuid'
    ]);

    Route::get('/ajax/search-batch-mincing', [MagnetTrapController::class, 'searchBatchMincing'])->name('ajax.search.batch');
    Route::get('/metal/export-excel', [MetalController::class, 'exportExcel'])->name('metal.exportExcel');
    Route::get('checklistmagnettrap/update-form/{checklistmagnettrap}', [MagnetTrapController::class, 'showUpdateForm'])
        ->name('checklistmagnettrap.showUpdateForm');
    Route::get('checklistmagnettrap/verification', [MagnetTrapController::class, 'showVerificationPage'])->name('checklistmagnettrap.verification');
    Route::put('checklistmagnettrap/{uuid}/verify', [MagnetTrapController::class, 'verify'])->name('checklistmagnettrap.verify');
    Route::get('checklistmagnettrap/export-pdf', [MagnetTrapController::class, 'exportPdf'])->name('checklistmagnettrap.exportPdf');
    Route::get('checklistmagnettrap/export-excel', [MagnetTrapController::class, 'exportExcel'])->name('checklistmagnettrap.exportExcel');
    // Route::resource('checklistmagnettrap', MagnetTrapController::class);
    Route::get(
        'checklistmagnettrap/recycle-bin',
        [MagnetTrapController::class, 'recyclebin']
    )->name('checklistmagnettrap.recyclebin');

    Route::post(
        'checklistmagnettrap/recycle-bin/{uuid}/restore',
        [MagnetTrapController::class, 'restore']
    )->name('checklistmagnettrap.restore');

    Route::delete(
        'checklistmagnettrap/recycle-bin/{uuid}/force-delete',
        [MagnetTrapController::class, 'deletePermanent']
    )->name('checklistmagnettrap.deletePermanent');


    Route::resource('checklistmagnettrap', MagnetTrapController::class);


    Route::get('/inspections/{inspection}/form-update', [RawMaterialInspectionController::class, 'showUpdateForm'])
        ->name('inspections.form_update');
    Route::get('/inspections/verification', [RawMaterialInspectionController::class, 'showVerificationPage'])
        ->name('inspections.verification');

    // RUTE BARU: Memproses modal verifikasi Packaging
    Route::put('/inspections/verify/{inspection}', [RawMaterialInspectionController::class, 'verify'])->name('inspections.verify');
    Route::get('/inspections/export-pdf', [RawMaterialInspectionController::class, 'exportPdf'])->name('inspections.exportPdf');
    Route::resource('inspections', RawMaterialInspectionController::class);

    // Packaging Inspections
    Route::get('/packaging-inspections/verification', [PackagingInspectionController::class, 'showVerificationList'])
        ->name('packaging-inspections.verification');

    // RUTE BARU: Memproses modal verifikasi Packaging
    Route::put('/packaging-inspections/verify/{inspection}', [PackagingInspectionController::class, 'verify'])->name('packaging-inspections.verify');
    Route::get('/packaging-inspections/edit-for-update/{packagingInspection}', [PackagingInspectionController::class, 'editForUpdate'])->name('packaging-inspections.edit-for-update');
    Route::get('/packaging-inspections/export-pdf', [PackagingInspectionController::class, 'exportPdf'])->name('packaging-inspections.exportPdf');
    // Route::resource('packaging-inspections', PackagingInspectionController::class);
    // RUTE BARU: Menampilkan halaman daftar verifikasi Packaging
    Route::get('packaging-inspections/{packagingInspection}/update-view', [PackagingInspectionController::class, 'editForUpdate'])->name('packaging-inspections.edit-for-update');
    Route::get('packaging-inspections/verification', [PackagingInspectionController::class, 'showVerificationList'])
        ->name('packaging-inspections.verification');

    // RUTE BARU: Memproses modal verifikasi Packaging
    Route::put('packaging-inspections/verify/{inspection}', [PackagingInspectionController::class, 'verify'])->name('packaging-inspections.verify');
    // Route::resource('packaging-inspections', PackagingInspectionController::class);

    Route::get(
        'packaging-inspections/recycle-bin',
        [PackagingInspectionController::class, 'recyclebin']
    )->name('packaging-inspections.recyclebin');

    Route::post(
        'packaging-inspections/recycle-bin/{uuid}/restore',
        [PackagingInspectionController::class, 'restore']
    )->name('packaging-inspections.restore');

    Route::delete(
        'packaging-inspections/recycle-bin/{uuid}/force-delete',
        [PackagingInspectionController::class, 'deletePermanent']
    )->name('packaging-inspections.deletePermanent');

    Route::resource('packaging-inspections', PackagingInspectionController::class)
        ->names('packaging-inspections');

    Route::get('pemeriksaan-retain/{pemeriksaanRetain}/update-view', [PemeriksaanRetainController::class, 'editForUpdate'])
        ->name('pemeriksaan_retain.edit-for-update');
    Route::get('pemeriksaan-retain/verification', [PemeriksaanRetainController::class, 'showVerificationPage'])
        ->name('pemeriksaan_retain.verification');
    Route::put('pemeriksaan-retain/{pemeriksaanRetain}/verify', [PemeriksaanRetainController::class, 'submitVerification'])
        ->name('pemeriksaan_retain.verify');
    Route::get('pemeriksaan-retain/export-pdf', [PemeriksaanRetainController::class, 'exportPdf'])->name('pemeriksaan_retain.exportPdf');

    Route::get(
        'pemeriksaan_retain/recycle-bin',
        [PemeriksaanRetainController::class, 'recyclebin']
    )->name('pemeriksaan_retain.recyclebin');

    Route::post(
        'pemeriksaan_retain/recycle-bin/{uuid}/restore',
        [PemeriksaanRetainController::class, 'restore']
    )->name('pemeriksaan_retain.restore');

    Route::delete(
        'pemeriksaan_retain/recycle-bin/{uuid}/force-delete',
        [PemeriksaanRetainController::class, 'deletePermanent']
    )->name('pemeriksaan_retain.deletePermanent');

    Route::resource('pemeriksaan_retain', PemeriksaanRetainController::class)
        ->names('pemeriksaan_retain');


    Route::get('loading-produks/{loadingProduk}/update', [LoadingProdukController::class, 'updateDetails'])
        ->name('loading-produks.edit-details');

    Route::put('loading-produks/{loadingProduk}/update', [LoadingProdukController::class, 'update'])
        ->name('loading-produks.update-custom');
    Route::get('loading-produks/verification', [LoadingProdukController::class, 'showVerification'])
        ->name('loading-produks.verification');
    Route::put('loading-produks/{uuid}/verify', [LoadingProdukController::class, 'verify'])
        ->name('loading-produks.verify');
    Route::get('loading-produks/export-pdf', [LoadingProdukController::class, 'exportPdf'])->name('loading-produks.exportPdf');
    Route::get(
        'loading-produks/recycle-bin',
        [LoadingProdukController::class, 'recyclebin']
    )->name('loading-produks.recyclebin');

    Route::post(
        'loading-produks/recycle-bin/{uuid}/restore',
        [LoadingProdukController::class, 'restore']
    )->name('loading-produks.restore');

    Route::delete(
        'loading-produks/recycle-bin/{uuid}/force-delete',
        [LoadingProdukController::class, 'deletePermanent']
    )->name('loading-produks.deletePermanent');
    // Route::resource('loading-produks', LoadingProdukController::class)
    // ->names('loading-produks');

    Route::get(
        'loading-produks/recycle-bin',
        [LoadingProdukController::class, 'recyclebin']
    )->name('loading-produks.recyclebin');

    Route::post(
        'loading-produks/recycle-bin/{uuid}/restore',
        [LoadingProdukController::class, 'restore']
    )->name('loading-produks.restore');

    Route::delete(
        'loading-produks/recycle-bin/{uuid}/force-delete',
        [LoadingProdukController::class, 'deletePermanent']
    )->name('loading-produks.deletePermanent');

    Route::resource('loading-produks', LoadingProdukController::class)
        ->names('loading-produks');

    // Route::resource('loading-produks', LoadingProdukController::class);

    Route::get('/dispositions/{disposition}/update-form', [DispositionController::class, 'showUpdateForm'])
        ->name('dispositions.update_form');
    Route::get('dispositions-verification', [DispositionController::class, 'verification'])
        ->name('dispositions.verification');

    Route::put('dispositions-verify/{disposition:uuid}', [DispositionController::class, 'verify'])
        ->name('dispositions.verify');
    Route::get('/dispositions/export-pdf', [DispositionController::class, 'exportPdf'])->name('dispositions.exportPdf');
    // Route::resource('dispositions', DispositionController::class);

    Route::get(
        'dispositions/recycle-bin',
        [DispositionController::class, 'recyclebin']
    )->name('dispositions.recyclebin');

    Route::post(
        'dispositions/recycle-bin/{uuid}/restore',
        [DispositionController::class, 'restore']
    )->name('dispositions.restore');

    Route::delete(
        'dispositions/recycle-bin/{uuid}/force-delete',
        [DispositionController::class, 'deletePermanent']
    )->name('dispositions.deletePermanent');

    Route::resource('dispositions', DispositionController::class)
        ->names('dispositions');

    Route::get('/berita-acara/{beritaAcara}/update-form', [BeritaAcaraController::class, 'showUpdateForm'])
        ->name('berita-acara.update_form');

    Route::prefix('berita-acara-verification')->name('berita-acara.')->group(function () {
        Route::get('qc-supervisor', [BeritaAcaraController::class, 'verificationSpv'])
            ->name('verification.spv');
        Route::post('{beritaAcara}/verify-spv', [BeritaAcaraController::class, 'verifySpv'])
            ->name('verify.spv');
    });
    Route::get('/berita-acara/export-pdf', [BeritaAcaraController::class, 'exportPdf'])->name('berita-acara.exportPdf');
    // Route::resource('berita-acara', BeritaAcaraController::class);

    Route::get(
        'berita-acara/recycle-bin',
        [BeritaAcaraController::class, 'recyclebin']
    )->name('berita-acara.recyclebin');

    Route::post(
        'berita-acara/recycle-bin/{uuid}/restore',
        [BeritaAcaraController::class, 'restore']
    )->name('berita-acara.restore');

    Route::delete(
        'berita-acara/recycle-bin/{uuid}/force-delete',
        [BeritaAcaraController::class, 'deletePermanent']
    )->name('berita-acara.deletePermanent');

    Route::resource('berita-acara', BeritaAcaraController::class)
        ->names('berita-acara');


    Route::get(
        '/pemeriksaan-kekuatan-magnet-trap/{pemeriksaanKekuatanMagnetTrap}/update-form',
        [PemeriksaanKekuatanMagnetTrapController::class, 'showUpdateForm']
    )
        ->name('pemeriksaan-kekuatan-magnet-trap.update_form');
    Route::prefix('pemeriksaan-kekuatan-magnet-trap-verification')->name('pemeriksaan-kekuatan-magnet-trap.')->group(function () {
        Route::get('spv', [PemeriksaanKekuatanMagnetTrapController::class, 'verificationSpv'])
            ->name('verification.spv');

        // {pemeriksaanKekuatanMagnetTrap} harus cocok dengan variabel di method controller
        Route::post('{pemeriksaanKekuatanMagnetTrap}/verify-spv', [PemeriksaanKekuatanMagnetTrapController::class, 'verifySpv'])
            ->name('verify.spv');
    });
    Route::get(
        'pemeriksaan-kekuatan-magnet-trap/recycle-bin',
        [PemeriksaanKekuatanMagnetTrapController::class, 'recyclebin']
    )->name('pemeriksaan-kekuatan-magnet-trap.recyclebin');

    Route::post(
        'pemeriksaan-kekuatan-magnet-trap/recycle-bin/{uuid}/restore',
        [PemeriksaanKekuatanMagnetTrapController::class, 'restore']
    )->name('pemeriksaan-kekuatan-magnet-trap.restore');

    Route::delete(
        'pemeriksaan-kekuatan-magnet-trap/recycle-bin/{uuid}/force-delete',
        [PemeriksaanKekuatanMagnetTrapController::class, 'deletePermanent']
    )->name('pemeriksaan-kekuatan-magnet-trap.deletePermanent');

    Route::resource('pemeriksaan-kekuatan-magnet-trap', PemeriksaanKekuatanMagnetTrapController::class)
        ->names('pemeriksaan-kekuatan-magnet-trap');

    Route::get(
        '/penyimpangan-kualitas/{penyimpanganKualitas}/update-form',
        [PenyimpanganKualitasController::class, 'showUpdateForm']
    )
        ->name('penyimpangan-kualitas.update_form');
    Route::prefix('penyimpangan-kualitas-verification')->name('penyimpangan-kualitas.')->group(function () {

        // Rute untuk "Diketahui Oleh"
        Route::get('diketahui', [PenyimpanganKualitasController::class, 'verificationDiketahui'])
            ->name('verification.diketahui');
        Route::post('{penyimpanganKualitas}/verify-diketahui', [PenyimpanganKualitasController::class, 'verifyDiketahui'])
            ->name('verify.diketahui');

        // Rute untuk "Disetujui Oleh"
        Route::get('disetujui', [PenyimpanganKualitasController::class, 'verificationDisetujui'])
            ->name('verification.disetujui');
        Route::post('{penyimpanganKualitas}/verify-disetujui', [PenyimpanganKualitasController::class, 'verifyDisetujui'])
            ->name('verify.disetujui');
    });
    Route::get('/penyimpangan-kualitas/export-pdf', [PenyimpanganKualitasController::class, 'exportPdf'])->name('penyimpangan-kualitas.exportPdf');

    Route::get(
        'penyimpangan-kualitas/recycle-bin',
        [PenyimpanganKualitasController::class, 'recyclebin']
    )->name('penyimpangan-kualitas.recyclebin');

    Route::post(
        'penyimpangan-kualitas/recycle-bin/{uuid}/restore',
        [PenyimpanganKualitasController::class, 'restore']
    )->name('penyimpangan-kualitas.restore');

    Route::delete(
        'penyimpangan-kualitas/recycle-bin/{uuid}/force-delete',
        [PenyimpanganKualitasController::class, 'deletePermanent']
    )->name('penyimpangan-kualitas.deletePermanent');

    Route::resource('penyimpangan-kualitas', PenyimpanganKualitasController::class)->parameters(['penyimpangan-kualitas' => 'penyimpanganKualitas']);

    /* ===============================
   WITHDRAWL
   ================================ */
    Route::resource('withdrawl', WithdrawlController::class)
        ->parameters(['withdrawl' => 'uuid'])
        ->except(['show']);

    Route::get('withdrawl/recycle-bin', [WithdrawlController::class, 'recyclebin'])
        ->name('withdrawl.recyclebin');

    Route::post('withdrawl/{uuid}/restore', [WithdrawlController::class, 'restore'])
        ->name('withdrawl.restore');

    Route::delete('withdrawl/{uuid}/delete-permanent', [WithdrawlController::class, 'deletePermanent'])
        ->name('withdrawl.deletePermanent');

    Route::get('withdrawl/export', [WithdrawlController::class, 'export'])
        ->name('withdrawl.export');

    Route::put('withdrawl/{uuid}/update-verification', [WithdrawlController::class, 'updateVerification'])
        ->name('withdrawl.updateVerification');

    Route::put('withdrawl/verify-manager/{uuid}', [WithdrawlController::class, 'updateApproval'])
        ->name('withdrawl.updateApproval');


    /* ===============================
   TRACEABILITY
   ================================ */
    Route::resource('traceability', TraceabilityController::class)
        ->parameters(['traceability' => 'uuid'])
        ->except(['show']);

    Route::get('traceability/recycle-bin', [TraceabilityController::class, 'recyclebin'])
        ->name('traceability.recyclebin');

    Route::post('traceability/{uuid}/restore', [TraceabilityController::class, 'restore'])
        ->name('traceability.restore');

    Route::delete('traceability/{uuid}/delete-permanent', [TraceabilityController::class, 'deletePermanent'])
        ->name('traceability.deletePermanent');

    Route::get('traceability/export', [TraceabilityController::class, 'export'])
        ->name('traceability.export');

    Route::put('traceability/{uuid}/update-verification', [TraceabilityController::class, 'updateVerification'])
        ->name('traceability.updateVerification');

    Route::put('traceability/verify-manager/{uuid}', [TraceabilityController::class, 'updateApproval'])
        ->name('traceability.updateApproval');


    /* ===============================
   RECALL
   ================================ */
    Route::resource('recall', RecallController::class)
        ->parameters(['recall' => 'uuid'])
        ->except(['show']);

    Route::get('recall/recycle-bin', [RecallController::class, 'recyclebin'])
        ->name('recall.recyclebin');

    Route::post('recall/{uuid}/restore', [RecallController::class, 'restore'])
        ->name('recall.restore');

    Route::delete('recall/{uuid}/delete-permanent', [RecallController::class, 'deletePermanent'])
        ->name('recall.deletePermanent');

    Route::get('recall/export', [RecallController::class, 'export'])
        ->name('recall.export');

    Route::put('recall/{uuid}/update-verification', [RecallController::class, 'updateVerification'])
        ->name('recall.updateVerification');

    Route::put('recall/verify-manager/{uuid}', [RecallController::class, 'updateApproval'])
        ->name('recall.updateApproval');


    /*FORM PUTRI*/
    // Stuffing
    Route::get('/stuffing/export-pdf', [App\Http\Controllers\StuffingController::class, 'exportPdf'])->name('stuffing.exportPdf');
    Route::get('/stuffing', [StuffingController::class, 'index'])->name('stuffing.index');
    Route::get('/stuffing/create', [StuffingController::class, 'create'])->name('stuffing.create');
    Route::post('/stuffing', [StuffingController::class, 'store'])->name('stuffing.store');
    Route::get('/stuffing/update/{uuid}', [StuffingController::class, 'update'])->name('stuffing.update.form');
    Route::put('/stuffing/update_qc/{uuid}', [StuffingController::class, 'update_qc'])->name('stuffing.update_qc');
    Route::get('/stuffing/edit/{uuid}', [StuffingController::class, 'edit'])->name('stuffing.edit.form');
    Route::put('/stuffing/edit_spv/{uuid}', [StuffingController::class, 'edit_spv'])->name('stuffing.edit_spv');
    Route::get('/stuffing/verification', [StuffingController::class, 'verification'])->name('stuffing.verification');
    Route::put('/stuffing/verification/{uuid}', [StuffingController::class, 'updateVerification'])
        ->name('stuffing.verification.update');
    Route::delete('/stuffing/{uuid}', [StuffingController::class, 'destroy'])->name('stuffing.destroy');

    // Wire
    Route::get('/wire/export-pdf', [WireController::class, 'exportPdf'])->name('wire.exportPdf');
    Route::get('/wire', [WireController::class, 'index'])->name('wire.index');
    Route::get('/wire/create', [WireController::class, 'create'])->name('wire.create');
    Route::post('/wire', [WireController::class, 'store'])->name('wire.store');
    Route::get('/wire/update/{uuid}', [WireController::class, 'update'])->name('wire.update.form');
    Route::put('/wire/update_qc/{uuid}', [WireController::class, 'update_qc'])->name('wire.update_qc');
    Route::get('/wire/edit/{uuid}', [WireController::class, 'edit'])->name('wire.edit.form');
    Route::put('/wire/edit_spv/{uuid}', [WireController::class, 'edit_spv'])->name('wire.edit_spv');
    Route::get('/wire/verification', [WireController::class, 'verification'])->name('wire.verification');
    Route::put('/wire/verification/{uuid}', [WireController::class, 'updateVerification'])
        ->name('wire.verification.update');
    Route::delete('/wire/{uuid}', [WireController::class, 'destroy'])->name('wire.destroy');

    // Sampling FG
    Route::get('/get-palet', [App\Http\Controllers\Sampling_fgController::class, 'getPalet'])->name('get.palet');
    Route::get('/sampling_fg/export-pdf', [Sampling_fgController::class, 'exportPdf'])->name('sampling_fg.exportPdf');
    Route::get('/get-jumlah-box', [App\Http\Controllers\sampling_fgController::class, 'getJumlahBox'])->name('get.jumlah.box');
    Route::get('/sampling_fg', [Sampling_fgController::class, 'index'])->name('sampling_fg.index');
    Route::get('/sampling_fg/create', [Sampling_fgController::class, 'create'])->name('sampling_fg.create');
    Route::post('/sampling_fg', [Sampling_fgController::class, 'store'])->name('sampling_fg.store');
    Route::get('/sampling_fg/update/{uuid}', [Sampling_fgController::class, 'update'])->name('sampling_fg.update.form');
    Route::put('/sampling_fg/update_qc/{uuid}', [Sampling_fgController::class, 'update_qc'])->name('sampling_fg.update_qc');
    Route::get('/sampling_fg/edit/{uuid}', [Sampling_fgController::class, 'edit'])->name('sampling_fg.edit.form');
    Route::put('/sampling_fg/edit_spv/{uuid}', [Sampling_fgController::class, 'edit_spv'])->name('sampling_fg.edit_spv');
    Route::get('/sampling_fg/verification', [Sampling_fgController::class, 'verification'])->name('sampling_fg.verification');
    Route::put('/sampling_fg/verification/{uuid}', [Sampling_fgController::class, 'updateVerification'])
        ->name('sampling_fg.verification.update');
    Route::delete('/sampling_fg/{uuid}', [Sampling_fgController::class, 'destroy'])->name('sampling_fg.destroy');


    // Chamber
    Route::get('/chamber/export-pdf', [ChamberController::class, 'exportPdf'])->name('chamber.exportPdf');
    Route::get('/chamber', [ChamberController::class, 'index'])->name('chamber.index');
    Route::get('/chamber/create', [ChamberController::class, 'create'])->name('chamber.create');
    Route::post('/chamber', [ChamberController::class, 'store'])->name('chamber.store');
    Route::get('/chamber/update/{uuid}', [ChamberController::class, 'update'])->name('chamber.update.form');
    Route::put('/chamber/update_qc/{uuid}', [ChamberController::class, 'update_qc'])->name('chamber.update_qc');
    Route::get('/chamber/edit/{uuid}', [ChamberController::class, 'edit'])->name('chamber.edit.form');
    Route::put('/chamber/edit_spv/{uuid}', [ChamberController::class, 'edit_spv'])->name('chamber.edit_spv');
    Route::get('/chamber/verification', [ChamberController::class, 'verification'])->name('chamber.verification');
    Route::put('/chamber/verification/{uuid}', [ChamberController::class, 'updateVerification'])
        ->name('chamber.verification.update');
    Route::delete('/chamber/{uuid}', [ChamberController::class, 'destroy'])->name('chamber.destroy');

    // Timbangan
    Route::get('/timbangan', [TimbanganController::class, 'index'])->name('timbangan.index');
    Route::get('/timbangan/create', [TimbanganController::class, 'create'])->name('timbangan.create');
    Route::post('/timbangan', [TimbanganController::class, 'store'])->name('timbangan.store');
    Route::get('/timbangan/update/{uuid}', [TimbanganController::class, 'update'])->name('timbangan.update.form');
    Route::put('/timbangan/update_qc/{uuid}', [TimbanganController::class, 'update_qc'])->name('timbangan.update_qc');
    Route::get('/timbangan/edit/{uuid}', [TimbanganController::class, 'edit'])->name('timbangan.edit.form');
    Route::put('/timbangan/edit_spv/{uuid}', [TimbanganController::class, 'edit_spv'])->name('timbangan.edit_spv');
    Route::get('/timbangan/verification', [TimbanganController::class, 'verification'])->name('timbangan.verification');
    Route::put('/timbangan/verification/{uuid}', [TimbanganController::class, 'updateVerification'])
        ->name('timbangan.verification.update');
    Route::get('/timbangan/export-pdf', [TimbanganController::class, 'exportPdf'])->name('timbangan.exportPdf');
    Route::delete('/timbangan/{uuid}', [TimbanganController::class, 'destroy'])->name('timbangan.destroy');

    // Thermometer
    Route::get('/thermometer', [ThermometerController::class, 'index'])->name('thermometer.index');
    Route::get('/thermometer/create', [ThermometerController::class, 'create'])->name('thermometer.create');
    Route::post('/thermometer', [ThermometerController::class, 'store'])->name('thermometer.store');
    Route::get('/thermometer/update/{uuid}', [ThermometerController::class, 'update'])->name('thermometer.update.form');
    Route::put('/thermometer/update_qc/{uuid}', [ThermometerController::class, 'update_qc'])->name('thermometer.update_qc');
    Route::get('/thermometer/edit/{uuid}', [ThermometerController::class, 'edit'])->name('thermometer.edit.form');
    Route::put('/thermometer/edit_spv/{uuid}', [ThermometerController::class, 'edit_spv'])->name('thermometer.edit_spv');
    Route::get('/thermometer/verification', [ThermometerController::class, 'verification'])->name('thermometer.verification');
    Route::put('/thermometer/verification/{uuid}', [ThermometerController::class, 'updateVerification'])
        ->name('thermometer.verification.update');
    Route::get('/thermometer/export-pdf', [ThermometerController::class, 'exportPdf'])->name('thermometer.exportPdf');
    Route::delete('/thermometer/{uuid}', [ThermometerController::class, 'destroy'])->name('thermometer.destroy');

    // Karton
    Route::get('/karton', [KartonController::class, 'index'])->name('karton.index');
    Route::get('/karton/create', [KartonController::class, 'create'])->name('karton.create');
    Route::post('/karton', [KartonController::class, 'store'])->name('karton.store');
    Route::get('/karton/update/{uuid}', [KartonController::class, 'update'])->name('karton.update.form');
    Route::put('/karton/update_qc/{uuid}', [KartonController::class, 'update_qc'])->name('karton.update_qc');
    Route::get('/karton/edit/{uuid}', [KartonController::class, 'edit'])->name('karton.edit.form');
    Route::put('/karton/edit_spv/{uuid}', [KartonController::class, 'edit_spv'])->name('karton.edit_spv');
    Route::get('/karton/verification', [KartonController::class, 'verification'])->name('karton.verification');
    Route::put('/karton/verification/{uuid}', [KartonController::class, 'updateVerification'])
        ->name('karton.verification.update');
    Route::get('/karton/export-pdf', [KartonController::class, 'exportPdf'])->name('karton.exportPdf');
    Route::delete('/karton/{uuid}', [KartonController::class, 'destroy'])->name('karton.destroy');

    // Sampling
    Route::get('/sampling', [SamplingController::class, 'index'])->name('sampling.index');
    Route::get('/sampling/create', [SamplingController::class, 'create'])->name('sampling.create');
    Route::post('/sampling', [SamplingController::class, 'store'])->name('sampling.store');
    Route::get('/sampling/update/{uuid}', [SamplingController::class, 'update'])->name('sampling.update.form');
    Route::put('/sampling/update_qc/{uuid}', [SamplingController::class, 'update_qc'])->name('sampling.update_qc');
    Route::get('/sampling/edit/{uuid}', [SamplingController::class, 'edit'])->name('sampling.edit.form');
    Route::put('/sampling/edit_spv/{uuid}', [SamplingController::class, 'edit_spv'])->name('sampling.edit_spv');
    Route::get('/sampling/verification', [SamplingController::class, 'verification'])->name('sampling.verification');
    Route::put('/sampling/verification/{uuid}', [SamplingController::class, 'updateVerification'])
        ->name('sampling.verification.update');
    Route::get('/sampling/export-pdf', [SamplingController::class, 'exportPdf'])->name('sampling.exportPdf');
    Route::delete('/sampling/{uuid}', [SamplingController::class, 'destroy'])->name('sampling.destroy');

    // Packing
    Route::get('/packing', [PackingController::class, 'index'])->name('packing.index');
    Route::get('/packing/create', [PackingController::class, 'create'])->name('packing.create');
    Route::post('/packing', [PackingController::class, 'store'])->name('packing.store');
    Route::get('/packing/update/{uuid}', [PackingController::class, 'update'])->name('packing.update.form');
    Route::put('/packing/update_qc/{uuid}', [PackingController::class, 'update_qc'])->name('packing.update_qc');
    Route::get('/packing/edit/{uuid}', [PackingController::class, 'edit'])->name('packing.edit.form');
    Route::put('/packing/edit_spv/{uuid}', [PackingController::class, 'edit_spv'])->name('packing.edit_spv');
    Route::get('/packing/verification', [PackingController::class, 'verification'])->name('packing.verification');
    Route::put('/packing/verification/{uuid}', [PackingController::class, 'updateVerification'])
        ->name('packing.verification.update');
    Route::get('/packing/export-pdf', [PackingController::class, 'exportPdf'])->name('packing.exportPdf');
    Route::delete('/packing/{uuid}', [PackingController::class, 'destroy'])->name('packing.destroy');

    // Pengecekan Klorin
    Route::get('/klorin', [KlorinController::class, 'index'])->name('klorin.index');
    Route::get('/klorin/create', [KlorinController::class, 'create'])->name('klorin.create');
    Route::post('/klorin', [KlorinController::class, 'store'])->name('klorin.store');
    Route::get('/klorin/update/{uuid}', [KlorinController::class, 'update'])->name('klorin.update.form');
    Route::put('/klorin/update_qc/{uuid}', [KlorinController::class, 'update_qc'])->name('klorin.update_qc');
    Route::get('/klorin/edit/{uuid}', [KlorinController::class, 'edit'])->name('klorin.edit.form');
    Route::put('/klorin/edit_spv/{uuid}', [KlorinController::class, 'edit_spv'])->name('klorin.edit_spv');
    Route::get('/klorin/verification', [KlorinController::class, 'verification'])->name('klorin.verification');
    Route::put('/klorin/verification/{uuid}', [KlorinController::class, 'updateVerification'])
        ->name('klorin.verification.update');
    Route::get('/klorin/export-pdf', [KlorinController::class, 'exportPdf'])->name('klorin.exportPdf');
    Route::delete('/klorin/{uuid}', [KlorinController::class, 'destroy'])->name('klorin.destroy');

    // Organoleptik
    Route::get('/organoleptik', [OrganoleptikController::class, 'index'])->name('organoleptik.index');
    Route::get('/organoleptik/create', [OrganoleptikController::class, 'create'])->name('organoleptik.create');
    Route::post('/organoleptik', [OrganoleptikController::class, 'store'])->name('organoleptik.store');
    Route::get('/organoleptik/update/{uuid}', [OrganoleptikController::class, 'update'])->name('organoleptik.update.form');
    Route::put('/organoleptik/update_qc/{uuid}', [OrganoleptikController::class, 'update_qc'])->name('organoleptik.update_qc');
    Route::get('/organoleptik/edit/{uuid}', [OrganoleptikController::class, 'edit'])->name('organoleptik.edit.form');
    Route::put('/organoleptik/edit_spv/{uuid}', [OrganoleptikController::class, 'edit_spv'])->name('organoleptik.edit_spv');
    Route::get('/organoleptik/verification', [OrganoleptikController::class, 'verification'])->name('organoleptik.verification');
    Route::put('/organoleptik/verification/{uuid}', [OrganoleptikController::class, 'updateVerification'])
        ->name('organoleptik.verification.update');
    Route::get('/organoleptik/export-pdf', [OrganoleptikController::class, 'exportPdf'])->name('organoleptik.exportPdf');
    Route::delete('/organoleptik/{uuid}', [OrganoleptikController::class, 'destroy'])->name('organoleptik.destroy');

    // Sampel
    Route::get('/sampel', [SampelController::class, 'index'])->name('sampel.index');
    Route::get('/sampel/create', [SampelController::class, 'create'])->name('sampel.create');
    Route::post('/sampel', [SampelController::class, 'store'])->name('sampel.store');
    Route::get('/sampel/update/{uuid}', [SampelController::class, 'update'])->name('sampel.update.form');
    Route::put('/sampel/update_qc/{uuid}', [SampelController::class, 'update_qc'])->name('sampel.update_qc');
    Route::get('/sampel/edit/{uuid}', [SampelController::class, 'edit'])->name('sampel.edit.form');
    Route::put('/sampel/edit_spv/{uuid}', [SampelController::class, 'edit_spv'])->name('sampel.edit_spv');
    Route::get('/sampel/verification', [SampelController::class, 'verification'])->name('sampel.verification');
    Route::put('/sampel/verification/{uuid}', [SampelController::class, 'updateVerification'])
        ->name('sampel.verification.update');
    Route::get('/sampel/export-pdf', [SampelController::class, 'exportPdf'])->name('sampel.exportPdf');
    Route::delete('/sampel/{uuid}', [SampelController::class, 'destroy'])->name('sampel.destroy');

    // PVDC
    Route::get('/pvdc', [PvdcController::class, 'index'])->name('pvdc.index');
    Route::get('/pvdc/create', [PvdcController::class, 'create'])->name('pvdc.create');
    Route::post('/pvdc', [PvdcController::class, 'store'])->name('pvdc.store');
    Route::get('/pvdc/update/{uuid}', [PvdcController::class, 'update'])->name('pvdc.update.form');
    Route::put('/pvdc/update_qc/{uuid}', [PvdcController::class, 'update_qc'])->name('pvdc.update_qc');
    Route::get('/pvdc/edit/{uuid}', [PvdcController::class, 'edit'])->name('pvdc.edit.form');
    Route::put('/pvdc/edit_spv/{uuid}', [PvdcController::class, 'edit_spv'])->name('pvdc.edit_spv');
    Route::get('/pvdc/verification', [PvdcController::class, 'verification'])->name('pvdc.verification');
    Route::put('/pvdc/verification/{uuid}', [PvdcController::class, 'updateVerification'])
        ->name('pvdc.verification.update');
    Route::delete('/pvdc/{uuid}', [PvdcController::class, 'destroy'])->name('pvdc.destroy');
    Route::get('/pvdc/export-pdf', [PvdcController::class, 'exportPdf'])->name('pvdc.exportPdf');

    // Labelisasi PVDC
    Route::get('/labelisasi_pvdc', [Labelisasi_pvdcController::class, 'index'])->name('labelisasi_pvdc.index');
    Route::get('/labelisasi_pvdc/create', [Labelisasi_pvdcController::class, 'create'])->name('labelisasi_pvdc.create');
    Route::post('/labelisasi_pvdc', [Labelisasi_pvdcController::class, 'store'])->name('labelisasi_pvdc.store');
    Route::get('/labelisasi_pvdc/update/{uuid}', [Labelisasi_pvdcController::class, 'update'])->name('labelisasi_pvdc.update.form');
    Route::put('/labelisasi_pvdc/update_qc/{uuid}', [Labelisasi_pvdcController::class, 'update_qc'])->name('labelisasi_pvdc.update_qc');
    Route::get('/labelisasi_pvdc/edit/{uuid}', [Labelisasi_pvdcController::class, 'edit'])->name('labelisasi_pvdc.edit.form');
    Route::put('/labelisasi_pvdc/edit_spv/{uuid}', [Labelisasi_pvdcController::class, 'edit_spv'])->name('labelisasi_pvdc.edit_spv');
    Route::get('/labelisasi_pvdc/verification', [Labelisasi_pvdcController::class, 'verification'])->name('labelisasi_pvdc.verification');
    Route::put('/labelisasi_pvdc/verification/{uuid}', [Labelisasi_pvdcController::class, 'updateVerification'])
        ->name('labelisasi_pvdc.verification.update');
    Route::post('/labelisasi_pvdc/save-row-temp', [Labelisasi_pvdcController::class, 'saveRowTemp'])->name('labelisasi_pvdc.saveRowTemp');
    Route::post('/labelisasi_pvdc/store-final', [Labelisasi_pvdcController::class, 'storeFinal'])->name('labelisasi_pvdc.storeFinal');
    Route::delete('/labelisasi_pvdc/{uuid}', [Labelisasi_pvdcController::class, 'destroy'])->name('labelisasi_pvdc.destroy');
    Route::get('/labelisasi_pvdc/export-pdf', [Labelisasi_pvdcController::class, 'exportPdf'])->name('labelisasi_pvdc.exportPdf');

    // Mincing

    Route::get('/mincing', [MincingController::class, 'index'])->name('mincing.index');
    Route::get('/mincing/create', [MincingController::class, 'create'])->name('mincing.create');
    Route::post('/mincing', [MincingController::class, 'store'])->name('mincing.store');
    Route::get('/mincing/update/{uuid}', [MincingController::class, 'update'])->name('mincing.update.form');
    Route::put('/mincing/update_qc/{uuid}', [MincingController::class, 'update_qc'])->name('mincing.update_qc');
    Route::get('/mincing/edit/{uuid}', [MincingController::class, 'edit'])->name('mincing.edit.form');
    Route::put('/mincing/edit_spv/{uuid}', [MincingController::class, 'edit_spv'])->name('mincing.edit_spv');
    Route::get('/mincing/verification', [MincingController::class, 'verification'])->name('mincing.verification');
    Route::put('/mincing/verification/{uuid}', [MincingController::class, 'updateVerification'])
        ->name('mincing.verification.update');
    Route::get('/mincing/export-pdf', [MincingController::class, 'exportPdf'])->name('mincing.exportPdf');
    Route::get('/mincing/export-excel', [MincingController::class, 'exportExcel'])->name('mincing.exportExcel');
    Route::delete('/mincing/{uuid}', [MincingController::class, 'destroy'])->name('mincing.destroy');

    // Metal
    Route::get('/metal', [MetalController::class, 'index'])->name('metal.index');
    Route::get('/metal/create', [MetalController::class, 'create'])->name('metal.create');
    Route::post('/metal', [MetalController::class, 'store'])->name('metal.store');
    Route::get('/metal/update/{uuid}', [MetalController::class, 'update'])->name('metal.update.form');
    Route::put('/metal/update_qc/{uuid}', [MetalController::class, 'update_qc'])->name('metal.update_qc');
    Route::get('/metal/edit/{uuid}', [MetalController::class, 'edit'])->name('metal.edit.form');
    Route::put('/metal/edit_spv/{uuid}', [MetalController::class, 'edit_spv'])->name('metal.edit_spv');
    Route::get('/metal/verification', [MetalController::class, 'verification'])->name('metal.verification');
    Route::put('/metal/verification/{uuid}', [MetalController::class, 'updateVerification'])
        ->name('metal.verification.update');
    Route::get('/metal/export-pdf', [MetalController::class, 'exportPdf'])->name('metal.exportPdf');
    Route::delete('/metal/{uuid}', [MetalController::class, 'destroy'])->name('metal.destroy');

    // Pemasakan
    Route::get('/pemasakan/export-pdf', [PemasakanController::class, 'exportPdf'])->name('pemasakan.exportPdf');
    Route::get('/pemasakan', [PemasakanController::class, 'index'])->name('pemasakan.index');
    Route::get('/pemasakan/create', [PemasakanController::class, 'create'])->name('pemasakan.create');
    Route::post('/pemasakan', [PemasakanController::class, 'store'])->name('pemasakan.store');
    Route::get('/pemasakan/update/{uuid}', [PemasakanController::class, 'update'])->name('pemasakan.update.form');
    Route::put('/pemasakan/update_qc/{uuid}', [PemasakanController::class, 'update_qc'])->name('pemasakan.update_qc');
    Route::get('/pemasakan/edit/{uuid}', [PemasakanController::class, 'edit'])->name('pemasakan.edit.form');
    Route::put('/pemasakan/edit_spv/{uuid}', [PemasakanController::class, 'edit_spv'])->name('pemasakan.edit_spv');
    Route::get('/pemasakan/verification', [PemasakanController::class, 'verification'])->name('pemasakan.verification');
    Route::put('/pemasakan/verification/{uuid}', [PemasakanController::class, 'updateVerification'])
        ->name('pemasakan.verification.update');
    Route::delete('/pemasakan/{uuid}', [PemasakanController::class, 'destroy'])->name('pemasakan.destroy');

    // Prepacking
    Route::get('/prepacking', [PrepackingController::class, 'index'])->name('prepacking.index');
    Route::get('/prepacking/create', [PrepackingController::class, 'create'])->name('prepacking.create');
    Route::post('/prepacking', [PrepackingController::class, 'store'])->name('prepacking.store');
    Route::get('/prepacking/update/{uuid}', [PrepackingController::class, 'update'])->name('prepacking.update.form');
    Route::put('/prepacking/update_qc/{uuid}', [PrepackingController::class, 'update_qc'])->name('prepacking.update_qc');
    Route::get('/prepacking/edit/{uuid}', [PrepackingController::class, 'edit'])->name('prepacking.edit.form');
    Route::put('/prepacking/edit_spv/{uuid}', [PrepackingController::class, 'edit_spv'])->name('prepacking.edit_spv');
    Route::get('/prepacking/verification', [PrepackingController::class, 'verification'])->name('prepacking.verification');
    Route::put('/prepacking/verification/{uuid}', [PrepackingController::class, 'updateVerification'])
        ->name('prepacking.verification.update');
    Route::get('/prepacking/export-pdf', [PrepackingController::class, 'exportPdf'])->name('prepacking.exportPdf');
    Route::delete('/prepacking/{uuid}', [PrepackingController::class, 'destroy'])->name('prepacking.destroy');

    // Washing
    Route::get('/washing/export-pdf', [WashingController::class, 'exportPdf'])->name('washing.exportPdf');
    Route::get('/washing', [WashingController::class, 'index'])->name('washing.index');
    Route::get('/washing/create', [WashingController::class, 'create'])->name('washing.create');
    Route::post('/washing', [WashingController::class, 'store'])->name('washing.store');
    Route::get('/washing/update/{uuid}', [WashingController::class, 'update'])->name('washing.update.form');
    Route::put('/washing/update_qc/{uuid}', [WashingController::class, 'update_qc'])->name('washing.update_qc');
    Route::get('/washing/edit/{uuid}', [WashingController::class, 'edit'])->name('washing.edit.form');
    Route::put('/washing/edit_spv/{uuid}', [WashingController::class, 'edit_spv'])->name('washing.edit_spv');
    Route::get('/washing/verification', [WashingController::class, 'verification'])->name('washing.verification');
    Route::put('/washing/verification/{uuid}', [WashingController::class, 'updateVerification'])
        ->name('washing.verification.update');
    Route::delete('/washing/{uuid}', [WashingController::class, 'destroy'])->name('washing.destroy');

    // Pemusnahan
    Route::get('/pemusnahan/export-pdf', [PemusnahanController::class, 'exportPdf'])->name('pemusnahan.exportPdf');
    Route::get('/pemusnahan', [PemusnahanController::class, 'index'])->name('pemusnahan.index');
    Route::get('/pemusnahan/create', [PemusnahanController::class, 'create'])->name('pemusnahan.create');
    Route::post('/pemusnahan', [PemusnahanController::class, 'store'])->name('pemusnahan.store');
    Route::get('/pemusnahan/update/{uuid}', [PemusnahanController::class, 'update'])->name('pemusnahan.update.form');
    Route::put('/pemusnahan/update_qc/{uuid}', [PemusnahanController::class, 'update_qc'])->name('pemusnahan.update_qc');
    Route::get('/pemusnahan/edit/{uuid}', [PemusnahanController::class, 'edit'])->name('pemusnahan.edit.form');
    Route::put('/pemusnahan/edit_spv/{uuid}', [PemusnahanController::class, 'edit_spv'])->name('pemusnahan.edit_spv');
    Route::get('/pemusnahan/verification', [PemusnahanController::class, 'verification'])->name('pemusnahan.verification');
    Route::put('/pemusnahan/verification/{uuid}', [PemusnahanController::class, 'updateVerification'])
        ->name('pemusnahan.verification.update');
    Route::delete('/pemusnahan/{uuid}', [PemusnahanController::class, 'destroy'])->name('pemusnahan.destroy');

    // Release Packing
    Route::get('/release_packing', [Release_packingController::class, 'index'])->name('release_packing.index');
    Route::get('/release_packing/create', [Release_packingController::class, 'create'])->name('release_packing.create');
    Route::post('/release_packing', [Release_packingController::class, 'store'])->name('release_packing.store');
    Route::get('/release_packing/update/{uuid}', [Release_packingController::class, 'update'])->name('release_packing.update.form');
    Route::put('/release_packing/update_qc/{uuid}', [Release_packingController::class, 'update_qc'])->name('release_packing.update_qc');
    Route::get('/release_packing/edit/{uuid}', [Release_packingController::class, 'edit'])->name('release_packing.edit.form');
    Route::put('/release_packing/edit_spv/{uuid}', [Release_packingController::class, 'edit_spv'])->name('release_packing.edit_spv');
    Route::get('/release_packing/verification', [Release_packingController::class, 'verification'])->name('release_packing.verification');
    Route::put('/release_packing/verification/{uuid}', [Release_packingController::class, 'updateVerification'])
        ->name('release_packing.verification.update');
    Route::get('/release_packing/export-pdf', [Release_packingController::class, 'exportPdf'])->name('release_packing.exportPdf');
    Route::delete('/release_packing/{uuid}', [Release_packingController::class, 'destroy'])->name('release_packing.destroy');

    /*FORM RTE CIKANDE*/
    // Retain RTE
    Route::get('/retain_rte', [Retain_rteController::class, 'index'])->name('retain_rte.index');
    Route::get('/retain_rte/create', [Retain_rteController::class, 'create'])->name('retain_rte.create');
    Route::post('/retain_rte', [Retain_rteController::class, 'store'])->name('retain_rte.store');
    Route::get('/retain_rte/update/{uuid}', [Retain_rteController::class, 'update'])->name('retain_rte.update.form');
    Route::put('/retain_rte/update_qc/{uuid}', [Retain_rteController::class, 'update_qc'])->name('retain_rte.update_qc');
    Route::get('/retain_rte/edit/{uuid}', [Retain_rteController::class, 'edit'])->name('retain_rte.edit.form');
    Route::put('/retain_rte/edit_spv/{uuid}', [Retain_rteController::class, 'edit_spv'])->name('retain_rte.edit_spv');
    Route::get('/retain_rte/verification', [Retain_rteController::class, 'verification'])->name('retain_rte.verification');
    Route::put('/retain_rte/verification/{uuid}', [Retain_rteController::class, 'updateVerification'])
        ->name('retain_rte.verification.update');
    Route::get('/retain_rte/export-pdf', [Retain_rteController::class, 'exportPdf'])->name('retain_rte.exportPdf');
    Route::delete('/retain_rte/{uuid}', [Retain_rteController::class, 'destroy'])->name('retain_rte.destroy');

    // Release Packing RTE
    Route::get('/release_packing_rte', [Release_packing_rteController::class, 'index'])->name('release_packing_rte.index');
    Route::get('/release_packing_rte/create', [Release_packing_rteController::class, 'create'])->name('release_packing_rte.create');
    Route::post('/release_packing_rte', [Release_packing_rteController::class, 'store'])->name('release_packing_rte.store');
    Route::get('/release_packing_rte/update/{uuid}', [Release_packing_rteController::class, 'update'])->name('release_packing_rte.update.form');
    Route::put('/release_packing_rte/update_qc/{uuid}', [Release_packing_rteController::class, 'update_qc'])->name('release_packing_rte.update_qc');
    Route::get('/release_packing_rte/edit/{uuid}', [Release_packing_rteController::class, 'edit'])->name('release_packing_rte.edit.form');
    Route::put('/release_packing_rte/edit_spv/{uuid}', [Release_packing_rteController::class, 'edit_spv'])->name('release_packing_rte.edit_spv');
    Route::get('/release_packing_rte/verification', [Release_packing_rteController::class, 'verification'])->name('release_packing_rte.verification');
    Route::put('/release_packing_rte/verification/{uuid}', [Release_packing_rteController::class, 'updateVerification'])
        ->name('release_packing_rte.verification.update');
    Route::get('/release_packing_rte/export-pdf', [Release_packing_rteController::class, 'exportPdf'])->name('release_packing_rte.exportPdf');
    Route::delete('/release_packing_rte/{uuid}', [Release_packing_rteController::class, 'destroy'])->name('release_packing_rte.destroy');

    // Pemasakan RTE
    Route::get('/pemasakan_rte', [Pemasakan_rteController::class, 'index'])->name('pemasakan_rte.index');
    Route::get('/pemasakan_rte/create', [Pemasakan_rteController::class, 'create'])->name('pemasakan_rte.create');
    Route::post('/pemasakan_rte', [Pemasakan_rteController::class, 'store'])->name('pemasakan_rte.store');
    Route::get('/pemasakan_rte/update/{uuid}', [Pemasakan_rteController::class, 'update'])->name('pemasakan_rte.update.form');
    Route::put('/pemasakan_rte/update_qc/{uuid}', [Pemasakan_rteController::class, 'update_qc'])->name('pemasakan_rte.update_qc');
    Route::get('/pemasakan_rte/edit/{uuid}', [Pemasakan_rteController::class, 'edit'])->name('pemasakan_rte.edit.form');
    Route::put('/pemasakan_rte/edit_spv/{uuid}', [Pemasakan_rteController::class, 'edit_spv'])->name('pemasakan_rte.edit_spv');
    Route::get('/pemasakan_rte/verification', [Pemasakan_rteController::class, 'verification'])->name('pemasakan_rte.verification');
    Route::put('/pemasakan_rte/verification/{uuid}', [Pemasakan_rteController::class, 'updateVerification'])
        ->name('pemasakan_rte.verification.update');
    Route::get('/pemasakan_rte/export-pdf', [Pemasakan_rteController::class, 'exportPdf'])->name('pemasakan_rte.exportPdf');
    Route::delete('/pemasakan_rte/{uuid}', [Pemasakan_rteController::class, 'destroy'])->name('pemasakan_rte.destroy');

    /*SUHU DAN SANITASI*/
    // Resource GMP tanpa show
    Route::resource('gmp', GmpController::class)->parameters(['gmp' => 'uuid'])->except(['show']);
    Route::get('/gmp/export', [GmpController::class, 'export'])->name('gmp.export');
    Route::put('/gmp/{uuid}/update-verification', [GmpController::class, 'updateVerification'])->name('gmp.updateVerification');

    // GMP
    Route::get('gmp/verification', [GmpController::class, 'verification'])->name('gmp.verification');
    Route::put('gmp/verification/{uuid}', [GmpController::class, 'updateVerification'])->name('gmp.verification.update');
    Route::get('/gmp/export', [GmpController::class, 'export'])->name('gmp.export');
    // Route::resource('gmp', GmpController::class)->parameters([
    //     'gmp' => 'uuid'
    // ]);

    // Suhu Ruang
    Route::get('/suhu', [SuhuController::class, 'index'])->name('suhu.index');
    Route::get('/suhu/create', [SuhuController::class, 'create'])->name('suhu.create');
    Route::post('/suhu', [SuhuController::class, 'store'])->name('suhu.store');
    Route::get('/suhu/update/{uuid}', [SuhuController::class, 'update'])->name('suhu.update.form');
    Route::put('/suhu/update_qc/{uuid}', [SuhuController::class, 'update_qc'])->name('suhu.update_qc');
    Route::get('/suhu/edit/{uuid}', [SuhuController::class, 'edit'])->name('suhu.edit.form');
    Route::put('/suhu/edit_spv/{uuid}', [SuhuController::class, 'edit_spv'])->name('suhu.edit_spv');
    Route::get('/suhu/verification', [SuhuController::class, 'verification'])->name('suhu.verification');
    Route::put('/suhu/verification/{uuid}', [SuhuController::class, 'updateVerification'])
        ->name('suhu.verification.update');
    Route::get('/suhu/export-pdf', [SuhuController::class, 'exportPdf'])->name('suhu.exportPdf');
    Route::delete('/suhu/{uuid}', [SuhuController::class, 'destroy'])->name('suhu.destroy');

    // Kontrol Sanitasi
    Route::get('/sanitasi', [SanitasiController::class, 'index'])->name('sanitasi.index');
    Route::get('/sanitasi/create', [SanitasiController::class, 'create'])->name('sanitasi.create');
    Route::post('/sanitasi', [SanitasiController::class, 'store'])->name('sanitasi.store');
    Route::get('/sanitasi/update/{uuid}', [SanitasiController::class, 'update'])->name('sanitasi.update.form');
    Route::put('/sanitasi/update_qc/{uuid}', [SanitasiController::class, 'update_qc'])->name('sanitasi.update_qc');
    Route::get('/sanitasi/edit/{uuid}', [SanitasiController::class, 'edit'])->name('sanitasi.edit.form');
    Route::put('/sanitasi/edit_spv/{uuid}', [SanitasiController::class, 'edit_spv'])->name('sanitasi.edit_spv');
    Route::get('/sanitasi/verification', [SanitasiController::class, 'verification'])->name('sanitasi.verification');
    Route::put('/sanitasi/verification/{uuid}', [SanitasiController::class, 'updateVerification'])
        ->name('sanitasi.verification.update');
    Route::get('/sanitasi/export-pdf', [SanitasiController::class, 'exportPdf'])->name('sanitasi.exportPdf');
    Route::delete('/sanitasi/{uuid}', [SanitasiController::class, 'destroy'])->name('sanitasi.destroy');
    Route::resource('raw-material', MasterRawMaterialController::class);
});

//Biarkan dibawah ini tetap di paling bawah written by Joe
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});




// Route::middleware(['permission:access roles'])->group(function () {
//         Route::resource('/roles', RoleController::class)->except(['show']);
//         Route::get('roles/{role}/manage-access', [RoleController::class, 'manageAccess'])->name('roles.manage-access');
//         Route::put('roles/{role}/manage-access', [RoleController::class, 'updateAccess'])->name('roles.manage-access.update');

//         Route::resource('/permissions', PermissionController::class)->except(['show']);
//     });
