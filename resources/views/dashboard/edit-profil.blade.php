@extends('dashboard.master')

@section('title', 'Edit Profil')

@section('breadcrumb')
  <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
  <li class="breadcrumb-item active">Edit Profil</li>
@endsection

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card shadow-sm">
      <div class="card-header text-white">
        <h5 class="mb-0">Edit Profil</h5>
      </div>
      <div class="card-body">
        <form class="was-validated">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="nama" class="form-label">Nama Lengkap</label>
              <input type="text" class="form-control" id="nama" placeholder="Nama lengkap" required>
              <div class="invalid-feedback">Nama tidak boleh kosong.</div>
            </div>

            <div class="col-md-6 mb-3">
              <label for="email" class="form-label">Email Aktif</label>
              <input type="email" class="form-control" id="email" placeholder="Email aktif" required>
              <div class="invalid-feedback">Email harus valid.</div>
            </div>

            <div class="col-md-12 mb-3">
              <label for="bio" class="form-label">Tentang Anda</label>
              <textarea class="form-control" id="bio" placeholder="Tulis sedikit tentang dirimu" required></textarea>
              <div class="invalid-feedback">Silakan isi bio kamu.</div>
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">Jenis Kelamin</label>
              <div class="form-check">
                <input type="radio" name="gender" id="pria" class="form-check-input" required>
                <label class="form-check-label" for="pria">Pria</label>
              </div>
              <div class="form-check mb-2">
                <input type="radio" name="gender" id="wanita" class="form-check-input" required>
                <label class="form-check-label" for="wanita">Wanita</label>
                <div class="invalid-feedback">Pilih jenis kelamin.</div>
              </div>
            </div>

            <div class="col-md-6 mb-3">
              <label for="departemen" class="form-label">Departemen</label>
              <select class="form-select" id="departemen" required>
                <option value="">Pilih departemen</option>
                <option value="IT">IT</option>
                <option value="HR">HR</option>
                <option value="Finance">Finance</option>
              </select>
              <div class="invalid-feedback">Pilih departemen kamu.</div>
            </div>

            <div class="col-md-6 mb-3">
              <label for="foto" class="form-label">Foto Profil</label>
              <input type="file" class="form-control" id="foto" required>
              <div class="invalid-feedback">Silakan upload foto profil.</div>
            </div>

            <div class="col-md-6 mb-3">
              <label for="password" class="form-label">Password Baru</label>
              <input type="password" class="form-control" id="password" placeholder="Biarkan kosong jika tidak ingin mengganti">
            </div>
          </div>

          <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="#" class="btn btn-secondary ms-2">Batal</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
