@extends('admin.master')

@section('title', 'Manajemen Cuti')

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.css">
<style>
    .stat-card {
        transition: all 0.3s;
        border-radius: 10px;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .status-badge {
        font-size: 0.85rem;
        padding: 0.35em 0.65em;
    }
</style>
@endsection

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title mb-0">Manajemen Cuti</h4>
                        <div>
                            <button class="btn btn-outline-secondary me-2">
                                <i class="ti ti-history me-1"></i> Riwayat Cuti
                            </button>
                            <button class="btn btn-outline-secondary me-2">
                                <i class="ti ti-calendar me-1"></i> Jadwal
                            </button>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#cutiModal">
                                <i class="ti ti-plus me-1"></i> Ajukan Cuti Baru
                            </button>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card stat-card bg-light border-0 shadow-sm">
                                <div class="card-body d-flex align-items-center">
                                    <div>
                                        <div class="text-muted">Jatah Tahunan</div>
                                        <h2 class="mb-0">12 Hari</h2>
                                    </div>
                                    <div class="ms-auto">
                                        <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                                            <i class="ti ti-calendar text-primary fs-4"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card stat-card bg-light border-0 shadow-sm">
                                <div class="card-body d-flex align-items-center">
                                    <div>
                                        <div class="text-muted">Cuti Terpakai</div>
                                        <h2 class="mb-0">3 Hari</h2>
                                    </div>
                                    <div class="ms-auto">
                                        <div class="rounded-circle bg-danger bg-opacity-10 p-3">
                                            <i class="ti ti-clock text-danger fs-4"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card stat-card bg-light border-0 shadow-sm">
                                <div class="card-body d-flex align-items-center">
                                    <div>
                                        <div class="text-muted">Sisa Cuti</div>
                                        <h2 class="mb-0">9 Hari</h2>
                                    </div>
                                    <div class="ms-auto">
                                        <div class="rounded-circle bg-success bg-opacity-10 p-3">
                                            <i class="ti ti-check text-success fs-4"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Pengajuan Cuti Terbaru</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="cuti-table" class="table table-striped table-hover">
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
                                            <td>{{ $item->jenisCuti->nama_jenis_cuti }}</td>
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
                                                <button class="btn btn-sm btn-info" title="Detail" onclick="showDetail('{{ $item->id_cuti }}')">
                                                    <i class="ti ti-eye"></i>
                                                </button>
                                                @if($item->status_cuti == 'Menunggu')
                                                <button class="btn btn-sm btn-danger" title="Batalkan" onclick="cancelLeave('{{ $item->id_cuti }}')">
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
<div class="modal fade" id="cutiModal" tabindex="-1" aria-labelledby="cutiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #0056b3; color: white;">
                <h5 class="modal-title text-white" id="cutiModalLabel">Form Pengajuan Cuti</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" method="POST" id="cutiForm">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Pegawai</label>
                            <input type="text" class="form-control" value="{{ $pegawai->nama_pegawai }}" readonly>
                            <input type="hidden" name="id_pegawai" value="{{ $pegawai->id_pegawai }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">NIP</label>
                            <input type="text" class="form-control" value="{{ $pegawai->nip }}" readonly>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Jenis Cuti <span class="text-danger">*</span></label>
                            <select class="form-select" name="id_jenis_cuti" id="jenisCuti" required>
                                <option value="">-- Pilih Jenis Cuti --</option>
                                @foreach(\App\Models\JenisCuti::all() as $jenis)
                                <option value="{{ $jenis->id_jenis_cuti }}" data-max="{{ $jenis->max_hari_cuti }}">{{ $jenis->nama_jenis_cuti }}</option>
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
                            <input type="text" id="tanggal_mulai" name="tanggal_mulai" class="form-control datepicker" placeholder="YYYY-MM-DD" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                            <input type="text" id="tanggal_selesai" name="tanggal_selesai" class="form-control datepicker" placeholder="YYYY-MM-DD" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jumlah Hari</label>
                        <input type="number" id="jumlahHari" class="form-control" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Keterangan <span class="text-danger">*</span></label>
                        <textarea name="keterangan" class="form-control" rows="3" placeholder="Jelaskan alasan cuti Anda..." required></textarea>
                    </div>

                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" name="konfirmasi" id="konfirmasi" required>
                        <label class="form-check-label" for="konfirmasi">
                            Saya menyatakan bahwa data yang saya isi benar dan dapat dipertanggungjawabkan.
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn" style="background-color: #0056b3; color: white;" id="submitCutiBtn">Ajukan Cuti</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Cuti -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #0056b3; color: white;">
                <h5 class="modal-title text-white" id="detailModalLabel">Detail Pengajuan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="detailContent">
                <div class="text-center">
                    <div class="spinner-border" style="color: #0056b3;" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Initialize Flatpickr datepickers
    const startDatePicker = flatpickr("#tanggal_mulai", {
        dateFormat: "Y-m-d",
        minDate: "today",
        onChange: calculateDays
    });
    
    const endDatePicker = flatpickr("#tanggal_selesai", {
        dateFormat: "Y-m-d",
        minDate: "today",
        onChange: calculateDays
    });
    
    // Handle leave type selection to update max days
    document.getElementById('jenisCuti').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const maxDays = selectedOption.getAttribute('data-max');
        
        if (maxDays) {
            document.getElementById('maxHari').value = maxDays + ' hari';
        } else {
            document.getElementById('maxHari').value = '';
        }
        
        // Reset dates when changing leave type
        startDatePicker.clear();
        endDatePicker.clear();
        document.getElementById('jumlahHari').value = '';
    });
    
    // Calculate days between dates
    function calculateDays() {
        const startDate = document.getElementById('tanggal_mulai').value;
        const endDate = document.getElementById('tanggal_selesai').value;
        
        if (startDate && endDate) {
            const start = new Date(startDate);
            const end = new Date(endDate);
            
            // Check if end date is before start date
            if (end < start) {
                alert('Tanggal selesai tidak boleh sebelum tanggal mulai!');
                endDatePicker.clear();
                document.getElementById('jumlahHari').value = '';
                return;
            }
            
            // Calculate working days (excluding weekends)
            let count = 0;
            let current = new Date(start);
            
            while (current <= end) {
                // Check if current day is not weekend (0 = Sunday, 6 = Saturday)
                const dayOfWeek = current.getDay();
                if (dayOfWeek !== 0 && dayOfWeek !== 6) {
                    count++;
                }
                
                // Move to next day
                current.setDate(current.getDate() + 1);
            }
            
            document.getElementById('jumlahHari').value = count;
            
            // Check if exceeds max days
            const jenisCuti = document.getElementById('jenisCuti');
            const maxDays = jenisCuti.options[jenisCuti.selectedIndex].getAttribute('data-max');
            
            if (maxDays && count > parseInt(maxDays)) {
                alert(`Jumlah hari cuti (${count} hari) melebihi batas maksimal (${maxDays} hari) untuk jenis cuti ini.`);
                endDatePicker.clear();
                document.getElementById('jumlahHari').value = '';
            }
        }
    }
    
    // Form validation and submission
    document.getElementById('submitCutiBtn').addEventListener('click', function() {
        const form = document.getElementById('cutiForm');
        
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
            
            // Add validation classes to all form elements
            Array.from(form.elements).forEach((input) => {
                if (input.required && !input.value) {
                    input.classList.add('is-invalid');
                } else {
                    input.classList.remove('is-invalid');
                }
            });
            
            // Check confirmation checkbox
            const konfirmasi = document.getElementById('konfirmasi');
            if (!konfirmasi.checked) {
                konfirmasi.classList.add('is-invalid');
            }
            
            return false;
        }
        
        // Add current date as tanggal_pengajuan
        const todayDate = new Date().toISOString().split('T')[0];
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'tanggal_pengajuan';
        hiddenInput.value = todayDate;
        form.appendChild(hiddenInput);
        
        // Set status to Menunggu
        const statusInput = document.createElement('input');
        statusInput.type = 'hidden';
        statusInput.name = 'status_cuti';
        statusInput.value = 'Menunggu';
        form.appendChild(statusInput);
        
        // Submit the form
        form.submit();
    });
    
    // Remove validation styling on input
    const formInputs = document.querySelectorAll('#cutiForm input, #cutiForm select, #cutiForm textarea');
    formInputs.forEach(input => {
        input.addEventListener('input', function() {
            this.classList.remove('is-invalid');
        });
    });
    
    // Initialize DataTables if available
    if (typeof $.fn.DataTable !== 'undefined') {
        $('#cuti-table').DataTable({
            responsive: true,
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ entri",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Selanjutnya",
                    previous: "Sebelumnya"
                }
            }
        });
    }
});

