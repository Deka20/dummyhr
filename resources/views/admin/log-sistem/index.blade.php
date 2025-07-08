@extends('admin.master')

@section('title', 'Log Sistem')
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.log-sistem.index') }}">Log Sistem</a></li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Log Sistem</h5>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-danger btn-sm" onclick="clearAllLogs()">
                        <i class="ti ti-trash"></i> Hapus Semua
                    </button>
                </div>
            </div>
            <div class="card-body">
                <!-- Filter Section -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <form method="GET" action="{{ route('admin.log-sistem.index') }}">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" placeholder="Cari aksi atau nama user..." value="{{ request('search') }}">
                                <button class="btn btn-outline-secondary" type="submit">
                                    <i class="ti ti-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-8">
                        <form method="GET" action="{{ route('admin.log-sistem.index') }}" class="d-flex gap-2">
                            <input type="hidden" name="search" value="{{ request('search') }}">
                            <input type="date" name="tanggal_mulai" class="form-control" value="{{ request('tanggal_mulai') }}" placeholder="Tanggal Mulai">
                            <input type="date" name="tanggal_selesai" class="form-control" value="{{ request('tanggal_selesai') }}" placeholder="Tanggal Selesai">
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-filter"></i> Filter
                            </button>
                            <a href="{{ route('admin.log-sistem.index') }}" class="btn btn-secondary">
                                <i class="ti ti-refresh"></i> Reset
                            </a>
                        </form>
                    </div>
                </div>

                <!-- Table Section -->
                <div class="table-responsive">
                   <table class="table table-bordered table-sm table-hover">
    <thead class="table-light">
        <tr>
            <th>Waktu</th>
            <th>User</th>
            <th>Pesan</th>
        </tr>
    </thead>
    <tbody>
        @forelse($logs as $log)
            <tr>
                <td>
                    <code>{{ \Carbon\Carbon::parse($log->created_at)->format('Y-m-d H:i:s') }}</code>
                </td>
                <td>
                    <code>{{ $log->nama_user ?? 'System' }}</code>
                </td>
                <td>
                    <code>{{ $log->keterangan }}</code>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center text-muted">
                    Tidak ada log ditemukan.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
                </div>

                <!-- Pagination -->
                @if($logs->hasPages())
                <div class="d-flex justify-content-center mt-3">
                    {{ $logs->links() }}
                </div>
                @endif

                <!-- Info -->
                <div class="mt-3">
                    <small class="text-muted">
                        Menampilkan {{ $logs->firstItem() ?? 0 }} sampai {{ $logs->lastItem() ?? 0 }} dari {{ $logs->total() }} data
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus log ini?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Hapus</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="clearAllModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus Semua</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-danger"><strong>Peringatan!</strong></p>
                <p>Apakah Anda yakin ingin menghapus semua log sistem? Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmClearAll">Hapus Semua</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
let deleteLogId = null;

function deleteLog(id) {
    deleteLogId = id;
    $('#deleteModal').modal('show');
}

function clearAllLogs() {
    $('#clearAllModal').modal('show');
}

$(document).ready(function() {
    // Konfirmasi hapus single log
    $('#confirmDelete').click(function() {
        if (deleteLogId) {
            $.ajax({
                url: `/admin/log-sistem/${deleteLogId}`,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        $('#deleteModal').modal('hide');
                        location.reload();
                    } else {
                        alert('Gagal menghapus log: ' + response.message);
                    }
                },
                error: function() {
                    alert('Terjadi kesalahan saat menghapus log');
                }
            });
        }
    });

    // Konfirmasi hapus semua log
    $('#confirmClearAll').click(function() {
        $.ajax({
            url: '{{ route("admin.log-sistem.clear-all") }}',
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $('#clearAllModal').modal('hide');
                    location.reload();
                } else {
                    alert('Gagal menghapus semua log: ' + response.message);
                }
            },
            error: function() {
                alert('Terjadi kesalahan saat menghapus semua log');
            }
        });
    });
});
</script>
@endsection