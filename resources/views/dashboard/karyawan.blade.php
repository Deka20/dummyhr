<!-- resources/views/data_karyawan.blade.php -->
@extends('dashboard.master')

@section('title', 'Manajemen Karyawan')

@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ url('/karyawan') }}">Karyawan</a></li>
@endsection

@section('content')
  <div class="row">
    <div class="col-sm-12">
      <div class="card">
        <div class="card-header">
          <h5>Manajemen Karyawan</h5>
          <small>
            Sistem ini memudahkan pengelolaan data karyawan secara cepat dan interaktif menggunakan fitur DataTables.
          </small>
        </div>
        <div class="card-body">
          <div class="dt-responsive table-responsive">
            <table id="simpletable" class="table table-striped table-bordered nowrap">
              <thead>
                <tr>
                  <th>Nama</th>
                  <th>Jabatan</th>
                  <th>Kantor</th>
                  <th>Usia</th>
                  <th>Tanggal Mulai</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Quinn Flynn</td>
                  <td>Arsitek Sistem</td>
                  <td>Jakarta</td>
                  <td>32</td>
                  <td>2021/04/25</td>
                  <td>
                    <a href="#" class="btn btn-sm btn-primary">Edit</a>
                    <a href="#" class="btn btn-sm btn-danger">Hapus</a>
                  </td>
                </tr>
                <tr>
                  <td>Garrett Winters</td>
                  <td>Akuntan</td>
                  <td>Surabaya</td>
                  <td>28</td>
                  <td>2020/07/25</td>
                  <td>
                    <a href="#" class="btn btn-sm btn-primary">Edit</a>
                    <a href="#" class="btn btn-sm btn-danger">Hapus</a>
                  </td>
                </tr>
                <tr>
                  <td>Ashton Cox</td>
                  <td>Staf Administrasi</td>
                  <td>Bandung</td>
                  <td>25</td>
                  <td>2022/01/12</td>
                  <td>
                    <a href="#" class="btn btn-sm btn-primary">Edit</a>
                    <a href="#" class="btn btn-sm btn-danger">Hapus</a>
                  </td>
                </tr>
                <tr>
                  <td>Cedric Kelly</td>
                  <td>Web Developer</td>
                  <td>Batam</td>
                  <td>26</td>
                  <td>2021/03/29</td>
                  <td>
                    <a href="#" class="btn btn-sm btn-primary">Edit</a>
                    <a href="#" class="btn btn-sm btn-danger">Hapus</a>
                  </td>
                </tr>
                <tr>
                  <td>Airi Satou</td>
                  <td>HRD</td>
                  <td>Yogyakarta</td>
                  <td>30</td>
                  <td>2019/11/28</td>
                  <td>
                    <a href="#" class="btn btn-sm btn-primary">Edit</a>
                    <a href="#" class="btn btn-sm btn-danger">Hapus</a>
                  </td>
                </tr>
              </tbody>
              <tfoot>
                <tr>
                  <th>Nama</th>
                  <th>Jabatan</th>
                  <th>Kantor</th>
                  <th>Usia</th>
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
