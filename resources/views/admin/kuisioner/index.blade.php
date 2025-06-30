@extends('admin.master')

@section('title', 'Kelola Kuisioner')

@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Dashboard</a></li>
  <li class="breadcrumb-item active">Kelola Kuisioner</li>
@endsection

@section('content')
<div class="row">
    <!-- Card untuk Tambah Kuisioner -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0 text-white">
                    <i class="fas fa-plus-circle me-2 text-white"></i>
                    {{ isset($editKuisioner) ? 'Edit Kuisioner' : 'Tambah Kuisioner' }}
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ isset($editKuisioner) ? route('admin.kuisioner.update', $editKuisioner->id) : route('admin.kuisioner.store') }}" method="POST">
                    @csrf
                    @if(isset($editKuisioner))
                        @method('PUT')
                    @endif
                    
                    <div class="mb-3">
                        <label for="kategori" class="form-label">Kategori</label>
                        <select class="form-select @error('kategori') is-invalid @enderror" id="kategori" name="kategori" required>
                            <option value="">Pilih Kategori</option>
                            <option value="kinerja" {{ (old('kategori', $editKuisioner->kategori ?? '') == 'kinerja') ? 'selected' : '' }}>Kinerja</option>
                            <option value="kedisiplinan" {{ (old('kategori', $editKuisioner->kategori ?? '') == 'kedisiplinan') ? 'selected' : '' }}>Kedisiplinan</option>
                            <option value="komunikasi" {{ (old('kategori', $editKuisioner->kategori ?? '') == 'komunikasi') ? 'selected' : '' }}>Komunikasi</option>
                            <option value="kerjasama" {{ (old('kategori', $editKuisioner->kategori ?? '') == 'kerjasama') ? 'selected' : '' }}>Kerjasama</option>
                            <option value="kepemimpinan" {{ (old('kategori', $editKuisioner->kategori ?? '') == 'kepemimpinan') ? 'selected' : '' }}>Kepemimpinan</option>
                            <option value="inovasi" {{ (old('kategori', $editKuisioner->kategori ?? '') == 'inovasi') ? 'selected' : '' }}>Inovasi</option>
                            <option value="pelayanan" {{ (old('kategori', $editKuisioner->kategori ?? '') == 'pelayanan') ? 'selected' : '' }}>Pelayanan</option>
                        </select>
                        @error('kategori')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="pertanyaan" class="form-label">Pertanyaan</label>
                        <textarea class="form-control @error('pertanyaan') is-invalid @enderror" 
                                  id="pertanyaan" 
                                  name="pertanyaan" 
                                  rows="4" 
                                  placeholder="Masukkan pertanyaan penilaian..."
                                  required>{{ old('pertanyaan', $editKuisioner->pertanyaan ?? '') }}</textarea>
                        @error('pertanyaan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="bobot" class="form-label">Bobot</label>
                        <input type="number" 
                               class="form-control @error('bobot') is-invalid @enderror" 
                               id="bobot" 
                               name="bobot" 
                               min="0.1" 
                               max="10" 
                               step="0.1"
                               value="{{ old('bobot', $editKuisioner->bobot ?? '1.0') }}"
                               placeholder="1.0"
                               required>
                        <small class="form-text text-muted">Bobot antara 0.1 - 10.0</small>
                        @error('bobot')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="aktif" 
                                   name="aktif" 
                                   value="1"
                                   {{ old('aktif', $editKuisioner->aktif ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="aktif">
                                Status Aktif
                            </label>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            {{ isset($editKuisioner) ? 'Update' : 'Simpan' }}
                        </button>
                        @if(isset($editKuisioner))
                            <a href="{{ route('admin.kuisioner.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Batal
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Card untuk Daftar Kuisioner -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>Daftar Kuisioner
                </h5>
                <div class="d-flex gap-2">
                    <!-- Filter Kategori -->
                    <form method="GET" action="{{ route('admin.kuisioner.index') }}" class="d-flex gap-2" id="filterForm">
                        <select class="form-select form-select-sm" name="kategori" style="width: auto;" onchange="this.form.submit()">
                            <option value="">Semua Kategori</option>
                            @foreach($kategoris as $kategori)
                                <option value="{{ $kategori }}" {{ request('kategori') == $kategori ? 'selected' : '' }}>
                                    {{ ucwords($kategori) }}
                                </option>
                            @endforeach
                        </select>
                        <!-- Filter Status Aktif -->
                        <select class="form-select form-select-sm" name="aktif" style="width: auto;" onchange="this.form.submit()">
                            <option value="">Semua Status</option>
                            <option value="1" {{ request('aktif') == '1' ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ request('aktif') == '0' ? 'selected' : '' }}>Non-Aktif</option>
                        </select>
                        <!-- Reset Filter Button -->
                        @if(request('kategori') || request('aktif') !== null)
                            <a href="{{ route('admin.kuisioner.index') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </form>
                </div>
            </div>
            <div class="card-body">
                @if($kuisioners->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="15%">Kategori</th>
                                    <th width="40%">Pertanyaan</th>
                                    <th width="10%">Bobot</th>
                                    <th width="10%">Status</th>
                                    <th width="20%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($kuisioners as $index => $kuisioner)
                                <tr>
                                    <td class="text-center">{{ $kuisioners->firstItem() + $index }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ ucwords($kuisioner->kategori) }}</span>
                                    </td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 300px;" title="{{ $kuisioner->pertanyaan }}">
                                            {{ $kuisioner->pertanyaan_preview ?? Str::limit($kuisioner->pertanyaan, 50) }}
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary">{{ $kuisioner->bobot }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $kuisioner->aktif ? 'success' : 'danger' }}">
                                            {{ $kuisioner->aktif ? 'Aktif' : 'Non-Aktif' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <!-- Tombol Edit -->
                                            <a href="{{ route('admin.kuisioner.edit', $kuisioner->id) }}" 
                                               class="btn btn-sm btn-warning" 
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            
                                            <!-- Tombol Toggle Status -->
                                            <form action="{{ route('admin.kuisioner.toggle', $kuisioner->id) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Yakin ingin mengubah status kuisioner ini?')">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" 
                                                        class="btn btn-sm btn-{{ $kuisioner->aktif ? 'success' : 'secondary' }}" 
                                                        title="{{ $kuisioner->aktif ? 'Non-aktifkan' : 'Aktifkan' }}">
                                                    <i class="fas fa-{{ $kuisioner->aktif ? 'toggle-on' : 'toggle-off' }}"></i>
                                                </button>
                                            </form>
                                            
                                            <!-- Tombol Hapus -->
                                            <form action="{{ route('admin.kuisioner.destroy', $kuisioner->id) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Yakin ingin menghapus kuisioner ini? Tindakan ini tidak dapat dibatalkan!')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-sm btn-danger" 
                                                        title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Simple Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            <small class="text-muted">
                                Halaman {{ $kuisioners->currentPage() }} dari {{ $kuisioners->lastPage() }} 
                                ({{ $kuisioners->total() }} total data)
                            </small>
                        </div>
                        <div>
                            <nav>
                                <ul class="pagination pagination-sm mb-0">
                                    <!-- Previous Button -->
                                    @if ($kuisioners->onFirstPage())
                                        <li class="page-item disabled">
                                            <span class="page-link">
                                                <i class="fas fa-chevron-left me-1"></i>Sebelumnya
                                            </span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $kuisioners->appends(request()->query())->previousPageUrl() }}">
                                                <i class="fas fa-chevron-left me-1"></i>Sebelumnya
                                            </a>
                                        </li>
                                    @endif

                                    <!-- Current Page Info -->
                                    <li class="page-item active">
                                        <span class="page-link">
                                            {{ $kuisioners->currentPage() }} / {{ $kuisioners->lastPage() }}
                                        </span>
                                    </li>

                                    <!-- Next Button -->
                                    @if ($kuisioners->hasMorePages())
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $kuisioners->appends(request()->query())->nextPageUrl() }}">
                                                Selanjutnya<i class="fas fa-chevron-right ms-1"></i>
                                            </a>
                                        </li>
                                    @else
                                        <li class="page-item disabled">
                                            <span class="page-link">
                                                Selanjutnya<i class="fas fa-chevron-right ms-1"></i>
                                            </span>
                                        </li>
                                    @endif
                                </ul>
                            </nav>
                        </div>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">
                            @if(request('kategori') || request('aktif') !== null)
                                Tidak ada kuisioner yang sesuai filter
                            @else
                                Belum ada kuisioner
                            @endif
                        </h5>
                        <p class="text-muted">
                            @if(request('kategori') || request('aktif') !== null)
                                Coba ubah filter atau <a href="{{ route('admin.kuisioner.index') }}">reset filter</a>
                            @else
                                Mulai dengan menambahkan kuisioner penilaian pertama Anda.
                            @endif
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mt-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <i class="fas fa-clipboard-list fa-2x mb-2"></i>
                <h4 class="text-white">{{ $totalKuisioner ?? 0 }}</h4>
                <small>Total Kuisioner</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <i class="fas fa-check-circle fa-2x mb-2"></i>
                <h4 class="text-white">{{ $kuisionerAktif ?? 0 }}</h4>
                <small>Kuisioner Aktif</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <i class="fas fa-pause-circle fa-2x mb-2"></i>
                <h4 class="text-white">{{ $kuisionerNonAktif ?? 0 }}</h4>
                <small>Kuisioner Non-Aktif</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <i class="fas fa-tags fa-2x mb-2"></i>
                <h4 class="text-white">{{ count($kategoris ?? []) }}</h4>
                <small>Kategori</small>
            </div>
        </div>
    </div>
</div>

<!-- Success/Error Messages -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Terdapat kesalahan:</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@endsection

@push('scripts')
<script>
    // Auto-hide success alerts
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.alert-success');
        alerts.forEach(alert => {
            setTimeout(() => {
                if (alert && !alert.classList.contains('fade')) {
                    alert.style.transition = 'opacity 0.5s';
                    alert.style.opacity = '0';
                    setTimeout(() => {
                        if (alert.parentNode) {
                            alert.remove();
                        }
                    }, 500);
                }
            }, 5000);
        });
    });

    // Form validation enhancement
    document.getElementById('bobot').addEventListener('input', function() {
        const value = parseFloat(this.value);
        if (value < 0.1 || value > 10) {
            this.setCustomValidity('Bobot harus antara 0.1 dan 10.0');
        } else {
            this.setCustomValidity('');
        }
    });

    // Auto-resize textarea
    document.getElementById('pertanyaan').addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });

    // Confirm before deleting
    document.querySelectorAll('form[action*="destroy"]').forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!confirm('Yakin ingin menghapus kuisioner ini? Tindakan ini tidak dapat dibatalkan!')) {
                e.preventDefault();
            }
        });
    });

    // Smooth scroll to form when editing
    @if(isset($editKuisioner))
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelector('.col-md-4 .card').scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        });
    @endif
