<div class="issue-scroll">
    @forelse($issueComplains as $data)
        <div class="issue-list-item">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div style="font-size: 13px; font-weight: 600; color: #441b05;">
                        {{ $data->judul_isu }}
                    </div>
                    <div style="font-size: 11px; color: #94a3b8; margin-top: 2px;">
                        {{ \Carbon\Carbon::parse($data->date)->format('d-m-Y') }}
                    </div>
                </div>

                <div>
                    @if($data->jenis === 'progress')
                        <span class="badge badge-info">Progress</span>
                    @elseif($data->jenis === 'penyelesaian')
                        <span class="badge badge-success">Penyelesaian</span>
                    @else
                        <span class="badge" style="background: #ea580c; color: #fff;">Update</span>
                    @endif
                </div>
            </div>

            <button type="button"
                class="btn btn-sm btn-link p-0 mt-1 issue-detail-btn"
                style="font-size: 11px;"
                data-judul_isu="{{ $data->judul_isu }}"
                data-detail="{{ $data->detail }}">
                Lihat Detail
            </button>
        </div>
    @empty
        <div class="issue-empty">Belum ada isu & komplain.</div>
    @endforelse
</div>

@if($issueComplains->hasPages())
    <div class="d-flex justify-content-between align-items-center mt-2" style="font-size: 11px;">

        @if($issueComplains->onFirstPage())
            <span style="
                display: inline-block;
                padding: 4px 10px;
                border: 1px solid #e2e8f0;
                border-radius: 6px;
                background: #f8fafc;
                color: #cbd5e1;
                font-weight: 600;
                cursor: not-allowed;
            ">
                &laquo; Sebelumnya
            </span>
        @else
            <a href="#"
                class="issue-page-link"
                data-page="{{ $issueComplains->currentPage() - 1 }}"
                style="
                    display: inline-block;
                    padding: 4px 10px;
                    border: 1px solid #ea580c;
                    border-radius: 6px;
                    background: #fff;
                    color: #ea580c;
                    font-weight: 600;
                    text-decoration: none;
                ">
                &laquo; Sebelumnya
            </a>
        @endif

        <span style="
            color: #64748b;
            font-weight: 600;
        ">
            Halaman {{ $issueComplains->currentPage() }} / {{ $issueComplains->lastPage() }}
        </span>

        @if($issueComplains->hasMorePages())
            <a href="#"
                class="issue-page-link"
                data-page="{{ $issueComplains->currentPage() + 1 }}"
                style="
                    display: inline-block;
                    padding: 4px 10px;
                    border: 1px solid #ea580c;
                    border-radius: 6px;
                    background: #fff;
                    color: #ea580c;
                    font-weight: 600;
                    text-decoration: none;
                ">
                Selanjutnya &raquo;
            </a>
        @else
            <span style="
                display: inline-block;
                padding: 4px 10px;
                border: 1px solid #e2e8f0;
                border-radius: 6px;
                background: #f8fafc;
                color: #cbd5e1;
                font-weight: 600;
                cursor: not-allowed;
            ">
                Selanjutnya &raquo;
            </span>
        @endif

    </div>
@endif


<div class="text-center mt-1">
    <span style="
        display: inline-block;
        padding: 3px 9px;
        border-radius: 999px;
        background: #f1f5f9;
        color: #64748b;
        font-size: 11px;
        font-weight: 600;
    ">
        {{ $issueComplains->total() }} Entry Data
        @if(request('jenis'))
            ({{ ucfirst(request('jenis')) }})
        @else
            (Semua)
        @endif
    </span>
</div>