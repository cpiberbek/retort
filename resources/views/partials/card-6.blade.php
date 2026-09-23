<style>
    @keyframes issueLabelAssemble {
        0% { opacity: 0; transform: translateY(-10px); }
        100% { opacity: 1; transform: translateY(0); }
    }
    @keyframes issueTitleAssemble {
        0% { opacity: 0; transform: translateX(-20px); }
        100% { opacity: 1; transform: translateX(0); }
    }
    @keyframes issueLineAssemble {
        0% { width: 0; opacity: 0; }
        100% { width: 100%; opacity: 1; }
    }
    @keyframes issueBodyAssemble {
        0% { opacity: 0; transform: translateY(8px); }
        100% { opacity: 1; transform: translateY(0); }
    }

    .issue-label { animation: issueLabelAssemble .35s cubic-bezier(.22,1,.36,1) both; }
    .issue-title { animation: issueTitleAssemble .45s cubic-bezier(.22,1,.36,1) .15s both; }
    .issue-line { width: 100%; animation: issueLineAssemble .5s cubic-bezier(.22,1,.36,1) .35s both; }
    .issue-body { animation: issueBodyAssemble .4s cubic-bezier(.22,1,.36,1) .5s both; }

    .issue-select {
        height: 32px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background-color: #fff;
        color: #441b05;
        font-size: 12px;
        padding: 4px 10px;
    }

    .issue-pill {
        font-size: 14px;
        font-weight: 600;
        padding: 7px 18px;
        border-radius: 999px;
        border: 1px solid #e2e8f0;
        background: #fff;
        color: #94a3b8;
        cursor: pointer;
        white-space: nowrap;
    }

    .issue-pill[data-jenis=""].active {
        background: #0d6efd;
        border-color: #0d6efd;
        color: #fff;
    }

    .issue-pill[data-jenis="progress"].active {
        background: #0dcaf0;
        border-color: #0dcaf0;
        color: #fff;
    }

    .issue-pill[data-jenis="penyelesaian"].active {
        background: #46bb84;
        border-color: #46bb84;
        color: #fff;
    }

    .issue-pill[data-jenis="update"].active {
        background: #ea580c;
        border-color: #ea580c;
        color: #fff;
    }

    .issue-list-item {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 10px 12px;
        margin-bottom: 8px;
    }

    .issue-list-item:last-child {
        margin-bottom: 0;
    }

    .issue-scroll {
        max-height: 260px;
        overflow-y: auto;
    }

    .issue-empty {
        text-align: center;
        color: #94a3b8;
        font-size: 12px;
        padding: 24px 0;
    }
</style>

<div class="py-2" id="issueComplainCard">

    <div class="issue-label" style="font-size: 10px; letter-spacing: 3px; color: #9a3412; font-weight: 600;">
        QA MONITORING
    </div>

    <div class="d-flex align-items-center justify-content-between mt-1">
        <div class="issue-title" style="font-size: 24px; line-height: 1; font-weight: 600; color: #441b05;">
            Issue & Complain
        </div>
    </div>

    <div class="mt-3 issue-line" style="height: 2px; background: linear-gradient(to right, #ea580c 45%, #e2e8f0 43%);"></div>

    <div class="issue-body mt-3">

        <div class="d-flex align-items-center flex-wrap gap-2 mb-2">
            <input type="date" id="issueDateFilter" class="issue-select" style="width: 150px;">
            <input type="text" id="issueSearchFilter" class="issue-select" style="width: 200px;" placeholder="Cari issue...">
            <button type="button" id="issueResetFilter" class="btn btn-sm btn-outline-secondary" style="font-size: 14px;">
                Reset
            </button>
        </div>

        <div class="d-flex align-items-center flex-wrap gap-3 mb-3" id="issueJenisFilter">
            <span class="issue-pill active" data-jenis="">Semua</span>
            <span class="issue-pill" data-jenis="progress">Progress</span>
            <span class="issue-pill" data-jenis="penyelesaian">Penyelesaian</span>
            <span class="issue-pill" data-jenis="update">Update</span>
        </div>

        <div id="issueListContainer">
            @include('partials.dashboard.card-6-list', ['issueComplains' => $issueComplains])
        </div>

    </div>

</div>

<div class="modal fade" id="issueDetailModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"> Detail Data Issue & Complain</h5>
                <button type="button" class="close" data-bs-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body text-left" id="issueDetailModalBody">
                {{-- diisi via JS --}}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function () {

    function loadIssues(page = 1) {
        const params = {
            date: $('#issueDateFilter').val(),
            search: $('#issueSearchFilter').val(),
            jenis: $('#issueJenisFilter .active').data('jenis') || '',
            page: page,
        };

        $.get("{{ route('dashboard.issue-complain.filter') }}", params, function (html) {
            $('#issueListContainer').html(html);
        });
    }

    $('#issueDateFilter').on('change', function () {
        loadIssues(1);
    });

    let issueSearchTimer;
    $('#issueSearchFilter').on('input', function () {
        clearTimeout(issueSearchTimer);
        issueSearchTimer = setTimeout(function () {
            loadIssues(1);
        }, 500);
    });

    $(document).on('click', '#issueJenisFilter .issue-pill', function () {
        $('#issueJenisFilter .issue-pill').removeClass('active');
        $(this).addClass('active');
        loadIssues(1);
    });

    $('#issueResetFilter').on('click', function () {
        $('#issueDateFilter').val('');
        $('#issueSearchFilter').val('');
        $('#issueJenisFilter .issue-pill').removeClass('active');
        $('#issueJenisFilter .issue-pill[data-jenis=""]').addClass('active');
        loadIssues(1);
    });

    $(document).on('click', '.issue-page-link', function (e) {
        e.preventDefault();
        const page = $(this).data('page');
        loadIssues(page);
    });

    $(document).on('click', '.issue-detail-btn', function () {
        const judul_isu = $(this).data('judul_isu');
        const detail = $(this).data('detail');

        $('#issueDetailModalBody').html(
            '<div class="mb-2"><strong>Topik:<br></strong> "' + $('<div>').text(judul_isu).html() + '"</div>' +
            '<div><strong>Detail:</strong><br>' + $('<div>').text(detail).html() + '</div>'
        );

        $('#issueDetailModal').modal('show');
    });

});
</script>
@endpush