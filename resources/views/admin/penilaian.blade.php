@extends('admin.master')


@section('title', 'Daftar Karyawan')

@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ url('/penilaian') }}">Penilaian</a></li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5>Daftar pegawai</h5>
    </div>
    <div class="card-body">
        <table class="table table-bordered text-center">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Jabatan</th>
                    <th>Departemen</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>Ahmad Fauzi</td>
                    <td>Staff HRD</td>
                    <td>SDM</td>
                    <td>
                        <a href="{{ url('/penilaian-karyawan') }}" class="btn btn-sm btn-primary">Beri Penilaian</a>
                    </td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Sri Wahyuni</td>
                    <td>Admin Keuangan</td>
                    <td>Keuangan</td>
                    <td>
                        <a href="{{ url('/penilaian-karyawan') }}" class="btn btn-sm btn-primary">Beri Penilaian</a>
                    </td>
                </tr>
                <!-- Tambahkan baris pegawai lainnya sesuai kebutuhan -->
            </tbody>
        </table>
    </div>
</div>
@endsection
