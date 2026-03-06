@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4"><i class="bi bi-pencil-square"></i> Edit Data No. Lot Wire</h4>

            <form method="POST" action="{{ route('wire.edit_spv', $wire->uuid) }}">
                @csrf
                @method('PUT')

                {{-- Identitas --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white"><strong>Identitas Data Wire</strong></div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal</label>
                                <input type="date" name="date" class="form-control"
                                       value="{{ old('date', $wire->date) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Shift</label>
                                <select name="shift" class="form-control" required>
                                    <option value="">-- Pilih Shift --</option>
                                    <option value="1" {{ old('shift', $wire->shift)==1 ? 'selected':'' }}>Shift 1</option>
                                    <option value="2" {{ old('shift', $wire->shift)==2 ? 'selected':'' }}>Shift 2</option>
                                    <option value="3" {{ old('shift', $wire->shift)==3 ? 'selected':'' }}>Shift 3</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Varian</label>
                                <select name="nama_produk" class="form-control selectpicker" data-live-search="true" required>
                                    @foreach($produks as $produk)
                                        <option value="{{ $produk->nama_produk }}"
                                            {{ old('nama_produk', $wire->nama_produk) == $produk->nama_produk ? 'selected' : '' }}>
                                            {{ $produk->nama_produk }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nama Supplier</label>
                                <select name="nama_supplier" class="form-control selectpicker" data-live-search="true" required>
                                    <option value="">-- Pilih Supplier --</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->nama_supplier }}"
                                            {{ old('nama_supplier', $wire->nama_supplier) == $supplier->nama_supplier ? 'selected' : '' }}>
                                            {{ $supplier->nama_supplier }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Data Wire --}}
                <div class="card mb-4">
                    <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center">
                        <strong>Data Wire</strong>
                        <button type="button" id="addMesinRow" class="btn btn-primary btn-sm">+ Tambah Mesin</button>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-bordered table-sm text-center align-middle" id="wireTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Mesin</th>
                                    <th>Start</th>
                                    <th>End</th>
                                    <th>No. Lot</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="wireBody">
                                @if(!empty($wireData))
                                    @foreach($wireData as $mIndex => $mesinData)
                                        @php
                                            $mesinData['detail'] = isset($mesinData['detail']) && is_array($mesinData['detail'])
                                                ? $mesinData['detail'] : [];
                                            $rowspan = count($mesinData['detail']) ?: 1;
                                        @endphp
                                        @foreach($mesinData['detail'] as $bIndex => $batch)
                                            <tr class="{{ $bIndex==0 ? 'mesin-row':'batch-row' }}">
                                                @if($bIndex==0)
                                                <td rowspan="{{ $rowspan }}" class="mesin-cell">
                                                    <select name="data_wire[{{ $mIndex }}][mesin]" class="form-control form-control-sm" required>
                                                        <option value="">-- Pilih Mesin --</option>
                                                        @foreach($mesins as $mesin)
                                                            <option value="{{ $mesin->nama_mesin }}"
                                                                {{ ($mesinData['mesin']??'') == $mesin->nama_mesin ? 'selected' : '' }}>
                                                                {{ $mesin->nama_mesin }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                @endif
                                                <td><input type="time" name="data_wire[{{ $mIndex }}][detail][{{ $bIndex }}][start]" class="form-control form-control-sm" value="{{ $batch['start']??'' }}"></td>
                                                <td><input type="time" name="data_wire[{{ $mIndex }}][detail][{{ $bIndex }}][end]" class="form-control form-control-sm" value="{{ $batch['end']??'' }}"></td>
                                                <td><input type="text" name="data_wire[{{ $mIndex }}][detail][{{ $bIndex }}][no_lot]" class="form-control form-control-sm" value="{{ $batch['no_lot']??'' }}"></td>
                                                <td>
                                                    @if($bIndex==0)
                                                    <button type="button" class="btn btn-success btn-sm addBatchRow">+ Batch</button>
                                                    <button type="button" class="btn btn-danger btn-sm removeRow">Hapus</button>
                                                    @else
                                                    <button type="button" class="btn btn-danger btn-sm removeRow">Hapus</button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Catatan --}}
                <div class="card mb-4">
                    <div class="card-header bg-light"><strong>Catatan</strong></div>
                    <div class="card-body">
                        <textarea name="catatan" class="form-control" rows="3">{{ old('catatan', $wire->catatan) }}</textarea>
                    </div>
                </div>

                {{-- Tombol --}}
                <div class="d-flex justify-content-between mt-3">
                    <button class="btn btn-success w-auto"><i class="bi bi-save"></i> Update</button>
                    <a href="{{ route('wire.index') }}" class="btn btn-secondary w-auto"><i class="bi bi-arrow-left"></i> Kembali</a>
                </div>

            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
