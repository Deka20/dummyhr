@extends('admin.master')

@section('title', 'Home | Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
@endsection

@section('content')
<div class="row">


    <!-- Statistics Cards Row -->
    <div class="col-12 mb-4">
        <div class="row">
            <!-- Card Karyawan Masuk Hari Ini -->
            <div class="col-md-3">
                <div class="card text-center" style="border-left: 4px solid #28a745;">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="card-title text-muted mb-1">Masuk Hari Ini</h6>
                                <h3 class="mb-0 text-success">142</h3>
                                <small class="text-muted">dari 180 karyawan</small>
                            </div>
                            <div class="text-success">
                                <i class="fas fa-user-check fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Karyawan Cuti -->
            <div class="col-md-3">
                <div class="card text-center" style="border-left: 4px solid #ffc107;">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="card-title text-muted mb-1">Cuti Hari Ini</h6>
                                <h3 class="mb-0 text-warning">23</h3>
                                <small class="text-muted">karyawan</small>
                            </div>
                            <div class="text-warning">
                                <i class="fas fa-calendar-times fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Terlambat -->
            <div class="col-md-3">
                <div class="card text-center" style="border-left: 4px solid #dc3545;">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="card-title text-muted mb-1">Terlambat</h6>
                                <h3 class="mb-0 text-danger">8</h3>
                                <small class="text-muted">karyawan</small>
                            </div>
                            <div class="text-danger">
                                <i class="fas fa-clock fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Tidak Masuk -->
            <div class="col-md-3">
                <div class="card text-center" style="border-left: 4px solid #6c757d;">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="card-title text-muted mb-1">Tidak Masuk</h6>
                                <h3 class="mb-0 text-secondary">7</h3>
                                <small class="text-muted">karyawan</small>
                            </div>
                            <div class="text-secondary">
                                <i class="fas fa-user-times fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="col-12">
        <div class="row">
            <!-- Chart Golongan -->
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">Golongan Pegawai</h5>
                    </div>
                    <div class="card-body">
                        <div id="golongan-chart" style="height: 350px;"></div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-2">
                                    <div style="width: 12px; height: 12px; background-color: #3498db; border-radius: 50%; margin-right: 8px;"></div>
                                    <span class="text-muted">Golongan A (38%)</span>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <div style="width: 12px; height: 12px; background-color: #2ecc71; border-radius: 50%; margin-right: 8px;"></div>
                                    <span class="text-muted">Golongan B (27%)</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-2">
                                    <div style="width: 12px; height: 12px; background-color: #f1c40f; border-radius: 50%; margin-right: 8px;"></div>
                                    <span class="text-muted">Golongan C (20%)</span>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <div style="width: 12px; height: 12px; background-color: #e74c3c; border-radius: 50%; margin-right: 8px;"></div>
                                    <span class="text-muted">Golongan D (15%)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Absen -->
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">Absensi Hari Ini</h5>
                    </div>
                    <div class="card-body d-flex flex-column justify-content-center">
                        <div class="text-center mb-4">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" 
                                style="width: 80px; height: 80px; background-color: #f8f9fa; border: 3px solid #0056b3;">
                                <i class="fas fa-clock fa-2x text-primary"></i>
                            </div>
                            <h4 class="mb-2" id="current-time">08:30:45</h4>
                            <p class="text-muted mb-0">Senin, 2 Juni 2025</p>
                        </div>
                        
                        <p class="mb-4 text-center text-muted">Silakan absen sesuai waktu kerja Anda.</p>
                        
                        <form method="POST" action="">
                            @csrf
                            <div class="d-grid gap-3">
                                <button type="submit" name="action" value="masuk" 
                                    class="btn btn-lg text-white py-3" 
                                    style="background-color: #0056b3;">
                                    <i class="fas fa-sign-in-alt me-2"></i>
                                    Absen Masuk
                                </button>

                                <button type="submit" name="action" value="pulang" 
                                    class="btn btn-lg text-primary border py-3" 
                                    style="border-color: #0056b3; background-color: white;">
                                    <i class="fas fa-sign-out-alt me-2"></i>
                                    Absen Pulang
                                </button>
                            </div>
                        </form>
                        
                        <div class="mt-4 pt-3 border-top">
                            <div class="row text-center">
                                <div class="col">
                                    <small class="text-muted d-block">Jam Masuk</small>
                                    <strong class="text-success">08:00</strong>
                                </div>
                                <div class="col">
                                    <small class="text-muted d-block">Jam Pulang</small>
                                    <strong class="text-danger">17:00</strong>
                                </div>
                            </div>
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
    // Donut Chart Golongan
    var golonganOptions = {
        series: [38, 27, 20, 15],
        chart: {
            type: 'donut',
            height: 350
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
                    size: '65%',
                    labels: {
                        show: true,
                        name: {
                            show: false
                        },
                        value: {
                            show: true,
                            fontSize: '24px',
                            fontFamily: 'Rubik, sans-serif',
                            fontWeight: 600,
                            offsetY: 8,
                            formatter: function (val) {
                                return val + '%';
                            }
                        },
                        total: {
                            show: true,
                            label: 'Total Pegawai',
                            fontSize: '14px',
                            fontWeight: 400,
                            color: '#9aa0ac',
                            formatter: function () {
                                return '347';
                            }
                        }
                    }
                }
            }
        },
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    height: 300
                }
            }
        }]
    };

    // Render Chart
    var golonganChart = new ApexCharts(document.querySelector("#golongan-chart"), golonganOptions);
    golonganChart.render();

    // Real-time clock
    function updateTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
        const timeElement = document.getElementById('current-time');
        if (timeElement) {
            timeElement.textContent = timeString;
        }
    }

    // Update time every second
    updateTime();
    setInterval(updateTime, 1000);
});
</script>
@endpush