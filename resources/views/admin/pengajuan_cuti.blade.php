@extends('admin.master')

@section('title', 'Manajemen Cuti')

@section('content')
<div class="container mt-4">
<div class="card-body">
          <div class="dt-responsive table-responsive">
            <h4 class="card-title mb-3">Manajemen Cuti</h4>
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card bg-light">
                        <div class="card-body d-flex align-items-center">
                            <div>
                                <div class="text-muted">Sisa Cuti Tahunan</div>
                                <h2 class="mb-0">12 hari</h2>
                            </div>
                            <div class="ms-auto">
                                <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                                    <i class="ti ti-calendar text-primary fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-light">
                        <div class="card-body d-flex align-items-center">
                            <div>
                                <div class="text-muted">Cuti Terpakai</div>
                                <h2 class="mb-0">3 hari</h2>
                            </div>
                            <div class="ms-auto">
                                <div class="rounded-circle bg-danger bg-opacity-10 p-3">
                                    <i class="ti ti-clock text-danger fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between mb-4">
                <button class="btn btn-primary" id="toggleFormBtn">
                    <i class="ti ti-plus me-2"></i>Ajukan Cuti Baru
                </button>
                <div>
                    <button class="btn btn-outline-secondary me-2">Riwayat Cuti</button>
                    <button class="btn btn-outline-secondary">Jadwal</button>
                </div>
            </div>

            <div id="formCuti" style="display: none;">
                <form action="" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama pegawai</label>
                            <input type="text" class="form-control" value="Ahmad Firdaus" readonly>
                            <input type="hidden" name="employee_id" value="123">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">NIP</label>
                            <input type="text" class="form-control" value="2023123456" readonly>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Departemen</label>
                            <input type="text" class="form-control" value="IT Support" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jabatan</label>
                            <input type="text" class="form-control" value="Web Developer" readonly>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Jenis Cuti <span class="text-danger">*</span></label>
                            <select class="form-select" name="leave_type_id" required>
                                <option value="">-- Pilih Jenis Cuti --</option>
                                <option value="1" data-days="3">Cuti Tahunan</option>
                                <option value="2" data-days="5">Cuti Sakit</option>
                                <option value="3" data-days="1">Cuti Khusus</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Sisa Cuti Tahunan</label>
                            <input type="text" class="form-control" value="12 hari" readonly>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                            <input type="text" id="start_date" name="start_date" class="form-control" placeholder="yyyy-mm-dd" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                            <input type="text" id="end_date" name="end_date" class="form-control" placeholder="yyyy-mm-dd" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Jumlah Hari</label>
                            <input type="number" id="days_count" name="days_count" class="form-control" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Delegasi Tugas Kepada <span class="text-danger">*</span></label>
                            <select class="form-select" name="delegate_to" required>
                                <option value="">-- Pilih pegawai --</option>
                                <option value="2">Andi - Supervisor</option>
                                <option value="3">Sari - Admin</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Alasan Cuti <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control" rows="3" placeholder="Jelaskan alasan cuti Anda..." required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Dokumen Pendukung <small class="text-muted">(opsional)</small></label>
                        <input type="file" name="attachment" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                        <div class="form-text">Format: PDF, JPG, JPEG, PNG. Maks: 2MB</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kontak Darurat <span class="text-danger">*</span></label>
                        <input type="text" name="emergency_contact" class="form-control" placeholder="Nomor telepon darurat" required>
                    </div>

                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" name="confirmation" id="confirmation" required>
                        <label class="form-check-label" for="confirmation">
                            Saya menyatakan bahwa data yang saya isi benar dan dapat dipertanggungjawabkan.
                        </label>
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary" id="cancelBtn">
                            <i class="ti ti-x me-2"></i>Batal
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-send me-2"></i>Ajukan Cuti
                        </button>
                    </div>
                </form>
            </div>

            <div class="row">
    <div class="col-sm-12">
      <div class="card">
        <div class="card-header">
        <h5 class="">Pengajuan Cuti Terbaru</h5>
        </div>

        <div class="card-body">
          <div class="dt-responsive table-responsive">
            <table id="attendance-table" class="table table-striped table-bordered nowrap">
            <thead>
                        <tr>
                            <th>No</th>
                            <th>Jenis Cuti</th>
                            <th>Tanggal</th>
                            <th>Durasi</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>Cuti Tahunan</td>
                            <td>10/03/2025 - 12/03/2025</td>
                            <td>3 hari</td>
                            <td><span class="badge bg-success">Disetujui</span></td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Cuti Sakit</td>
                            <td>25/02/2025</td>
                            <td>1 hari</td>
                            <td><span class="badge bg-success">Disetujui</span></td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>Cuti Khusus</td>
                            <td>15/01/2025 - 17/01/2025</td>
                            <td>3 hari</td>
                            <td><span class="badge bg-warning text-dark">Expired</span></td>
                        </tr>
                    </tbody>
                    </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    
    const btnToggle = document.getElementById('toggleFormBtn');
    const formCuti = document.getElementById('formCuti');
    
    if (btnToggle && formCuti) {
        btnToggle.addEventListener('click', function () {
            const isHidden = formCuti.style.display === 'none' || formCuti.style.display === '';
            
            formCuti.style.display = isHidden ? 'block' : 'none';
            
            btnToggle.innerHTML = isHidden
                ? '<i class="ti ti-x me-2"></i>Tutup Form'
                : '<i class="ti ti-plus me-2"></i>Ajukan Cuti Baru';
        });
    } else {
        console.error('Toggle button or form element not found');
    }
    
    // Cancel Button
    const cancelBtn = document.getElementById('cancelBtn');
    if (cancelBtn && formCuti && btnToggle) {
        cancelBtn.addEventListener('click', function() {
            formCuti.style.display = 'none';
            btnToggle.innerHTML = '<i class="ti ti-plus me-2"></i>Ajukan Cuti Baru';
        });
    }
});
</script>
@endpush