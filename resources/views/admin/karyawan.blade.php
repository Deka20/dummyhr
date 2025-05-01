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
        <div class="card-header">
          <h5>Manajemen pegawai</h5>
          <small>
            Sistem ini memudahkan pengelolaan data pegawai secara cepat dan interaktif menggunakan fitur DataTables.
          </small>
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
                <tr>
                  <td>{{$pegawai->nama}}</td>
                  <td>{{$pegawai->nama}}</td>
                  <td>{{$pegawai->tempat_lahir}}</td>
                  <td>{{$pegawai->jenis_kelamin}}</td>
                  <td>{{$pegawai->tanggal_masuk}}</td>
                  <td>
                    <a href="#" class="btn btn-sm text-primary border" style="border-color: #0056b3; background-color: white;" >Edit</a>
                    <a href="#"  class="btn btn-sm text-white" style="background-color: #0056b3;">Hapus</a>
                  </td>
                </tr>
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
@endsection

@push('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="{{ asset('assets/js/plugins/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/dataTables.bootstrap5.min.js') }}"></script>

  <script>
    $('#simpletable').DataTable();
  </script>
@endpush
