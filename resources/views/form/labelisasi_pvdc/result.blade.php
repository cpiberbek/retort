<div class="modal fade" id="resultModal{{ $dep->uuid }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title">Detail Labelisasi PVDC</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body table-responsive">
                <table class="table table-bordered table-sm text-center align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Mesin</th>
                            <th>Kode Batch</th>
                            <th>Gambar</th>
                            <th>Ket</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dep->labelisasi_detail as $item)
                            <tr>
                                <td>{{ $item['mesin'] ?? '-' }}</td>

                                <td>
                                    {{ $item['mincing']->kode_produksi ?? 'Batch tidak ditemukan' }}
                                </td>

                                <td>
                                    @if(!empty($item['file']))
                                        @php
                                            $fileUrl = $item['file'];

                                            if (preg_match('/^https?:\/\/[^\/]+\/storage\/(.+)$/i', $fileUrl, $matches)) {
                                                $fileUrl = asset('storage/' . $matches[1]);
                                            } elseif (!preg_match('/^https?:\/\//i', $fileUrl)) {
                                                $fileUrl = asset('storage/' . ltrim($fileUrl, '/'));
                                            }
                                        @endphp

                                        <a href="{{ $fileUrl }}" target="_blank">
                                            <img src="{{ $fileUrl }}" width="50" class="img-thumbnail">
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>

                                <td>{{ $item['keterangan'] ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>