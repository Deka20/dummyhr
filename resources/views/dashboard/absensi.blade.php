@extends('dashboard.master')

@section('title', 'Manajemen Absensi')  <!-- Menentukan title halaman -->
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ url('/absensi') }}">Absensi</a></li>
@endsection


@section('content')
  <!-- Konten halaman absensi di sini -->
  <div class="row">
    <div class="col-sm-12">
      <div class="card">
        <div class="card-header">
          <h5>Data Absensi Karyawan</h5>
          <small>Sistem ini memudahkan pengelolaan data absensi karyawan.</small>
        </div>

        <div class="card-body">
          <div class="dt-responsive table-responsive">
            <table id="attendance-table" class="table table-striped table-bordered nowrap">
            <thead>
            <tr>
                <th>Nama</th>
                <th>Tanggal</th>
                <th>Status Kehadiran</th>
                <th>Waktu Masuk</th>
                <th>Waktu Keluar</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>Quinn Flynn</td>
                <td>2025/04/10</td>
                <td>Hadir</td>
                <td>08:00</td>
                <td>17:00</td>
            </tr>
            <tr>
                <td>Garrett Winters</td>
                <td>2025/04/10</td>
                <td>Hadir</td>
                <td>08:15</td>
                <td>17:10</td>
            </tr>
            <tr>
                <td>Ashton Cox</td>
                <td>2025/04/10</td>
                <td>Izin</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>Cedric Kelly</td>
                <td>2025/04/10</td>
                <td>Hadir</td>
                <td>08:05</td>
                <td>17:00</td>
            </tr>
            <tr>
                <td>Airi Satou</td>
                <td>2025/04/10</td>
                <td>Hadir</td>
                <td>08:00</td>
                <td>17:05</td>
            </tr>
            </tbody>
            <tfoot>
            <tr>
                <th>Nama</th>
                <th>Tanggal</th>
                <th>Status Kehadiran</th>
                <th>Waktu Masuk</th>
                <th>Waktu Keluar</th>
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
    $('#attendance-table').DataTable();
  </script>
@endpush
  


