@extends('karyawan.master')

@section('title', 'Kuisioner Penilaian Kinerja Dosen & Laboran')

@section('content')
<div class="container">
    <!-- Header -->
    <div class="card mb-4">
        <div class="card-body text-center">
            <h3 class="text-primary mb-3">KUISIONER PENILAIAN KINERJA PEGAWAI</h3>
            <hr>
            <p class="text-muted mb-0">
                Selamat datang, <strong>{{ $pegawai->nama }}</strong>
            </p>
            <small class="text-muted">Departemen: {{ $nama_departemen }}</small>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Selection Form -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filter Penilaian</h5>
        </div>
        <div class="card-body">
            <form id="selectionForm">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="periode_penilaian" class="form-label">
                            <i class="fas fa-calendar me-1"></i>Periode Penilaian:
                        </label>
                        <select class="form-select" id="periode_penilaian" name="periode_penilaian" required>
                            <option value="">-- Pilih Periode Penilaian --</option>
                            @foreach($periode as $p)
                                <option value="{{ $p->id }}" data-tahun="{{ $p->tahun }}" data-semester="{{ $p->semester }}">
                                    {{ $p->tahun }} - {{ $p->semester }} ({{ $p->nama_periode }})
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">Pilih periode penilaian yang sedang aktif</div>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="departemen" class="form-label">
                            <i class="fas fa-building me-1"></i>Departemen:
                        </label>
                        <select class="form-select" id="departemen" name="departemen" required>
                            <option value="">-- Pilih Departemen --</option>
                            @foreach($departemen as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->nama_departemen }}</option>
                            @endforeach
                        </select>
                        <div class="form-text">Pilih departemen pegawai yang akan dinilai</div>
                    </div>
                </div>

                <div class="text-center">
                    <button type="button" id="cariDataBtn" class="btn btn-primary btn-lg" disabled>
                        <i class="fas fa-search me-2"></i>CARI PEGAWAI
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Period Info -->
    <div id="periodInfo" class="card mb-4" style="display: none;">
        <div class="card-body bg-light">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h6 class="mb-1">Informasi Periode Terpilih:</h6>
                    <div id="periodDetails" class="text-muted"></div>
                </div>
                <div class="col-md-4 text-end">
                    <span class="badge bg-success fs-6">
                        <i class="fas fa-check-circle me-1"></i>Aktif
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Results Section -->
    <div id="resultsSection" class="mt-4" style="display: none;">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-users me-2"></i>Daftar Pegawai
                </h5>
                <div id="pegawaiCount" class="badge bg-info fs-6"></div>
            </div>
            <div class="card-body">
                <!-- Pegawai List -->
                <div id="pegawaiList">
                    <!-- Will be populated by JavaScript -->
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Spinner Template -->
    <div id="loadingTemplate" style="display: none;">
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">Loading...</span>
            </div>
            <div class="mt-3 text-muted">Memuat data pegawai...</div>
        </div>
    </div>

    <!-- Empty State Template -->
    <div id="emptyStateTemplate" style="display: none;">
        <div class="text-center py-5">
            <i class="fas fa-users text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
            <h5 class="mt-3 text-muted">Tidak Ada Data Pegawai</h5>
            <p class="text-muted mb-0">Tidak ditemukan pegawai pada departemen yang dipilih</p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const periodeSelect = document.getElementById('periode_penilaian');
    const departemenSelect = document.getElementById('departemen');
    const cariDataBtn = document.getElementById('cariDataBtn');
    const resultsSection = document.getElementById('resultsSection');
    const periodInfo = document.getElementById('periodInfo');
    const periodDetails = document.getElementById('periodDetails');
    const pegawaiCount = document.getElementById('pegawaiCount');
    
    let selectedPeriode = null;
    let selectedDepartemen = null;

    // Handle periode penilaian change
    periodeSelect.addEventListener('change', function() {
        selectedPeriode = this.value;
        
        if (selectedPeriode) {
            const selectedOption = this.options[this.selectedIndex];
            const tahun = selectedOption.dataset.tahun;
            const semester = selectedOption.dataset.semester;
            const namaPeriode = selectedOption.textContent;
            
            // Show period info
            periodDetails.innerHTML = `
                <div class="row">
                    <div class="col-md-4">
                        <strong>Tahun:</strong> ${tahun}
                    </div>
                    <div class="col-md-4">
                        <strong>Semester:</strong> ${semester}
                    </div>
                    <div class="col-md-4">
                        <strong>Periode:</strong> ${namaPeriode.split(' (')[1]?.replace(')', '') || 'N/A'}
                    </div>
                </div>
            `;
            periodInfo.style.display = 'block';
        } else {
            periodInfo.style.display = 'none';
        }
        
        checkFormComplete();
        hideResults();
    });

    departemenSelect.addEventListener('change', function() {
        selectedDepartemen = this.value;
        checkFormComplete();
        hideResults();
    });

    function checkFormComplete() {
        const isComplete = selectedPeriode && selectedDepartemen;
        cariDataBtn.disabled = !isComplete;
        
        if (isComplete) {
            cariDataBtn.classList.remove('btn-secondary');
            cariDataBtn.classList.add('btn-primary');
        } else {
            cariDataBtn.classList.remove('btn-primary');
            cariDataBtn.classList.add('btn-secondary');
        }
    }

    function hideResults() {
        resultsSection.style.display = 'none';
    }

    // Handle cari data
    cariDataBtn.addEventListener('click', function() {
        if (!selectedPeriode || !selectedDepartemen) return;
        
        // Show loading
        resultsSection.style.display = 'block';
        const pegawaiList = document.getElementById('pegawaiList');
        pegawaiList.innerHTML = document.getElementById('loadingTemplate').innerHTML;
        
        // Fetch pegawai by departemen
        fetch(`/kuisioner/get-pegawai/${selectedDepartemen}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(pegawaiData => {
                displayPegawaiList(pegawaiData);
                updatePegawaiCount(pegawaiData.length);
            })
            .catch(error => {
                console.error('Error:', error);
                pegawaiList.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Terjadi kesalahan saat memuat data pegawai. Silakan coba lagi.
                    </div>
                `;
            });
    });

    function updatePegawaiCount(count) {
        pegawaiCount.textContent = `${count} Pegawai`;
    }

    function displayPegawaiList(pegawaiData) {
        const pegawaiList = document.getElementById('pegawaiList');
        
        if (pegawaiData.length === 0) {
            pegawaiList.innerHTML = document.getElementById('emptyStateTemplate').innerHTML;
            return;
        }

        let html = '<div class="row">';
        
        pegawaiData.forEach(pegawai => {
            // Check if user exists
            if (!pegawai.user || !pegawai.user.id_user) {
                return; // Skip this pegawai if user data is missing
            }

            const statusBadge = getStatusBadge(pegawai);
            
            html += `
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="card-title mb-0">${pegawai.nama}</h6>
                                ${statusBadge}
                            </div>
                            <p class="card-text text-muted small mb-2">
                                <i class="fas fa-id-badge me-1"></i>
                                ${pegawai.jabatan || 'Dosen/Laboran'}
                            </p>
                            <p class="card-text text-muted small mb-3">
                                <i class="fas fa-envelope me-1"></i>
                                ${pegawai.user.email || 'N/A'}
                            </p>
                            <div class="d-flex gap-2 flex-wrap">
                                <button type="button" class="btn btn-primary btn-sm flex-fill" 
                                        onclick="mulaiKuisioner(${selectedPeriode}, ${pegawai.user.id_user})">
                                    <i class="fas fa-play me-1"></i>Mulai Kuisioner
                                </button>
                                <button type="button" class="btn btn-outline-info btn-sm flex-fill" 
                                        onclick="lihatHasil(${selectedPeriode}, ${pegawai.user.id_user})">
                                    <i class="fas fa-chart-bar me-1"></i>Lihat Hasil
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        pegawaiList.innerHTML = html;
    }

    function getStatusBadge(pegawai) {
        // This is a placeholder for status checking
        // You can implement actual status checking via AJAX if needed
        return '<span class="badge bg-light text-dark">Belum Dinilai</span>';
    }

    // Auto-select current user's department if available
    @if($pegawai && $pegawai->id_departemen)
        departemenSelect.value = "{{ $pegawai->id_departemen }}";
        departemenSelect.dispatchEvent(new Event('change'));
    @endif
});

function mulaiKuisioner(periodeId, dinilaiId) {
    // Show confirmation dialog
    if (confirm('Apakah Anda yakin ingin memulai kuisioner untuk pegawai ini?')) {
        window.location.href = `/kuisioner/${periodeId}/${dinilaiId}`;
    }
}

function lihatHasil(periodeId, dinilaiId) {
    window.location.href = `/kuisioner/${periodeId}/${dinilaiId}/result`;
}
</script>

<style>
.card {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    border: none;
    /* Tidak ada transition transform */
    /* transition: transform 0.2s ease-in-out; */ 
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    font-weight: 600;
}

.table th {
    background-color: #f8f9fa;
    font-weight: 600;
}

.spinner-border {
    width: 3rem;
    height: 3rem;
}

.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.form-label {
    font-weight: 600;
    color: #495057;
}

.form-text {
    font-size: 0.85rem;
}

.badge {
    font-size: 0.75rem;
}

.alert-dismissible .btn-close {
    padding: 0.5rem 0.75rem;
}

/* Responsive improvements */
@media (max-width: 768px) {
    .btn-lg {
        padding: 0.75rem 1.5rem;
    }
    
    .card-body .row .col-md-6 {
        margin-bottom: 1rem;
    }
    
    .d-flex.gap-2.flex-wrap .btn {
        margin-bottom: 0.5rem;
    }
}

/* Animation for loading */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

#resultsSection {
    animation: fadeIn 0.3s ease-out;
}

/* Status badge colors */
.badge.bg-light {
    color: #6c757d !important;
    border: 1px solid #dee2e6;
}
</style>

@endsection