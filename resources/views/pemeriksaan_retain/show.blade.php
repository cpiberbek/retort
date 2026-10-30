@extends('layouts.app')

@push('styles')
    {{-- Style untuk badge status --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <style>
        .badge-status { padding: 0.5em 0.75em; font-size: 0.9rem; font-weight: 600; border-radius: 50rem; }
        .status-pending { background-color: rgba(108, 117, 125, 0.1); color: #6c757d; }
        .status-verified { background-color: rgba(25, 135, 84, 0.1); color: #198754; }
        .status-revision { background-color: rgba(220, 53, 69, 0.1); color: #DC3545; }
    </style>
@endpush

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Detail Pemeriksaan Retain</h2>
        <div>
            {{-- Tombol 'Kembali' akan mengarahkan ke halaman index atau halaman verifikasi, tergantung dari mana Anda datang --}}
            @can('can access edit button')
            <a href="{{ route('pemeriksaan_retain.edit', $pemeriksaanRetain->uuid) }}" class="btn btn-warning">Edit</a>
            @endcan
            <a href="{{ url()->previous(route('pemeriksaan_retain.index')) }}" class="btn btn-secondary">Kembali</a>
        </div>
    </div>

    {{-- Detail Master --}}
    <div class="card mb-3">
        <div class="card-header">
            <h4>Data Utama</h4>
        </div>
        <div class="card-body">
            <div class="row">
                {{-- <div class="col-md-3">
                    <strong>ID (Angka):</strong>
                    <p>{{ $pemeriksaanRetain->id }}</p>
                </div> --}}
                {{-- <div class="col-md-3">
                    <strong>UUID:</strong>
                    <p>{{ $pemeriksaanRetain->uuid }}</p>
                </div> --}}
                <div class="col-md-3">
                    <strong>Tanggal:</strong>
                    <p>{{ \Carbon\Carbon::parse($pemeriksaanRetain->tanggal)->format('d F Y') }} (Hari {{ $pemeriksaanRetain->hari }})</p>
                </div>
                <div class="col-md-3">
                    <strong>Dibuat Oleh:</strong>
                    <p>{{ $pemeriksaanRetain->creator->name ?? 'N/A' }}</p>
                </div>
            </div>
            <strong>Keterangan:</strong>
            <p>{{ $pemeriksaanRetain->keterangan ?? '-' }}</p>
        </div>
        <div class="card-footer text-muted">
            Dibuat: {{ $pemeriksaanRetain->created_at->format('d M Y, H:i') }} | 
            Diupdate: {{ $pemeriksaanRetain->updated_at->format('d M Y, H:i') }}
        </div>
    </div>

    {{-- (BAGIAN BARU) Detail Verifikasi --}}
    <div class="card mb-3">
        <div class="card-header">
            <h4>Status Verifikasi</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <strong>Status:</strong>
                    <p>
                        @if($pemeriksaanRetain->status_spv == 1)
                            <span class="badge-status status-verified"><i class="fas fa-check-circle me-1"></i>Verified</span>
                        @elseif($pemeriksaanRetain->status_spv == 2)
                            <span class="badge-status status-revision"><i class="fas fa-exclamation-circle me-1"></i>Revision</span>
                        @else
                            <span class="badge-status status-pending"><i class="fas fa-clock me-1"></i>Pending</span>
                        @endif
                    </p>
                </div>
                <div class="col-md-4">
                    <strong>Diverifikasi Oleh:</strong>
                    {{-- Ini memerlukan update controller (lihat poin 2) --}}
                    <p>{{ $pemeriksaanRetain->verifiedBy->name ?? '-' }}</p>
                </div>
                <div class="col-md-4">
                    <strong>Tanggal Verifikasi:</strong>
                    <p>{{ $pemeriksaanRetain->verified_at ? \Carbon\Carbon::parse($pemeriksaanRetain->verified_at)->format('d M Y H:i') : '-' }}</p>
                </div>
                <div class="col-md-12">
                    <strong>Catatan SPV:</strong>
                    <p class="fst-italic">"{{ $pemeriksaanRetain->catatan_spv ?? '-' }}"</p>
                </div>
            </div>
        </div>
    </div>
    {{-- (AKHIR BAGIAN BARU) --}}


    <hr>

    {{-- Detail Item --}}
    <h4>Detail Item Pemeriksaan ({{ $pemeriksaanRetain->items->count() }} item)</h4>

    @foreach ($pemeriksaanRetain->items as $index => $item)
        <div class="card mb-3">
            <div class="card-header">
                <strong>Item #{{ $index + 1 }}</strong>
            </div>
            <div class="card-body">
                
                {{-- (DIUPDATE) Menambahkan Exp Date dan mengubah grid --}}
                <div class="row mb-2">
                    <div class="col-md-3"><strong>Kode Produksi:</strong> {{ $item->kode_produksi ?? '-' }}</div>
                    <div class="col-md-3"><strong>Exp Date:</strong> {{ $item->exp_date ? \Carbon\Carbon::parse($item->exp_date)->format('d M Y') : '-' }}</div>
                    <div class="col-md-2"><strong>Varian:</strong> {{ $item->varian ?? '-' }}</div>
                    <div class="col-md-2"><strong>Panjang:</strong> {{ $item->panjang ?? '-' }} cm</div>
                    <div class="col-md-2"><strong>Diameter:</strong> {{ $item->diameter ?? '-' }} cm</div>
                </div>
                <hr>
                
                {{-- (DIUPDATE) Mengisi data Sensori --}}
                <h6>Sensori</h6>
                <div class="row mb-2">
                    <div class="col-md-3"><strong>Rasa (1-3):</strong> {{ $item->sensori_rasa ?? '-' }}</div>
                    <div class="col-md-3"><strong>Warna (1-3):</strong> {{ $item->sensori_warna ?? '-' }}</div>
                    <div class="col-md-3"><strong>Aroma (1-3):</strong> {{ $item->sensori_aroma ?? '-' }}</div>
                    <div class="col-md-3"><strong>Texture (1-3):</strong> {{ $item->sensori_texture ?? '-' }}</div>
                </div>
                <hr>
                
                {{-- Bagian Temuan (Sudah ada) --}}
                <h6>Temuan</h6>
                <p>
                    @if($item->temuan_jamur) <span class="badge bg-danger">Jamur</span> @endif
                    @if($item->temuan_lendir) <span class="badge bg-danger">Lendir</span> @endif
                    @if($item->temuan_pinehole) <span class="badge bg-danger">Pinehole</span> @endif
                    @if($item->temuan_kejepit) <span class="badge bg-danger">Kejepit</span> @endif
                    @if($item->temuan_seal) <span class="badge bg-danger">Seal Halus/Lepas</span> @endif
                    @if(!$item->temuan_jamur && !$item->temuan_lendir && !$item->temuan_pinehole && !$item->temuan_kejepit && !$item->temuan_seal)
                        <span class="badge bg-success">Tidak Ada Temuan</span>
                    @endif
                </p>
                <hr>
                
                {{-- (DIUPDATE) Mengisi data Parameter Lab --}}
                <h6>Parameter Lab</h6>
                <div class="row">
                    <div class="col-md-4"><strong>Kadar Garam:</strong> {{ $item->lab_garam ?? '-' }}</div>
                    <div class="col-md-4"><strong>Kadar Air:</strong> {{ $item->lab_air ?? '-' }}</div>
                    <div class="col-md-4"><strong>Mikro:</strong> {{ $item->lab_mikro ?? '-' }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection