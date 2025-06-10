@extends('admin.master')

@section('title', 'Manajemen Cuti')

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.css">
<style>
    .stat-card {
        transition: transform 0.3s;
        border-radius: 10px;
    }
    .stat-card:hover {
        transform: translateY(-3px);
    }
    .status-badge {
        font-size: 0.85rem;
        padding: 0.35em 0.65em;
    }
</style>
@endsection

@section('content')
<div class="container-fluid mt-4">
    <!-- Alert Messages -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title mb-0">Manajemen Cuti</h4>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#cutiModal">
                            <i class="ti ti-plus me-1"></i> Ajukan Cuti Baru
                        </button>
                    </div>
                    
                    <!-- Statistics Cards -->
                   @php
                        $jatah = $pegawai->jatahtahunan;

                        $cutiTerpakai = $pegawai->cuti()
                            ->where('status_cuti', 'Disetujui')
                            ->get()
                            ->sum(function($cuti) {
                                return \Carbon\Carbon::parse($cuti->tanggal_mulai)
                                    ->diffInDays(\Carbon\Carbon::parse($cuti->tanggal_selesai)) + 1;
                            });

                        $sisaCuti = $jatah - $cutiTerpakai;
                    @endphp

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card stat-card bg-light">
                                <div class="card-body d-flex align-items-center">
                                    <div>
                                        <div class="text-muted">Jatah Tahunan</div>
                                        <h2 class="mb-0">{{ $jatah }} Hari</h2>
                                    </div>
                                    <div class="ms-auto">
                                        <i class="ti ti-calendar text-primary fs-4"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card stat-card bg-light">
                                <div class="card-body d-flex align-items-center">
                                    <div>
                                        <div class="text-muted">Cuti Terpakai</div>
                                        <h2 class="mb-0">{{ $cutiTerpakai }} Hari</h2>
                                    </div>
                                    <div class="ms-auto">
                                        <i class="ti ti-clock text-danger fs-4"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card stat-card bg-light">
                                <div class="card-body d-flex align-items-center">
                                    <div>
                                        <div class="text-muted">Sisa Cuti</div>
                                        <h2 class="mb-0">{{ $sisaCuti }} Hari</h2>
                                    </div>
                                    <div class="ms-auto">
                                        <i class="ti ti-check text-success fs-4"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Data Table -->
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Pengajuan Cuti Terbaru</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Jenis Cuti</th>
                                            <th>Tanggal Pengajuan</th>
                                            <th>Tanggal Cuti</th>
                                            <th>Durasi</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($cuti as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $item->jenisCuti->nama_jenis_cuti ?? 'N/A' }}</td>
                                            <td>{{ \Carbon\Carbon::parse($item->tanggal_pengajuan)->format('d/m/Y') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d/m/Y') }}</td>
                                            <td>
                                                @php
                                                    $start = \Carbon\Carbon::parse($item->tanggal_mulai);
                                                    $end = \Carbon\Carbon::parse($item->tanggal_selesai);
                                                    $diffInDays = $start->diffInDays($end) + 1;
                                                @endphp
                                                {{ $diffInDays }} hari
                                            </td>
                                            <td>
                                                @if($item->status_cuti == 'Disetujui')
                                                    <span class="badge bg-success status-badge">Disetujui</span>
                                                @elseif($item->status_cuti == 'Ditolak')
                                                    <span class="badge bg-danger status-badge">Ditolak</span>
                                                @else
                                                    <span class="badge bg-warning text-dark status-badge">Menunggu</span>
                                                @endif
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-info" onclick="showDetail('{{ $item->id_cuti }}')">
                                                    <i class="ti ti-eye"></i>
                                                </button>
                                                @if($item->status_cuti == 'Menunggu')
                                                <button class="btn btn-sm btn-danger ms-1" onclick="cancelLeave('{{ $item->id_cuti }}')">
                                                    <i class="ti ti-x"></i>
                                                </button>
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="7" class="text-center">Tidak ada data pengajuan cuti</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pengajuan Cuti -->
<div class="modal fade" id="cutiModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white">Form Pengajuan Cuti</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="" method="POST" id="cutiForm">
                    @csrf
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Pegawai</label>
                            <input type="text" class="form-control" value="{{ $pegawai->nama ?? '' }}" readonly>
                            <input type="hidden" name="id_pegawai" value="{{ $pegawai->id_pegawai ?? '' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Telepon</label>
                            <input type="text" class="form-control" value="{{ $pegawai->no_hp ?? '' }}" readonly>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Jenis Cuti <span class="text-danger">*</span></label>
                            <select class="form-select" name="id_jenis_cuti" id="jenisCuti" required>
                                <option value="">-- Pilih Jenis Cuti --</option>
                                @foreach(\App\Models\JenisCuti::all() as $jenis)
                                <option value="{{ $jenis->id_jenis_cuti }}" data-max="{{ $jenis->max_hari_cuti }}">
                                    {{ $jenis->nama_jenis_cuti }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Maksimal Hari</label>
                            <input type="text" id="maxHari" class="form-control" readonly>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                            <input type="date" id="tanggal_mulai" name="tanggal_mulai" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                            <input type="date" id="tanggal_selesai" name="tanggal_selesai" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jumlah Hari</label>
                        <input type="number" id="jumlahHari" class="form-control" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Keterangan <span class="text-danger">*</span></label>
                        <textarea name="keterangan" class="form-control" rows="3" placeholder="Jelaskan alasan cuti..." required></textarea>
                    </div>

                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" id="konfirmasi" required>
                        <label class="form-check-label" for="konfirmasi">
                            Saya menyatakan bahwa data yang saya isi benar dan dapat dipertanggungjawabkan.
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="submitBtn">Ajukan Cuti</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Detail Pengajuan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailContent">
                <div class="text-center">
                    <div class="spinner-border" role="status"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Set minimum date to today
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('tanggal_mulai').min = today;
    document.getElementById('tanggal_selesai').min = today;
    
    // Handle leave type selection
    document.getElementById('jenisCuti').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const maxDays = selectedOption.getAttribute('data-max');
        
        if (maxDays) {
            document.getElementById('maxHari').value = maxDays + ' hari';
        } else {
            document.getElementById('maxHari').value = '';
        }
        
        // Reset dates
        document.getElementById('tanggal_mulai').value = '';
        document.getElementById('tanggal_selesai').value = '';
        document.getElementById('jumlahHari').value = '';
    });
    
    // Calculate days when dates change
    function calculateDays() {
        const startDate = document.getElementById('tanggal_mulai').value;
        const endDate = document.getElementById('tanggal_selesai').value;
        
        if (startDate && endDate) {
            const start = new Date(startDate);
            const end = new Date(endDate);
            
            if (end < start) {
                alert('Tanggal selesai tidak boleh sebelum tanggal mulai!');
                document.getElementById('tanggal_selesai').value = '';
                document.getElementById('jumlahHari').value = '';
                return;
            }
            
            // Calculate working days
            let count = 0;
            let current = new Date(start);
            
            while (current <= end) {
                const dayOfWeek = current.getDay();
                if (dayOfWeek !== 0 && dayOfWeek !== 6) { // Not weekend
                    count++;
                }
                current.setDate(current.getDate() + 1);
            }
            
            document.getElementById('jumlahHari').value = count;
            
            // Check max days
            const jenisCuti = document.getElementById('jenisCuti');
            const maxDays = jenisCuti.options[jenisCuti.selectedIndex].getAttribute('data-max');
            
            if (maxDays && count > parseInt(maxDays)) {
                alert(`Jumlah hari cuti (${count} hari) melebihi batas maksimal (${maxDays} hari).`);
                document.getElementById('tanggal_selesai').value = '';
                document.getElementById('jumlahHari').value = '';
            }
        }
    }
    
    // Add event listeners for date calculation
    document.getElementById('tanggal_mulai').addEventListener('change', calculateDays);
    document.getElementById('tanggal_selesai').addEventListener('change', calculateDays);
    
    // Form submission
    document.getElementById('submitBtn').addEventListener('click', function() {
        const form = document.getElementById('cutiForm');
        
        if (form.checkValidity()) {
            // Add hidden fields
            const todayDate = new Date().toISOString().split('T')[0];
            
            // Add tanggal_pengajuan
            let hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'tanggal_pengajuan';
            hiddenInput.value = todayDate;
            form.appendChild(hiddenInput);
            
            // Add status
            hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'status_cuti';
            hiddenInput.value = 'Menunggu';
            form.appendChild(hiddenInput);
            
            form.submit();
        } else {
            form.reportValidity();
        }
    });
});

// Show detail function
function showDetail(id) {
    const modal = new bootstrap.Modal(document.getElementById('detailModal'));
    const content = document.getElementById('detailContent');
    
    content.innerHTML = '<div class="text-center"><div class="spinner-border"></div></div>';
    modal.show();
    
    // Simulate fetch (replace with actual API call)
    setTimeout(() => {
        content.innerHTML = `
            <table class="table table-borderless">
                <tr><td><strong>ID Cuti:</strong></td><td>${id}</td></tr>
                <tr><td><strong>Status:</strong></td><td><span class="badge bg-warning">Menunggu</span></td></tr>
                <tr><td><strong>Keterangan:</strong></td><td>Detail pengajuan cuti</td></tr>
            </table>
        `;
    }, 1000);
}

// Cancel leave function  
function cancelLeave(id) {
    if (confirm('Apakah Anda yakin ingin membatalkan pengajuan cuti ini?')) {
        // Add your cancel logic here
        alert('Pengajuan cuti dibatalkan');
        location.reload();
    }
}
</script>
@endpush