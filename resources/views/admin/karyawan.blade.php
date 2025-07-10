<!-- resources/views/admin/karyawan.blade.php -->
@extends('admin.master')

@section('title', 'Manajemen Karyawan')

@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ url('/karyawan') }}">pegawai</a></li>
@endsection

@section('content')
   <div class="row">
    <div class="col-sm-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <div>
            <h5>Manajemen pegawai</h5>
            <small>
              Sistem ini memudahkan pengelolaan data pegawai secara cepat dan interaktif dengan fitur filter dan pagination.
            </small>
          </div>
          <div>
            <button type="button" class="btn text-white" style="background-color: #0056b3;" data-bs-toggle="modal" data-bs-target="#tambahPegawaiModal">
              <i class="fas fa-plus"></i> Tambah Pegawai
            </button>
          </div>
        </div>
        
        <!-- Filter Section -->
        <div class="card-body border-bottom">
          <form method="GET" action="{{ route('admin.karyawan') }}" class="mb-3">
            <div class="row">
              <div class="col-md-3 mb-2">
                <label class="form-label">Nama</label>
                <input type="text" name="nama" class="form-control" value="{{ request('nama') }}" placeholder="Cari nama pegawai...">
              </div>
              <div class="col-md-2 mb-2">
                <label class="form-label">Departemen</label>
                <select name="departemen" class="form-select">
                  <option value="">Semua Departemen</option>
                  @foreach($departemen as $dept)
                    <option value="{{ $dept->id_departemen }}" {{ request('departemen') == $dept->id_departemen ? 'selected' : '' }}>
                      {{ $dept->nama_departemen }}
                    </option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-2 mb-2">
                <label class="form-label">Jabatan</label>
                <select name="jabatan" class="form-select">
                  <option value="">Semua Jabatan</option>
                  @foreach($jabatan as $jbt)
                    <option value="{{ $jbt->id_jabatan }}" {{ request('jabatan') == $jbt->id_jabatan ? 'selected' : '' }}>
                      {{ $jbt->nama_jabatan }}
                    </option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-2 mb-2">
                <label class="form-label">Jenis Kelamin</label>
                <select name="jenis_kelamin" class="form-select">
                  <option value="">Semua</option>
                  <option value="L" {{ request('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                  <option value="P" {{ request('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                </select>
              </div>
              <div class="col-md-3 mb-2">
                <label class="form-label">Tanggal Masuk</label>
                <div class="row">
                  <div class="col-6">
                    <input type="date" name="tanggal_masuk_dari" class="form-control" value="{{ request('tanggal_masuk_dari') }}" placeholder="Dari">
                  </div>
                  <div class="col-6">
                    <input type="date" name="tanggal_masuk_sampai" class="form-control" value="{{ request('tanggal_masuk_sampai') }}" placeholder="Sampai">
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <button type="submit" class="btn btn-primary">
                  <i class="fas fa-search"></i> Filter
                </button>
                <a href="{{ route('admin.karyawan') }}" class="btn btn-secondary">
                  <i class="fas fa-times"></i> Reset
                </a>
              </div>
            </div>
          </form>
        </div>

        <div class="card-body">
          <!-- Info dan Kontrol -->
          <div class="row mb-3">
            <div class="col-md-6">
              <div class="d-flex align-items-center">
                <label class="form-label me-2">Tampilkan:</label>
                <select name="per_page" class="form-select" style="width: auto;" onchange="changePerPage(this.value)">
                  <option value="10" {{ request('per_page') == '10' ? 'selected' : '' }}>10</option>
                  <option value="25" {{ request('per_page') == '25' ? 'selected' : '' }}>25</option>
                  <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50</option>
                  <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100</option>
                </select>
                <span class="ms-2">data per halaman</span>
              </div>
            </div>
            <div class="col-md-6">
              <div class="d-flex align-items-center justify-content-end">
                <span class="me-3">
                  Menampilkan {{ $karyawan->firstItem() ?? 0 }} - {{ $karyawan->lastItem() ?? 0 }} 
                  dari {{ $karyawan->total() }} data
                </span>
              </div>
            </div>
          </div>

          <div class="table-responsive">
            <table class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th>
                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'nama', 'sort_order' => request('sort_by') == 'nama' && request('sort_order') == 'asc' ? 'desc' : 'asc']) }}" 
                       class="text-decoration-none text-dark">
                      Nama
                      @if(request('sort_by') == 'nama')
                        <i class="fas fa-sort-{{ request('sort_order') == 'asc' ? 'up' : 'down' }}"></i>
                      @else
                        <i class="fas fa-sort"></i>
                      @endif
                    </a>
                  </th>
                  <th>Departemen</th>
                  <th>Jabatan</th>
                  <th>Tempat Lahir</th>
                  <th>
                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'jenis_kelamin', 'sort_order' => request('sort_by') == 'jenis_kelamin' && request('sort_order') == 'asc' ? 'desc' : 'asc']) }}" 
                       class="text-decoration-none text-dark">
                      Jenis Kelamin
                      @if(request('sort_by') == 'jenis_kelamin')
                        <i class="fas fa-sort-{{ request('sort_order') == 'asc' ? 'up' : 'down' }}"></i>
                      @else
                        <i class="fas fa-sort"></i>
                      @endif
                    </a>
                  </th>
                  <th>
                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'tanggal_masuk', 'sort_order' => request('sort_by') == 'tanggal_masuk' && request('sort_order') == 'asc' ? 'desc' : 'asc']) }}" 
                       class="text-decoration-none text-dark">
                      Tanggal Masuk
                      @if(request('sort_by') == 'tanggal_masuk')
                        <i class="fas fa-sort-{{ request('sort_order') == 'asc' ? 'up' : 'down' }}"></i>
                      @else
                        <i class="fas fa-sort"></i>
                      @endif
                    </a>
                  </th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                @forelse($karyawan as $p)
                <tr>
                  <td>
                    <div class="d-flex align-items-center">
                      <img src="{{ $p->foto && $p->foto !== 'avatar-1.jpg' ? asset('uploads/pegawai/' . $p->foto) : asset('assets/images/user/avatar-1.jpg') }}" 
                           alt="Foto" class="rounded-circle me-2" width="40" height="40">
                      {{ $p->nama }}
                    </div>
                  </td>
                  <td>{{ $p->departemen->nama_departemen }}</td>
                  <td>{{ $p->jabatan->nama_jabatan ?? 'Tidak ada jabatan' }}</td>
                  <td>{{ $p->tempat_lahir }}</td>
                  <td>{{ $p->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                  <td>{{ \Carbon\Carbon::parse($p->tanggal_masuk)->format('d/m/Y') }}</td>
                  <td class="d-flex gap-1">
                    <!-- Edit Button -->
                    <a href="{{ route('pegawai.edit', $p->id_pegawai) }}" 
                      class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1" 
                      title="Edit">
                      <i class="fas fa-edit"></i> Edit
                    </a>

                    <!-- Delete Button -->
                    <form action="{{ route('pegawai.destroy', $p->id_pegawai) }}" 
                          method="POST" 
                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus pegawai {{ $p->nama }}?')">
                      @csrf
                      @method('DELETE')
                      <button style="background-color:red" type="submit" 
                              class="btn btn-sm d-flex text-white align-items-center gap-1" 
                              title="Hapus">
                        <i class="fas fa-trash-alt"></i> Hapus
                      </button>
                    </form>

                    <!-- View Button -->
                    <a href="{{ route('pegawai.show', $p->id_pegawai) }}" 
                      class="btn btn-sm btn-outline-info d-flex align-items-center gap-1" 
                      title="Lihat Detail">
                      <i class="fas fa-eye"></i> Detail
                    </a>
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="7" class="text-center">
                    <div class="py-4">
                      <i class="fas fa-users fa-3x text-muted mb-3"></i>
                      <p class="text-muted">Tidak ada data pegawai yang ditemukan</p>
                      @if(request()->anyFilled(['nama', 'departemen', 'jabatan', 'jenis_kelamin', 'tanggal_masuk_dari', 'tanggal_masuk_sampai']))
                        <a href="{{ route('admin.karyawan') }}" class="btn btn-primary">Reset Filter</a>
                      @endif
                    </div>
                  </td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div class="d-flex justify-content-between align-items-center mt-3">
            <div>
              <small class="text-muted">
                Menampilkan {{ $karyawan->firstItem() ?? 0 }} - {{ $karyawan->lastItem() ?? 0 }} 
                dari {{ $karyawan->total() }} data
              </small>
            </div>
            <div>
              {{ $karyawan->links() }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Tambah Pegawai -->
  <div class="modal fade" id="tambahPegawaiModal" tabindex="-1" aria-labelledby="tambahPegawaiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="tambahPegawaiModalLabel">Tambah Pegawai Baru</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="{{ route('karyawan.store') }}" method="POST" enctype="multipart/form-data" id="formTambahPegawai">
          @csrf
          <div class="modal-body">
            <div class="row">
              <!-- Foto Profil -->
              <div class="col-md-12 mb-3">
                <label class="form-label">Foto Profil</label>
                <div class="d-flex align-items-center gap-3">
                  <div class="position-relative">
                    <img id="previewFoto" src="{{ asset('assets/images/user/avatar-1.jpg') }}" 
                         alt="Preview Foto" class="rounded-circle" width="80" height="80" style="object-fit: cover;">
                  </div>
                  <div>
                    <input type="file" name="foto" class="form-control" id="inputFoto" accept="image/*">
                    <small class="text-muted">Kosongkan jika ingin menggunakan foto default. Format: JPG, PNG, JPEG. Max: 2MB</small>
                  </div>
                </div>
              </div>

              <!-- Data Pribadi -->
              <div class="col-md-6 mb-3">
                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" name="nama" class="form-control" required value="{{ old('nama') }}">
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label">Tempat Lahir <span class="text-danger">*</span></label>
                <input type="text" name="tempat_lahir" class="form-control" required value="{{ old('tempat_lahir') }}">
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                <input type="date" name="tanggal_lahir" class="form-control" required value="{{ old('tanggal_lahir') }}">
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                <select name="jenis_kelamin" class="form-select" required>
                  <option value="">Pilih Jenis Kelamin</option>
                  <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                  <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                </select>
              </div>

              <div class="col-md-12 mb-3">
                <label class="form-label">Alamat <span class="text-danger">*</span></label>
                <textarea name="alamat" class="form-control" rows="3" required>{{ old('alamat') }}</textarea>
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label">No. HP <span class="text-danger">*</span></label>
                <input type="text" name="no_hp" class="form-control" required value="{{ old('no_hp') }}">
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control" required value="{{ old('email') }}">
              </div>

              <!-- Data Pekerjaan -->
              <div class="col-md-6 mb-3">
                <label class="form-label">Departemen <span class="text-danger">*</span></label>
                <select name="id_departemen" class="form-select" required>
                  <option value="">Pilih Departemen</option>
                  @foreach($departemen as $dept)
                    <option value="{{ $dept->id_departemen }}" {{ old('id_departemen') == $dept->id_departemen ? 'selected' : '' }}>
                      {{ $dept->nama_departemen }}
                    </option>
                  @endforeach
                </select>
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label">Jabatan <span class="text-danger">*</span></label>
                <select name="id_jabatan" class="form-select" required>
                  <option value="">Pilih Jabatan</option>
                  @foreach($jabatan as $jbt)
                    <option value="{{ $jbt->id_jabatan }}" {{ old('id_jabatan') == $jbt->id_jabatan ? 'selected' : '' }}>
                      {{ $jbt->nama_jabatan }}
                    </option>
                  @endforeach
                </select>
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label">Tanggal Masuk <span class="text-danger">*</span></label>
                <input type="date" name="tanggal_masuk" class="form-control" required value="{{ old('tanggal_masuk') }}">
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label">Jatah Cuti Tahunan <span class="text-danger">*</span></label>
                <input type="number" name="jatahtahunan" class="form-control" required min="0" value="{{ old('jatahtahunan', 12) }}">
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save"></i> Simpan
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
<script>
  // Function untuk mengubah per_page
  function changePerPage(value) {
    const url = new URL(window.location.href);
    url.searchParams.set('per_page', value);
    url.searchParams.set('page', 1); // Reset ke halaman 1
    window.location.href = url.toString();
  }

  // Preview foto saat upload
  document.getElementById('inputFoto').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function(e) {
        document.getElementById('previewFoto').src = e.target.result;
      };
      reader.readAsDataURL(file);
    } else {
      document.getElementById('previewFoto').src = "{{ asset('assets/images/user/avatar-1.jpg') }}";
    }
  });

  // Reset form saat modal ditutup
  document.getElementById('tambahPegawaiModal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('formTambahPegawai').reset();
    document.getElementById('previewFoto').src = "{{ asset('assets/images/user/avatar-1.jpg') }}";
  });

  // Auto-open modal jika ada error validasi
  @if($errors->any())
    document.addEventListener('DOMContentLoaded', function() {
      const modal = new bootstrap.Modal(document.getElementById('tambahPegawaiModal'));
      modal.show();
    });
  @endif
</script>
@endpush

@push('styles')
<style>
  .table th {
    background-color: #f8f9fa;
    font-weight: 600;
  }
  
  .table th a {
    color: #495057;
  }
  
  .table th a:hover {
    color: #007bff;
  }
  
  .btn-group .btn {
    border-radius: 0.375rem;
    margin-right: 0.25rem;
  }
  
  .modal-lg {
    max-width: 800px;
  }
  
  .form-label {
    font-weight: 500;
    margin-bottom: 0.5rem;
  }
  
  .text-danger {
    color: #dc3545 !important;
  }
  
  .card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
  }
  
  .filter-section {
    background-color: #f8f9fa;
    padding: 1rem;
    border-radius: 0.375rem;
    margin-bottom: 1rem;
  }
</style>
@endpush