<script>
    $(document).ready(function(){
        $('.selectpicker').selectpicker();
    });
</script>

<script>
$(document).ready(function(){
    const mesinOptions = `{!! collect($mesins)
        ->map(fn($m)=>"<option value='{$m->nama_mesin}'>{$m->nama_mesin}</option>")
        ->implode('') !!}`;

    const tableBody = $('#wireBody');
    let mesinIndex = {{ count($wireData)-1 ?? 0 }};

    $('#addMesinRow').click(function(){
        mesinIndex++;
        const newRow = `
        <tr class="mesin-row">
            <td rowspan="1" class="mesin-cell">
                <select name="data_wire[${mesinIndex}][mesin]" class="form-control form-control-sm" required>
                    <option value="">-- Pilih Mesin --</option>
                    ${mesinOptions}
                </select>
            </td>
            <td><input type="time" name="data_wire[${mesinIndex}][detail][0][start]" class="form-control form-control-sm"></td>
            <td><input type="time" name="data_wire[${mesinIndex}][detail][0][end]" class="form-control form-control-sm"></td>
            <td><input type="text" name="data_wire[${mesinIndex}][detail][0][no_lot]" class="form-control form-control-sm"></td>
            <td>
                <button type="button" class="btn btn-success btn-sm addBatchRow">+ Batch</button>
                <button type="button" class="btn btn-danger btn-sm removeRow">Hapus</button>
            </td>
        </tr>`;
        tableBody.append(newRow);
    });

    tableBody.on('click', '.addBatchRow', function(){
        const mesinRow = $(this).closest('tr');
        const mesinIndex = mesinRow.find('select').attr('name').match(/\[(\d+)\]/)[1];
        const sameRows = mesinRow.nextAll('tr').filter(function(){
            const name = $(this).find('input[name*="[start]"]').attr('name');
            return name && name.startsWith(`data_wire[${mesinIndex}]`);
        });
        const batchCount = sameRows.length + 1;
        let lastEnd = sameRows.length>0 ? sameRows.last().find('input[name*="[end]"]').val() : mesinRow.find('input[name*="[end]"]').val();
        if(!lastEnd){
            const now = new Date();
            lastEnd = `${String(now.getHours()).padStart(2,'0')}:${String(now.getMinutes()).padStart(2,'0')}`;
        }
        const newBatch = `<tr class="batch-row">
            <td><input type="time" name="data_wire[${mesinIndex}][detail][${batchCount}][start]" class="form-control form-control-sm" value="${lastEnd}"></td>
            <td><input type="time" name="data_wire[${mesinIndex}][detail][${batchCount}][end]" class="form-control form-control-sm"></td>
            <td><input type="text" name="data_wire[${mesinIndex}][detail][${batchCount}][no_lot]" class="form-control form-control-sm"></td>
            <td><button type="button" class="btn btn-danger btn-sm removeRow">Hapus</button></td>
        </tr>`;
        if(sameRows.length>0){
            sameRows.last().after(newBatch);
        }else{
            mesinRow.after(newBatch);
        }
        const mesinCell = mesinRow.find('.mesin-cell');
        mesinCell.attr('rowspan', parseInt(mesinCell.attr('rowspan'))+1);
    });

    tableBody.on('click', '.removeRow', function(){
        const tr = $(this).closest('tr');
        if(tr.find('.mesin-cell').length){
            const rowspan = parseInt(tr.find('.mesin-cell').attr('rowspan'));
            tr.nextAll(':lt('+(rowspan-1)+')').remove();
        }else{
            const mesinRow = tr.prevAll('.mesin-row:first');
            const mesinCell = mesinRow.find('.mesin-cell');
            mesinCell.attr('rowspan', parseInt(mesinCell.attr('rowspan'))-1);
        }
        tr.remove();
    });
});
</script>

<style>
.table-bordered th, .table-bordered td { text-align:center; vertical-align:middle; }
.form-control-sm { min-width:120px; }
</style>
@endsection
