@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3>
                    <i class="bi bi-trash"></i>
                    Recycle Bin Pemeriksaan Stuffing Sosis Retort
                </h3>

                <a href="{{ route('stuffing.index') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="table-responsive">

                <table class="table table-striped table-bordered table-hover align-middle mb-0">

                    <thead class="table-danger text-center">
                        <tr>
                            <th>No.</th>
                            <th>Date | Shift</th>
                            <th>Nama Produk</th>
                            <th>Kode Produksi</th>
                            <th>Exp. Date</th>
                            <th>Kode Mesin</th>
                            <th>Jam Mulai</th>
                            <th>Pemeriksaan</th>
                            <th>QC</th>
                            <th>Dihapus Pada</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($stuffing as $item)
                        <tr>

                            <td class="text-center">
                                {{ $loop->iteration }}
                            </td>

                            <td class="text-center">
                                {{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }}
                                || Shift: {{ $item->shift }}
                            </td>

                            <td class="text-center">
                                {{ $item->nama_produk }}
                            </td>

                            <td class="text-center">
                                {{ $item->mincing->kode_produksi ?? '-' }}
                            </td>

                            <td class="text-center">
                                {{ \Carbon\Carbon::parse($item->exp_date)->format('d-m-Y') }}
                            </td>

                            <td class="text-center">
                                {{ $item->kode_mesin }}
                            </td>

                            <td class="text-center">
                                {{ $item->jam_mulai ?? '-' }}
                            </td>

                            {{-- ================= DETAIL ================= --}}
                            <td class="text-center">

                                <button
                                class="btn btn-info btn-sm mb-2 toggle-btn"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#stuffingCollapse{{ $item->uuid }}">
                                Details
                            </button>

                            <div class="collapse" id="stuffingCollapse{{ $item->uuid }}">

                                <table class="table table-bordered table-striped table-sm text-center align-middle">

                                    <tbody>

                                        @php
                                        $fields = [
                                        ['type'=>'title','label'=>'Parameter Adonan'],
                                        ['type'=>'field','label'=>'Suhu (°C)','key'=>'suhu'],
                                        ['type'=>'field','label'=>'Sensori','key'=>'sensori'],

                                        ['type'=>'title','label'=>'Parameter Stuffing'],
                                        ['type'=>'field','label'=>'Kecepatan Stuffing','key'=>'kecepatan_stuffing'],
                                        ['type'=>'field','label'=>'Panjang/pcs (cm)','key'=>'panjang_pcs'],
                                        ['type'=>'field','label'=>'Berat/pcs (gr)','key'=>'berat_pcs'],
                                        ['type'=>'field','label'=>'Cek Vakum','key'=>'cek_vakum'],
                                        ['type'=>'field','label'=>'Kebersihan Seal','key'=>'kebersihan_seal'],
                                        ['type'=>'field','label'=>'Kekuatan Seal','key'=>'kekuatan_seal'],
                                        ['type'=>'field','label'=>'Diameter Klip (mm)','key'=>'diameter_klip'],
                                        ['type'=>'field','label'=>'Print Kode','key'=>'print_kode'],
                                        ['type'=>'field','label'=>'Lebar Cassing (mm)','key'=>'lebar_cassing'],
                                        ];
                                        @endphp

                                        @foreach ($fields as $field)

                                        @if ($field['type'] === 'title')

                                        <tr class="table-secondary">
                                            <td colspan="2" class="fw-bold text-start">
                                                {{ $field['label'] }}
                                            </td>
                                        </tr>

                                        @else

                                        @php
                                        $value = $item->{$field['key']} ?? null;

                                        $display = in_array(
                                        $field['key'],
                                        ['sensori','cek_vakum','kebersihan_seal','kekuatan_seal','print_kode']
                                        )
                                        ? (!empty($value) ? '✔' : '-')
                                        : ($value ?? '-');
                                        @endphp

                                        <tr>
                                            <td class="text-start">
                                                {{ $field['label'] }}
                                            </td>
                                            <td>
                                                {{ $display }}
                                            </td>
                                        </tr>

                                        @endif

                                        @endforeach

                                    </tbody>
                                </table>

                            </div>

                        </td>

                        {{-- ================= QC ================= --}}
                        <td class="text-center">
                            {{ $item->username }}
                        </td>

                        {{-- ================= DELETED ================= --}}
                        <td class="text-center">
                            {{ \Carbon\Carbon::parse($item->deleted_at)->format('d-m-Y H:i') }}
                        </td>

                        {{-- ================= ACTION ================= --}}
                        <td class="text-center">

                            <form action="{{ route('stuffing.restore', $item->uuid) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-success btn-sm">
                                    <i class="bi bi-arrow-clockwise"></i> Restore
                                </button>
                            </form>

                            <form
                            action="{{ route('stuffing.deletePermanent', $item->uuid) }}"
                            method="POST"
                            class="d-inline">

                            @csrf
                            @method('DELETE')

                            <button
                            class="btn btn-danger btn-sm"
                            onclick="return confirm('Hapus permanen?')">

                            <i class="bi bi-x-circle"></i> Delete
                        </button>

                    </form>

                </td>

            </tr>

            @empty
            <tr>
                <td colspan="11" class="text-center">
                    Recycle bin kosong.
                </td>
            </tr>
            @endforelse

        </tbody>

    </table>

</div>

<div class="d-flex justify-content-end mt-3">
    {{ $stuffing->links('pagination::bootstrap-5') }}
</div>

</div>
</div>

</div>
@endsection
