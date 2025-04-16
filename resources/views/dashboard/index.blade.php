@extends('dashboard.master')

@section('title', 'Home | Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
@endsection

@section('content')
<div class="row">
  <!-- Profil Pengguna -->
  <div class="col-md-8">
    <div class="card mb-4">
      <div class="card-header">
        <h5>Profil Pengguna</h5>
      </div>
      <div class="card-body">
        <div class="row align-items-center">
          <div class="col-md-3 text-center">
            <div class="avatar avatar-xl mb-3">
              <img src="{{ asset('assets/images/user/avatar-1.jpg') }}" alt="User Profile" class="img-fluid rounded-circle">
            </div>
            <h4 class="mb-1">John Doe</h4>
            <p class="text-muted">Administrator</p>
            <div class="mb-2">
              <span class="badge bg-primary">Active</span>
            </div>
            <button class="btn btn-primary btn-sm">Edit Profile</button>
          </div>
          <div class="col-md-9">
            <div class="row">
              <div class="col-md-6">
                <div class="card border mb-3">
                  <div class="card-body">
                    <h6 class="mb-2 f-w-400 text-muted">Email</h6>
                    <p class="mb-0">johndoe@example.com</p>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="card border mb-3">
                  <div class="card-body">
                    <h6 class="mb-2 f-w-400 text-muted">No. Telepon</h6>
                    <p class="mb-0">+62 812 3456 7890</p>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="card border mb-3">
                  <div class="card-body">
                    <h6 class="mb-2 f-w-400 text-muted">Bergabung Sejak</h6>
                    <p class="mb-0">18 April 2023</p>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="card border mb-3">
                  <div class="card-body">
                    <h6 class="mb-2 f-w-400 text-muted">Status</h6>
                    <p class="mb-0"><span class="badge bg-light-success border border-success"><i class="ti ti-check"></i> Online</span></p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Form Absen -->
    <div class="card">
      <div class="card-header">
        <h5>Absensi Hari Ini</h5>
      </div>
      <div class="card-body text-center">
        <p class="mb-3 text-muted">Silakan absen sesuai waktu kerja Anda.</p>
        <form method="POST" action="">
          @csrf
          <div class="d-flex justify-content-center gap-3">
            <button type="submit" name="action" value="masuk" class="btn btn-success">Absen Masuk</button>
            <button type="submit" name="action" value="pulang" class="btn btn-danger">Absen Pulang</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Donut Chart Golongan -->
  <div class="col-md-4">
    <div class="card h-100">
      <div class="card-header">
        <h5>Golongan Karyawan</h5>
      </div>
      <div class="card-body d-flex flex-column justify-content-center">
        <div id="golongan-chart" style="height: 300px;"></div>
        <div class="row mt-4">
          <div class="col-12">
            <div class="d-flex align-items-center mb-3">
              <div style="width: 12px; height: 12px; background-color: #3498db; border-radius: 50%; margin-right: 8px;"></div>
              <span class="text-muted">Golongan A (38%)</span>
            </div>
            <div class="d-flex align-items-center mb-3">
              <div style="width: 12px; height: 12px; background-color: #2ecc71; border-radius: 50%; margin-right: 8px;"></div>
              <span class="text-muted">Golongan B (27%)</span>
            </div>
            <div class="d-flex align-items-center mb-3">
              <div style="width: 12px; height: 12px; background-color: #f1c40f; border-radius: 50%; margin-right: 8px;"></div>
              <span class="text-muted">Golongan C (20%)</span>
            </div>
            <div class="d-flex align-items-center">
              <div style="width: 12px; height: 12px; background-color: #e74c3c; border-radius: 50%; margin-right: 8px;"></div>
              <span class="text-muted">Golongan D (15%)</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    var options = {
      series: [38, 27, 20, 15],
      chart: {
        type: 'donut',
        height: 300
      },
      colors: ['#3498db', '#2ecc71', '#f1c40f', '#e74c3c'],
      labels: ['Golongan A', 'Golongan B', 'Golongan C', 'Golongan D'],
      legend: {
        show: false
      },
      dataLabels: {
        enabled: false
      },
      plotOptions: {
        pie: {
          donut: {
            size: '70%',
            labels: {
              show: true,
              name: {
                show: false
              },
              value: {
                show: true,
                fontSize: '22px',
                fontFamily: 'Rubik, sans-serif',
                offsetY: 8,
                formatter: function (val) {
                  return val + '%';
                }
              },
              total: {
                show: true,
                label: 'Total',
                formatter: function () {
                  return '347';
                }
              }
            }
          }
        }
      }
    };

    var chart = new ApexCharts(document.querySelector("#golongan-chart"), options);
    chart.render();
  });
</script>
@endpush
