@extends('layouts.app')

@section('content')

<div class="container-fluid py-0">

	@once
	@php
	function showValue($val) {
		if (is_array($val)) {
			return collect($val)
			->map(fn($v) => "<span class='badge bg-gradient bg-light text-dark border border-secondary me-1 mb-1'>$v</span>")
			->implode('');
		}
		return e($val ?? '-');
	}
	@endphp
	@endonce

	{{-- Alert sukses --}}
	@if(session('success'))
	<div class="alert alert-success alert-dismissible fade show" role="alert">
		{{ session('success') }}
		<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
	</div>
	@endif

	{{-- Alert error --}}
	@if(session('error'))
	<div class="alert alert-danger alert-dismissible fade show" role="alert">
		{{ session('error') }}
		<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
	</div>
	@endif


	<div class="card shadow-sm">
		<div class="card-body">
			<div class="d-flex justify-content-between align-items-center mb-4">
				<h3><i class="bi bi-list-check"></i> Data Pengecekan Pemasakan</h3>
			</div>

			{{-- Filter dan Live Search --}}
			<form id="filterForm" method="GET" action="{{ route('pemasakan.verification') }}" class="d-flex flex-wrap align-items-center gap-2 mb-3 p-2 border rounded bg-light shadow-sm">

				<div class="input-group" style="max-width: 220px;">
					<span class="input-group-text bg-white border-end-0">
						<i class="bi bi-calendar-date text-muted"></i>
					</span>
					<input type="date" name="date" id="filter_date" class="form-control border-start-0"
					value="{{ request('date') }}" placeholder="Tanggal Produksi">
				</div>

				<div class="input-group flex-grow-1" style="max-width: 350px;">
					<span class="input-group-text bg-white border-end-0">
						<i class="bi bi-search text-muted"></i>
					</span>
					<input type="text" name="search" id="search" class="form-control border-start-0"
					value="{{ request('search') }}" placeholder="Cari Nama Produk / Kode Produksi...">
				</div>
			</form>

			<script>
				document.addEventListener('DOMContentLoaded', () => {
					const search = document.getElementById('search');
					const date = document.getElementById('filter_date');
					const form = document.getElementById('filterForm');
					let timer;

					search.addEventListener('input', () => {
						clearTimeout(timer);
						timer = setTimeout(() => form.submit(), 500);
					});

					date.addEventListener('change', () => form.submit());
				});
			</script>

			{{-- Tambahkan table-responsive agar tabel tidak keluar border --}}
			<div class="table-responsive">
				<table class="table table-striped table-bordered align-middle">
					<thead class="table-primary text-center">
						<tr>
							<th>NO.</th>
							<th>Date | Shift</th>
							<th>Nama Produk</th>
							<th>Kode Produksi</th>
							<th>No. Chamber</th>
							<th>Berat Produk (Gram)</th>
							<th>Suhu Produk (°C)</th>
							<th>Jumlah Tray</th>
							<th>Total Reject (Kg)</th>
							<th>Pengecekan</th>
							<th>QC</th>
							<th>Produksi</th>
							<th>SPV</th>
							@can('can access update button')
                                <th>Aksi</th>
                                @endcan
						</tr>
					</thead>
					<tbody>
						@php 
						$no = ($data->currentPage() - 1) * $data->perPage() + 1; 
						@endphp

						@forelse ($data as $dep)
						@php
						$cooking = json_decode($dep->cooking, true);
						@endphp
						<tr>
							<td class="text-center align-middle">{{ $no++ }}</td>
							<td class="text-center align-middle">{{ \Carbon\Carbon::parse($dep->date)->format('d-m-Y') }} | Shift: {{ $dep->shift }}</td>
							<td class="text-center align-middle">{{ $dep->nama_produk }}</td>
							<td class="text-center align-middle">{{ $dep->kode_produksi }}</td>
							<td class="text-center align-middle">{{ $dep->no_chamber }}</td>
							<td class="text-center align-middle">{{ $dep->berat_produk }}</td>
							<td class="text-center align-middle">{{ $dep->suhu_produk }}</td>
							<td class="text-center align-middle">{{ $dep->jumlah_tray }}</td>
							<td class="text-center align-middle">{{ $dep->total_reject }}</td>
							<td class="text-center align-middle">
								@if(!empty($cooking))
								<a href="#" class="fw-bold text-decoration-underline text-primary"
								data-bs-toggle="modal" data-bs-target="#cookingModal{{ $dep->uuid }}">
								Result
							</a>

							{{-- Modal Detail Cooking --}}
							<div class="modal fade" id="cookingModal{{ $dep->uuid }}" tabindex="-1"
								aria-labelledby="cookingModalLabel{{ $dep->uuid }}" aria-hidden="true">
								<div class="modal-dialog modal-xl modal-dialog-scrollable">
									<div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">

										<div class="modal-header bg-primary bg-gradient text-white">
											<h5 class="modal-title fw-semibold">
												<i class="bi bi-fire me-2"></i> Detail Proses Pemasakan
											</h5>
											<button type="button" class="btn-close btn-close-white"
											data-bs-dismiss="modal" aria-label="Close"></button>
										</div>

										<div class="modal-body p-4 bg-light-subtle">
											@php
											$sections = [
											'2. Tekanan & Suhu Awal' => [
											'Tekanan Angin (Kg/cm²)' => 'tekanan_angin',
											'Tekanan Steam (Kg/cm²)' => 'tekanan_steam',
											'Tekanan Air (Kg/cm²)' => 'tekanan_air'
											],
											'3. Pemanasan Awal' => [
											'Suhu Air Awal (°C)' => 'suhu_air_awal',
											'Tekanan Awal (Mpa)' => 'tekanan_awal',
											'Waktu Mulai' => 'waktu_mulai_awal',
											'Waktu Selesai' => 'waktu_selesai_awal'
											],
											'4. Proses Pemanasan' => [
											'Suhu Air Proses (°C)' => 'suhu_air_proses',
											'Tekanan Proses (Mpa)' => 'tekanan_proses',
											'Waktu Mulai' => 'waktu_mulai_proses',
											'Waktu Selesai' => 'waktu_selesai_proses'
											],
											'5. Sterilisasi' => [
											'Suhu Air Sterilisasi (°C)' => 'suhu_air_sterilisasi',
											'Thermometer Retort (°C)' => 'thermometer_retort',
											'Tekanan Sterilisasi (Mpa)' => 'tekanan_sterilisasi',
											'Waktu Mulai' => 'waktu_mulai_sterilisasi',
											'Waktu Pengecekan' => 'waktu_pengecekan_sterilisasi',
											'Waktu Selesai' => 'waktu_selesai_sterilisasi'
											],
											'6. Pendinginan Awal' => [
											'Suhu Air (°C)' => 'suhu_air_pendinginan_awal',
											'Tekanan (Mpa)' => 'tekanan_pendinginan_awal',
											'Waktu Mulai Awal' => 'waktu_mulai_pendinginan_awal',
											'Waktu Selesai Awal' => 'waktu_selesai_pendinginan_awal'
											],
											'7. Pendinginan' => [
											'Suhu Air (°C)' => 'suhu_air_pendinginan',
											'Tekanan (Mpa)' => 'tekanan_pendinginan',
											'Waktu Mulai' => 'waktu_mulai_pendinginan',
											'Waktu Selesai' => 'waktu_selesai_pendinginan'
											],
											'8. Proses Akhir' => [
											'Suhu Air (°C)' => 'suhu_air_akhir',
											'Tekanan (Mpa)' => 'tekanan_akhir',
											'Waktu Mulai' => 'waktu_mulai_akhir',
											'Waktu Selesai' => 'waktu_selesai_akhir'
											],
											'9. Total Waktu Proses' => [
											'Waktu Mulai Total Proses' => 'waktu_mulai_total',
											'Waktu Selesai Total Proses' => 'waktu_selesai_total'
											],
											'10. Hasil Pemasakan' => [
											'Suhu Produk Akhir (°C)' => 'suhu_produk_akhir',
											'Panjang (Cm)' => 'panjang',
											'Diameter (Cm)' => 'diameter',
											'Rasa (1-3)' => 'rasa',
											'Warna (1-3)' => 'warna',
											'Aroma (1-3)' => 'aroma',
											'Texture (1-3)' => 'texture',
											'Sobek Seal' => 'sobek_seal'
											]
											];
											@endphp

											@foreach($sections as $title => $rows)
											<div class="mb-4">
												<h6 class="fw-bold fs-6 text-primary mb-3">
													<i class="bi bi-chevron-right text-info me-1"></i> {{ $title }}
												</h6>

												<div class="table-responsive shadow-sm rounded">
													<table class="table table-bordered table-sm align-middle text-center mb-0 bg-white">
														<tbody>
															@foreach($rows as $label => $key)
															<tr>
																<td class="fw-semibold text-start ps-3 w-50">{{ $label }}</td>
																<td class="text-start ps-3">{!! showValue($cooking[$key] ?? '-') !!}</td>
															</tr>
															@endforeach
														</tbody>
													</table>
												</div>
											</div>
											@endforeach
										</div>

										<div class="modal-footer bg-body-tertiary border-0">
											<button type="button" class="btn btn-outline-secondary btn-sm px-4" data-bs-dismiss="modal">
												<i class="bi bi-x-circle"></i> Tutup
											</button>
										</div>
									</div>
								</div>
							</div>
							@else
							<span>-</span>
							@endif
						</td>

						<td class="text-center">
							{{ \Illuminate\Support\Facades\DB::table('s')->where('name', $dep->name)->value('name') }}
						</td>
						<td class="text-center align-middle">{{ $dep->nama_produksi }}</td>
						<td class="text-center align-middle">
							@if ($dep->status_spv == 0)
							<span class="fw-bold text-secondary">Created</span>
							@elseif ($dep->status_spv == 1)
							<span class="fw-bold text-success">Verified</span>
							@elseif ($dep->status_spv == 2)
							<!-- Link buka modal -->
							<a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#revisionModal{{ $dep->uuid }}" 
								class="text-danger fw-bold text-decoration-none" style="cursor: pointer;">Revision</a>
								<!-- Modal -->
								<div class="modal fade" id="revisionModal{{ $dep->uuid }}" tabindex="-1" aria-labelledby="revisionModalLabel{{ $dep->uuid }}" aria-hidden="true">
									<div class="modal-dialog modal-dialog-centered">
										<div class="modal-content">
											<div class="modal-header bg-danger text-white">
												<h5 class="modal-title" id="revisionModalLabel{{ $dep->uuid }}">Detail Revisi</h5>
												<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
											</div>
											<div class="modal-body">
												<ul class="list-unstyled mb-0">
													<li><strong>Status:</strong> Revision</li>
													<li><strong>Catatan:</strong> {{ $dep->catatan_spv ?? '-' }}</li>
												</ul>
											</div>
											<div class="modal-footer">
												<button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
											</div>
										</div>
									</div>
								</div>
								@endif
							</td>
							<td class="text-center align-middle">
								<button type="button" class="btn btn-primary btn-sm fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#verifyModal{{ $dep->uuid }}">
									<i class="bi bi-shield-check me-1"></i> Verifikasi
								</button>
								<a href="{{ route('pemasakan.edit.form', $dep->uuid) }}" class="btn btn-warning btn-sm me-1">
									<i class="bi bi-pencil-square"></i> Edit
								</a>
								<form action="{{ route('pemasakan.destroy', $dep->uuid) }}" method="POST" class="d-inline">
									@csrf
									@method('DELETE')
									<button type="submit" class="btn btn-danger btn-sm"
									onclick="return confirm('Yakin ingin menghapus?')">
									<i class="bi bi-trash"></i> Hapus
								</button>
							</form>
							<div class="modal fade" id="verifyModal{{ $dep->uuid }}" tabindex="-1" aria-labelledby="verifyModalLabel{{ $dep->uuid }}" aria-hidden="true">
								<div class="modal-dialog modal-dialog-centered modal-md">
									<form action="{{ route('pemasakan.verification.update', $dep->uuid) }}" method="POST">
										@csrf
										@method('PUT')
										<div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden text-white" 
										style="background: linear-gradient(145deg, #7a1f12, #9E3419); 
										box-shadow: 0 15px 40px rgba(0,0,0,0.5);">
										<div class="modal-header border-bottom border-light-subtle p-4" style="border-bottom-width: 3px !important;">
											<h5 class="modal-title fw-bolder fs-3 text-uppercase" id="verifyModalLabel{{ $dep->uuid }}" style="color: #00ffc4;">
												<i class="bi bi-gear-fill me-2"></i> VERIFICATION
											</h5>
											<button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
										</div>

										<div class="modal-body p-5">
											<p class="text-light mb-4 fs-6">
												Pastikan data yang akan diverifikasi di check dengan teliti terlebih dahulu.
											</p>
											<div class="row g-4">
												<div class="col-md-12">
													<label for="status_spv_{{ $dep->uuid }}" class="form-label fw-bold mb-2 text-center d-block" 
														style="color: #FFE5DE; font-size: 0.95rem;">
														Pilih Status Verifikasi
													</label>

													<select 
													name="status_spv" 
													id="status_spv_{{ $dep->uuid }}" 
													class="form-select form-select-lg fw-bold text-center mx-auto"
													style="
													background: linear-gradient(135deg, #fff1f0, #ffe5de);
													border: 2px solid #dc3545;
													border-radius: 12px;
													color: #dc3545;
													height: 55px;
													font-size: 1.1rem;
													box-shadow: 0 6px 12px rgba(0,0,0,0.1);
													width: 85%;
													transition: all 0.3s ease;
													"
													required
													>
													<option value="1" {{ $dep->status_spv == 1 ? 'selected' : '' }} 
														style="color: #198754; font-weight: 600;">✅ Verified (Disetujui)</option>
														<option value="2" {{ $dep->status_spv == 2 ? 'selected' : '' }} 
															style="color: #dc3545; font-weight: 600;">❌ Revision (Perlu Perbaikan)</option>
														</select>
													</div>

													<div class="col-md-12 mt-3">
														<label for="catatan_spv_{{ $dep->uuid }}" class="form-label fw-bold text-light mb-2">
															Catatan Tambahan (Opsional)
														</label>
														<textarea name="catatan_spv" id="catatan_spv_{{ $dep->uuid }}" rows="4" 
															class="form-control text-dark border-0 shadow-none" 
															placeholder="Masukkan catatan, misalnya alasan revisi..." 
															style="background-color: #FFE5DE; height: 120px;">{{ $dep->catatan_spv }}</textarea>

														</div>
													</div>
												</div>

												<div class="modal-footer justify-content-end p-4 border-top" style="background-color: #9E3419; border-color: #00ffc4 !important;">
													<button type="button" class="btn btn-outline-light fw-bold rounded-pill px-4 me-2" data-bs-dismiss="modal">
														Batal
													</button>
													<button type="submit" class="btn fw-bolder rounded-pill px-5" style="background-color: #E39581; color: #2c3e50;">
														<i class="bi bi-save-fill me-1"></i> SUBMIT
													</button>
												</div>
											</div>
										</div>
									</form>
								</div>
							</div>
						</td>
					</tr>
					@empty
					<tr>
						<td colspan="19" class="text-center">Belum ada data pengecekan pemasakan.</td>
					</tr>
					@endforelse
				</tbody>
			</table>
		</div>

		{{-- Pagination --}}
		<div class="mt-3">
			{{ $data->withQueryString()->links('pagination::bootstrap-5') }}
		</div>
	</div>
</div>
</div>

{{-- Auto-hide alert setelah 3 detik --}}
<script>
	setTimeout(() => {
		const alert = document.querySelector('.alert');
		if(alert){
			alert.classList.remove('show');
			alert.classList.add('fade');
		}
	}, 3000);
</script>

{{-- CSS tambahan agar tabel lebih rapi --}}
<style>
	.table td, .table th {
		font-size: 0.85rem;
		white-space: nowrap; 
	}
	.text-danger {
		font-weight: bold;
	}
	.text-muted.fst-italic {
		color: #6c757d !important;
		font-style: italic !important;
	}
	.container {
		padding-left: 2px !important;
		padding-right: 2px !important;
	}
	.text-success { color: green; font-weight: bold; }
	.text-danger { color: red; font-weight: bold; }
</style>
@endsection