</script>
@endpush

<style>
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border: 1px solid rgba(0, 0, 0, 0.125);
        transition: box-shadow 0.15s ease-in-out;
    }
    
    .card:hover {
        box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.025);
    }
    
    .btn-group .btn {
        border-radius: 0;
    }
    
    .btn-group .btn:first-child {
        border-top-left-radius: 0.25rem;
        border-bottom-left-radius: 0.25rem;
    }
    
    .btn-group .btn:last-child {
        border-top-right-radius: 0.25rem;
        border-bottom-right-radius: 0.25rem;
    }

    .text-truncate {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Simple Pagination Styles */
    .pagination {
        margin-bottom: 0;
    }

    .pagination .page-link {
        color: #495057;
        border-color: #dee2e6;
        font-size: 0.875rem;
        padding: 0.375rem 0.75rem;
        transition: all 0.15s ease-in-out;
    }

    .pagination .page-item.active .page-link {
        background-color: #007bff;
        border-color: #007bff;
        color: white;
        font-weight: 500;
    }

    .pagination .page-link:hover:not(.disabled) {
        color: #0056b3;
        background-color: #e9ecef;
        border-color: #dee2e6;
        transform: translateY(-1px);
    }

    .pagination .page-item.disabled .page-link {
        color: #6c757d;
        background-color: #fff;
        border-color: #dee2e6;
        opacity: 0.6;
    }

    /* Enhanced form styles */
    .form-control:focus,
    .form-select:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .invalid-feedback {
        font-size: 0.875em;
    }

    /* Statistics cards hover effect */
    .row .card {
        transition: transform 0.2s ease-in-out;
    }

    .row .card:hover {
        transform: translateY(-2px);
    }

    /* Badge improvements */
    .badge {
        font-size: 0.75em;
        padding: 0.35em 0.65em;
    }

    /* Alert improvements */
    .alert {
        border: none;
        border-radius: 0.5rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    /* Mobile responsive adjustments */
    @media (max-width: 768px) {
        .btn-group {
            display: flex;
            flex-direction: column;
        }
        
        .btn-group .btn {
            border-radius: 0.25rem !important;
            margin-bottom: 2px;
        }
        
        .pagination .page-link {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
        }
        
        .d-flex.justify-content-between {
            flex-direction: column;
            gap: 1rem;
        }
        
        .d-flex.justify-content-between > div {
            text-align: center;
        }
    }

    /* Loading state for buttons */
    .btn:disabled {
        opacity: 0.65;
        cursor: not-allowed;
    }

    /* Smooth transitions */
    * {
        transition: color 0.15s ease-in-out, 
                   background-color 0.15s ease-in-out, 
                   border-color 0.15s ease-in-out, 
                   box-shadow 0.15s ease-in-out;
    }
</style>