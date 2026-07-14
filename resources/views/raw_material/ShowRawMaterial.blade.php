{{-- resources/views/raw_material/ShowRawMaterial.blade.php --}}

@extends('layouts.app') {{-- Menggunakan layout utama Anda --}}

{{-- Mendefinisikan bagian style kustom --}}
@push('styles')
    {{-- Menambahkan link untuk Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    {{-- Font Awesome untuk ikon (jika diperlukan) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" />

    <style>
        /* Mengubah font utama untuk tampilan yang lebih modern */
        body {
            background-color: #f8f9fa;
            font-family: 'Inter', sans-serif;
        }

        .card-custom {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .card-header strong {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Style untuk key-value pairs */
        .detail-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }

        .detail-item {
            background-color: #fdfdfd;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }

        .detail-item dt {
            font-weight: 600;
            color: #495057;
            font-size: 0.9rem;
        }

        .detail-item dd {
            margin-bottom: 0;
            color: #212529;
            font-size: 1rem;
        }

        /* Style untuk status badge (dari index) */
        .badge-status {
            padding: 0.5em 0.75em;
            font-size: 0.9rem;
            /* Sedikit lebih besar untuk detail */
            font-weight: 600;
            border-radius: 50rem;
        }

        .status-ok {
            background-color: rgba(25, 135, 84, 0.1);
            color: #198754;
        }

        .status-not-ok {
            background-color: rgba(220, 53, 69, 0.1);
            color: #DC3545;
        }
    </style>
@endpush

{{-- Mendefinisikan bagian konten --}}
@section('content')
    <div class="container-fluid py-0">
        <div class="card card-custom shadow-sm border-0">
            <div class="card-body p-4 p-md-5">

                {{-- HEADER --}}
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="mb-1"><i class="bi bi-file-earmark-text"></i> Detail Pemeriksaan Bahan Baku</h4>
                        <p class="text-muted mb-0">Inspeksi oleh: {{ $inspection->user->name ?? 'N/A' }} pada
                            {{ $inspection->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    <div>
                        <a href="{{ route('inspections.index') }}" class="btn btn-secondary me-2"><i
                                class="bi bi-arrow-left"></i> Kembali</a>
                        <a href="{{ route('inspections.edit', $inspection->uuid) }}" class="btn btn-warning"><i
                                class="bi bi-pencil-square"></i> Edit</a>
                    </div>
                </div>

                {{-- FUNGSI HELPER UNTUK TAMPILKAN STATUS --}}
                @php
                    function renderStatus($value)
                    {
                        if ($value) {
                            return '<span class="badge-status status-ok"><i class="fas fa-check-circle me-1"></i>OK</span>';
                        }
                        return '<span class="badge-status status-not-ok"><i class="fas fa-times-circle me-1"></i>NOT OK</span>';
                    }
                @endphp

                {{-- CARD INFORMASI UMUM --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong><i class="bi bi-info-circle-fill"></i> Informasi Umum</strong>
                    </div>
                    <div class="card-body">
                        <dl class="detail-list">
                            <div class="detail-item">
                                <dt>Waktu Kedatangan</dt>
                                <dd>{{ $inspection->setup_kedatangan ? $inspection->setup_kedatangan->format('d M Y, H:i') : '-' }}
                                </dd>
                            </div>
                            <div class="detail-item">
                                <dt>Bahan Baku</dt>
                                <dd>{{ $inspection->bahan_baku }}</dd>
                            </div>
                            <div class="detail-item">
                                <dt>Supplier</dt>
                                <dd>{{ $inspection->supplier }}</dd>
                            </div>
                            <div class="detail-item">
                                <dt>No. DO / PO</dt>
                                <dd>{{ $inspection->do_po }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                {{-- CARD KONDISI FISIK --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <strong><i class="bi bi-truck"></i> Kondisi Fisik (Mobil & Kemasan)</strong>
                    </div>
                    <div class="card-body">
                        <dl class="detail-list">
                            <div class="detail-item">
                                <dt>Warna</dt>
                                <dd>{!! renderStatus($inspection->mobil_check_warna) !!}</dd>
                            </div>
                            <div class="detail-item">
                                <dt>Kotoran</dt>
                                <dd>{!! renderStatus($inspection->mobil_check_kotoran) !!}</dd>
                            </div>
                            <div class="detail-item">
                                <dt>Aroma</dt>
                                <dd>{!! renderStatus($inspection->mobil_check_aroma) !!}</dd>
                            </div>
                            <div class="detail-item">
                                <dt>Kemasan</dt>
                                <dd>{!! renderStatus($inspection->mobil_check_kemasan) !!}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                {{-- CARD ANALISA PRODUK --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <strong><i class="bi bi-search"></i> Analisa Produk</strong>
                    </div>
                    <div class="card-body">
                        <dl class="detail-list">
                            <div class="detail-item">
                                <dt>K.A / FFA</dt>
                                <dd>{{ $inspection->analisa_ka_ffa }}</dd>
                            </div>
                            <div class="detail-item">
                                <dt>Logo Halal</dt>
                                <dd>{!! renderStatus($inspection->analisa_logo_halal) !!}</dd>
                            </div>
                            <div class="detail-item">
                                <dt>Negara Asal Produsen</dt>
                                <dd>{{ $inspection->analisa_negara_asal }}</dd>
                            </div>
                            <div class="detail-item">
                                <dt>Nama Produsen</dt>
                                <dd>{{ $inspection->analisa_produsen }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                {{-- CARD TRANSPORTER --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <strong><i class="bi bi-person-badge"></i> Informasi Transporter</strong>
                    </div>
                    <div class="card-body">
                        <dl class="detail-list">
                            <div class="detail-item">
                                <dt>Nopol Mobil</dt>
                                <dd>{{ $inspection->nopol_mobil }}</dd>
                            </div>
                            <div class="detail-item">
                                <dt>Kondisi Mobil</dt>
                                <dd>{{ $inspection->kondisi_mobil }}</dd>
                            </div>
                            <div class="detail-item">
                                <dt>No. Segel</dt>
                                <dd>{{ $inspection->no_segel }}</dd>
                            </div>
                            <div class="detail-item">
                                <dt>Suhu Mobil</dt>
                                <dd>{{ $inspection->suhu_mobil ? $inspection->suhu_mobil . ' °C' : '-' }}</dd>
                            </div>

                            <div class="detail-item">
                                <dt>Suhu Daging/Bahan</dt>
                                <dd>{{ $inspection->suhu_daging ? $inspection->suhu_daging . ' °C' : '-' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                {{-- CARD DOKUMEN PENDUKUNG --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <strong><i class="bi bi-file-earmark-text"></i> Dokumen Pendukung</strong>
                    </div>
                    <div class="card-body">
                        <dl class="detail-list">
                            <div class="detail-item">
                                <dt>Status Dokumen Halal</dt>
                                <dd>{!! renderStatus($inspection->dokumen_halal_berlaku) !!}</dd>
                            </div>
                            <div class="detail-item">
                                <dt>File Halal</dt>
                                <dd>
                                    @if ($inspection->dokumen_halal_file)
                                        <a href="{{ asset('storage/' . $inspection->dokumen_halal_file) }}" target="_blank"
                                            class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i> Lihat File</a>
                                    @else
                                        <span class="text-muted">Tidak diupload</span>
                                    @endif
                                </dd>
                            </div>
                            <div class="detail-item">
                                <dt>File COA</dt>
                                <dd>
                                    @if ($inspection->dokumen_coa_file)
                                        <a href="{{ asset('storage/' . $inspection->dokumen_coa_file) }}" target="_blank"
                                            class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i> Lihat File</a>
                                    @else
                                        <span class="text-muted">Tidak diupload</span>
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                {{-- CARD DETAIL PRODUK --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <strong><i class="bi bi-list-nested"></i> Detail Produk (Batch)</strong>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">Kode Batch</th>
                                        <th scope="col">Tgl Kadaluarsa</th>
                                        <th scope="col" class="text-end">Jumlah</th>
                                        <th scope="col" class="text-end">Sampel</th>
                                        <th scope="col" class="text-end">Reject</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($inspection->productDetails as $detail)
                                        <tr>
                                            <td>{{ $detail->kode_batch }}</td>
                                            <td>{{ $detail->exp ? \Carbon\Carbon::parse($detail->exp)->format('d M Y') : '-' }}
                                            </td>
                                            <td class="text-end">{{ $detail->jumlah }}</td>
                                            <td class="text-end">{{ $detail->jumlah_sampel }}</td>
                                            <td class="text-end">{{ $detail->jumlah_reject }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">Tidak ada data detail produk.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- KETERANGAN --}}
                @if ($inspection->keterangan)
                    <div class="card mb-4">
                        <div class="card-header">
                            <strong><i class="bi bi-chat-left-text"></i> Keterangan Tambahan</strong>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $inspection->keterangan }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