// Function to show detail modal
function showDetail(id) {
    const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
    const detailContent = document.getElementById('detailContent');
    
    // Show loading
    detailContent.innerHTML = `
        <div class="text-center">
            <div class="spinner-border" style="color: #0056b3;" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;
    
    detailModal.show();
    
    // Fetch cuti details
    fetch(`/admin/cuti/${id}`)
        .then(response => response.json())
        .then(data => {
            const statusClass = {
                'Disetujui': 'success',
                'Ditolak': 'danger',
                'Menunggu': 'warning'
            };
            
            detailContent.innerHTML = `
                <div class="card border-0">
                    <div class="card-body p-0">
                        <table class="table table-borderless">
                            <tr>
                                <td width="40%"><strong>Jenis Cuti</strong></td>
                                <td>${data.jenis_cuti.nama_jenis_cuti}</td>
                            </tr>
                            <tr>
                                <td><strong>Pegawai</strong></td>
                                <td>${data.pegawai.nama_pegawai}</td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal Pengajuan</strong></td>
                                <td>${new Date(data.tanggal_pengajuan).toLocaleDateString('id-ID')}</td>
                            </tr>
                            <tr>
                                <td><strong>Periode Cuti</strong></td>
                                <td>${new Date(data.tanggal_mulai).toLocaleDateString('id-ID')} - ${new Date(data.tanggal_selesai).toLocaleDateString('id-ID')}</td>
                            </tr>
                            <tr>
                                <td><strong>Status</strong></td>
                                <td><span class="badge bg-${statusClass[data.status_cuti]}">${data.status_cuti}</span></td>
                            </tr>
                            <tr>
                                <td><strong>Keterangan</strong></td>
                                <td>${data.keterangan || '-'}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            `;
        })
        .catch(error => {
            detailContent.innerHTML = `
                <div class="alert alert-danger">
                    Terjadi kesalahan saat mengambil data. Silakan coba lagi.
                </div>
            `;
            console.error('Error:', error);
        });
}

// Function to cancel leave request
function cancelLeave(id) {
    if (confirm('Apakah Anda yakin ingin membatalkan pengajuan cuti ini?')) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        fetch(`/admin/cuti/${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Pengajuan cuti berhasil dibatalkan');
                window.location.reload();
            } else {
                alert('Gagal membatalkan pengajuan cuti');
            }
        })
        .catch(error => {
            alert('Terjadi kesalahan saat membatalkan pengajuan');
            console.error('Error:', error);
        });
    }
}
</script>
@endpush