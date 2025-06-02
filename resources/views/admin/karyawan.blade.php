<!-- resources/views/data_karyawan.blade.php -->
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
              Sistem ini memudahkan pengelolaan data pegawai secara cepat dan interaktif menggunakan fitur DataTables.
            </small>
          </div>
          <div>
            <button type="button" class="btn text-white "style="background-color: #0056b3;" data-bs-toggle="modal" data-bs-target="#tambahPegawaiModal">
              <i class="fas fa-plus"></i> Tambah Pegawai
            </button>
          </div>
        </div>
        <div class="card-body">
          <div class="dt-responsive table-responsive">
            <table id="simpletable" class="table table-striped table-bordered nowrap">
              <thead>
                <tr>
                  <th>Nama</th>
                  <th>Jabatan</th>
                  <th>Tempat Lahir</th>
                  <th>Jenis Kelamin</th>
                  <th>Tanggal Mulai</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
              @foreach($karyawan as $p)
              <tr>
                  <td>{{ $p->nama }}</td>
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
              @endforeach
              </tbody>
              <tfoot>
                <tr>
                  <th>Nama</th>
                  <th>Jabatan</th>
                  <th>Tempat Lahir</th>
                  <th>Jenis Kelamin</th>
                  <th>Tanggal Mulai</th>
                  <th>Aksi</th>
                </tr>
              </tfoot>
            </table>
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
        <form action="{{ route('karyawan.store') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="nama" class="form-label">Nama Lengkap</label>
                <input type="text" class="form-control" id="nama" name="nama" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="tempat_lahir" class="form-label">Tempat Lahir</label>
                <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir" required>
              </div>
            </div>
            
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                <select class="form-select" id="jenis_kelamin" name="jenis_kelamin" required>
                  <option value="">Pilih Jenis Kelamin</option>
                  <option value="L">Laki-laki</option>
                  <option value="P">Perempuan</option>
                </select>
              </div>
            </div>
            
            <div class="mb-3">
              <label for="alamat" class="form-label">Alamat</label>
              <textarea class="form-control" id="alamat" name="alamat" rows="3" required></textarea>
            </div>
            
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="no_hp" class="form-label">No. Handphone</label>
                <input type="text" class="form-control" id="no_hp" name="no_hp" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
              </div>
            </div>
            
            <div class="row">
            <div class="col-md-6 mb-3">
              <label for="id_departemen" class="form-label">Departemen</label>
              <select class="form-select" id="id_departemen" name="id_departemen" required>
                <option value="">Pilih Departemen</option>
                @foreach($departemen as $dept)
                  <option value="{{ $dept->id_departemen }}">{{ $dept->nama_departemen }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-md-6 mb-3" id="jabatan-container" style="display: none;">
              <label for="id_jabatan" class="form-label">Jabatan</label>
              <select class="form-select" id="id_jabatan" name="id_jabatan">
                <option value="">Pilih Jabatan</option>
                @foreach($jabatan as $jbt)
                  <option value="{{ $jbt->id_jabatan }}">{{ $jbt->nama_jabatan }}</option>
                @endforeach
              </select>
            </div>
          </div>

            
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="tanggal_masuk" class="form-label">Tanggal Masuk</label>
                <input type="date" class="form-control" id="tanggal_masuk" name="tanggal_masuk" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="jatah_tahunan" class="form-label">Jatah Cuti Tahunan</label>
                <input type="number" class="form-control" id="jatah_tahunan" name="jatahtahunan" value="0">
              </div>
            </div>
            
            <div class="mb-3">
              <label for="foto" class="form-label">Foto</label>
              <input type="file" class="form-control" id="foto" name="foto">
              <small class="text-muted">Format: JPG, PNG, JPEG. Maks: 2MB</small>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="{{ asset('assets/js/plugins/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/dataTables.bootstrap5.min.js') }}"></script>

  <script>
    $(document).ready(function() {
      $('#simpletable').DataTable();
      
      // Initialize any form validations or dynamic elements
      $('.btn-close, .btn-secondary').on('click', function() {
        $('#tambahPegawaiModal form')[0].reset();
      });
    });
  </script>

  <script>
  document.addEventListener('DOMContentLoaded', function () {
    const departemenSelect = document.getElementById('id_departemen');
    const jabatanContainer = document.getElementById('jabatan-container');

    departemenSelect.addEventListener('change', function () {
      if (this.value !== "") {
        jabatanContainer.style.display = 'block';
      } else {
        jabatanContainer.style.display = 'none';
        document.getElementById('id_jabatan').value = ""; // reset jabatan
      }
    });
  });
</script>
@endpush