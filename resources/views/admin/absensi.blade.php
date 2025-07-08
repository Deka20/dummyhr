@extends('admin.master')

@section('title', 'Manajemen Absensi')
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.absensi') }}">Absensi</a></li>
@endsection

@section('content')
  <div class="row">
    <div class="col-12">
      <div class="card shadow-sm">
        <div class="card-header">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h5 class="mb-1"><i class="fas fa-calendar-check me-2"></i>Data Absensi Pegawai</h5>
              <small class="text-muted">Monitoring dan laporan absensi pegawai</small>
            </div>
            <div class="btn-group">
              <button class="btn btn-primary btn-sm" onclick="exportTable()">
                <i class="fas fa-download me-1"></i> Export
              </button>
            </div>
          </div>
        </div>

        <div class="card-body p-0">
          @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show m-3 mb-0" role="alert">
              <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          @endif

          <!-- Filter Section (Simplified) -->
          <div class="p-3 border-bottom bg-light">
            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label small fw-bold">Filter Status</label>
                <select class="form-select form-select-sm" id="filterStatus">
                  <option value="">Semua Status</option>
                  <option value="Hadir">Hadir</option>
                  <option value="Izin">Izin</option>
                  <option value="Sakit">Sakit</option>
                  <option value="Tidak Hadir">Tidak Hadir</option>
                </select>
              </div>
              <div class="col-md-4">
                <label class="form-label small fw-bold">Dari Tanggal</label>
                <input type="date" class="form-control form-control-sm" id="filterTanggalMulai">
              </div>
              <div class="col-md-4">
                <label class="form-label small fw-bold">Sampai Tanggal</label>
                <input type="date" class="form-control form-control-sm" id="filterTanggalAkhir">
              </div>
            </div>
          </div>

          <!-- Table Section -->
          <div class="table-responsive">
            <table class="table table-striped table-hover">
              <thead class="table-light">
                <tr>
                  <th class="text-center">No</th>
                  <th><i class="fas fa-user me-1"></i>Nama Pegawai</th>
                  <th><i class="fas fa-building me-1"></i>Departemen</th>
                  <th><i class="fas fa-calendar me-1"></i>Tanggal</th>
                  <th class="text-center"><i class="fas fa-clipboard-check me-1"></i>Status</th>
                  <th class="text-center"><i class="fas fa-clock me-1"></i>Masuk</th>
                  <th class="text-center"><i class="fas fa-clock me-1"></i>Keluar</th>
                  <th class="text-center"><i class="fas fa-cogs me-1"></i>Aksi</th>
                </tr>
              </thead>
              <tbody>
                @forelse($absensi as $index => $item)
                  <tr>
                    <td class="text-center fw-bold">{{ $absensi->firstItem() + $index }}</td>
                    <td>
                      <div class="d-flex align-items-center">
                        <div class="avatar avatar-sm bg-primary rounded-circle me-2 d-flex align-items-center justify-content-center">
                          <i class="fas fa-user text-white"></i>
                        </div>
                        <div>
                          <div class="fw-bold">{{ $item->pegawai->nama ?? 'N/A' }}</div>
                          <small class="text-muted">{{ $item->pegawai->no_hp ?? '' }}</small>
                        </div>
                      </div>
                    </td>
                    <td>
                      <span class="badge bg-light text-dark border">
                        {{ $item->pegawai->departemen->nama_departemen ?? 'N/A' }}
                      </span>
                    </td>
                    <td class="text-center">
                      <div class="fw-bold">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</div>
                      <small class="text-muted">{{ \Carbon\Carbon::parse($item->tanggal)->format('l') }}</small>
                    </td>
                    <td class="text-center">
                      @php
                        $statusConfig = [
                          'Hadir' => ['class' => 'bg-success', 'icon' => 'fas fa-check'],
                          'Izin' => ['class' => 'bg-warning', 'icon' => 'fas fa-exclamation'],
                          'Sakit' => ['class' => 'bg-info', 'icon' => 'fas fa-heartbeat'],
                          'Tidak Hadir' => ['class' => 'bg-danger', 'icon' => 'fas fa-times']
                        ];
                        $config = $statusConfig[$item->status_kehadiran] ?? ['class' => 'bg-secondary', 'icon' => 'fas fa-question'];
                      @endphp
                      <span class="badge fs-6 px-3 py-2 {{ $config['class'] }}">
                        <i class="{{ $config['icon'] }} me-1"></i>
                        {{ $item->status_kehadiran }}
                      </span>
                    </td>
                    <td class="text-center">
                      @if($item->waktu_masuk)
                        <div class="fw-bold text-success">
                          {{ \Carbon\Carbon::parse($item->waktu_masuk)->format('H:i') }}
                        </div>
                        <small class="text-muted">WIB</small>
                      @else
                        <span class="text-muted">-</span>
                      @endif
                    </td>
<td class="text-center">
  @if($item->waktu_pulang)
    <div class="fw-bold text-danger">
      {{ \Carbon\Carbon::parse($item->waktu_pulang)->format('H:i') }}
    </div>
    <small class="text-muted">WIB</small>
  @else
    @if(
      $item->status_kehadiran === 'Hadir' &&
      \Carbon\Carbon::parse($item->tanggal)->lt(\Carbon\Carbon::today())
    )
      <span class="badge bg-secondary">
        <i class="fas fa-sign-out-alt me-1"></i> Lupa Absen Pulang
      </span>
    @else
      <span class="text-muted">-</span>
    @endif
  @endif
</td>

                    <td class="text-center">
                      <div class="d-flex justify-content-center gap-2">
                        <button type="button" 
                                class="btn btn-outline-info btn-sm"
                                data-bs-toggle="modal" 
                                data-bs-target="#detailModal{{ $item->id }}"
                                title="Detail">
                          <i class="fas fa-eye"></i>
                        </button>
                        <form action="{{ route('admin.absensi.destroy', $item->id_kehadiran) }}" 
                              method="POST" 
                              class="d-inline">
                          @csrf
                          @method('DELETE')
                          <button type="submit" 
                                  class="btn btn-outline-danger btn-sm"
                                  onclick="return confirm('Yakin ingin menghapus data absensi ini?')"
                                  title="Hapus">
                            <i class="fas fa-trash"></i>
                          </button>
                        </form>
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="8" class="text-center py-5">
                      <div class="text-muted">
                        <i class="fas fa-inbox fa-3x mb-3"></i>
                        <h5>Tidak ada data absensi</h5>
                        <p>Belum ada data absensi yang tersedia.</p>
                      </div>
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          <!-- Laravel Pagination -->
          <div class="d-flex justify-content-between align-items-center p-3">
            <div class="text-muted small">
              <i class="fas fa-info-circle me-1"></i>
              Menampilkan {{ $absensi->firstItem() ?? 0 }} sampai {{ $absensi->lastItem() ?? 0 }} dari {{ $absensi->total() }} data
            </div>
            <div>
              {{ $absensi->links('pagination::bootstrap-4') }}
            </div>
          </div>

          <!-- Status Legend -->
          <div class="card-footer bg-light">
            <div class="d-flex justify-content-center gap-2">
              <span class="badge bg-success"><i class="fas fa-check me-1"></i>Hadir</span>
              <span class="badge bg-warning"><i class="fas fa-exclamation me-1"></i>Izin</span>
              <span class="badge bg-info"><i class="fas fa-heartbeat me-1"></i>Sakit</span>
              <span class="badge bg-danger"><i class="fas fa-times me-1"></i>Tidak Hadir</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Detail Modals -->
  @foreach($absensi as $item)
    <div class="modal fade" id="detailModal{{ $item->id }}" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              <i class="fas fa-info-circle me-2"></i>Detail Absensi
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-bold text-muted">Nama Pegawai</label>
                <p class="mb-0">{{ $item->pegawai->nama ?? 'N/A' }}</p>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold text-muted">Departemen</label>
                <p class="mb-0">
                  <span class="badge bg-light text-dark border">
                    {{ $item->pegawai->departemen->nama_departemen ?? 'N/A' }}
                  </span>
                </p>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold text-muted">Tanggal</label>
                <p class="mb-0">{{ \Carbon\Carbon::parse($item->tanggal)->format('d F Y') }}</p>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold text-muted">Status Kehadiran</label>
                <p class="mb-0">
                  @php
                    $statusConfig = [
                      'Hadir' => 'bg-success',
                      'Izin' => 'bg-warning',
                      'Sakit' => 'bg-info',
                      'Tidak Hadir' => 'bg-danger'
                    ];
                    $badgeClass = $statusConfig[$item->status_kehadiran] ?? 'bg-secondary';
                  @endphp
                  <span class="badge {{ $badgeClass }}">
                    {{ $item->status_kehadiran }}
                  </span>
                </p>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold text-muted">Waktu Masuk</label>
                <p class="mb-0">
                  {{ $item->waktu_masuk ? \Carbon\Carbon::parse($item->waktu_masuk)->format('H:i') . ' WIB' : '-' }}
                </p>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold text-muted">Waktu Keluar</label>
                <p class="mb-0">
                  {{ $item->waktu_pulang ? \Carbon\Carbon::parse($item->waktu_pulang)->format('H:i') . ' WIB' : '-' }}
                </p>
              </div>
              @if($item->keterangan)
                <div class="col-12">
                  <label class="form-label fw-bold text-muted">Keterangan</label>
                  <div class="p-3 bg-light rounded">
                    {{ $item->keterangan }}
                  </div>
                </div>
              @endif
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
              <i class="fas fa-times me-1"></i>Tutup
            </button>
          </div>
        </div>
      </div>
    </div>
  @endforeach
@endsection

@push('scripts')
  <script>
    // Simple filter functions (tanpa DataTables)
    function filterTable() {
      const status = document.getElementById('filterStatus').value;
      const tanggalMulai = document.getElementById('filterTanggalMulai').value;
      const tanggalAkhir = document.getElementById('filterTanggalAkhir').value;
      
      // Simple filtering logic - ini bisa dikembangkan lebih lanjut
      // Untuk sekarang, filter akan me-reload halaman dengan parameter
      const params = new URLSearchParams(window.location.search);
      
      if (status) params.set('status', status);
      else params.delete('status');
      
      if (tanggalMulai) params.set('tanggal_mulai', tanggalMulai);
      else params.delete('tanggal_mulai');
      
      if (tanggalAkhir) params.set('tanggal_akhir', tanggalAkhir);
      else params.delete('tanggal_akhir');
      
      window.location.search = params.toString();
    }

    // Event listeners for filters
    document.getElementById('filterStatus').addEventListener('change', filterTable);
    document.getElementById('filterTanggalMulai').addEventListener('change', filterTable);
    document.getElementById('filterTanggalAkhir').addEventListener('change', filterTable);

    // Export function (simplified)
    function exportTable() {
      // Simple export - bisa dikembangkan lebih lanjut
      const table = document.querySelector('table');
      const rows = table.querySelectorAll('tbody tr');
      
      let csvContent = "data:text/csv;charset=utf-8,";
      csvContent += "No,Nama Pegawai,Departemen,Tanggal,Status,Waktu Masuk,Waktu Keluar\n";
      
      rows.forEach((row, index) => {
        const cells = row.querySelectorAll('td');
        if (cells.length > 0 && !cells[0].textContent.includes('Tidak ada data')) {
          const rowData = [
            cells[0].textContent.trim(),
            cells[1].querySelector('.fw-bold').textContent.trim(),
            cells[2].textContent.trim(),
            cells[3].querySelector('.fw-bold').textContent.trim(),
            cells[4].textContent.trim(),
            cells[5].querySelector('.fw-bold') ? cells[5].querySelector('.fw-bold').textContent.trim() : '-',
            cells[6].querySelector('.fw-bold') ? cells[6].querySelector('.fw-bold').textContent.trim() : '-'
          ];
          
          const csvRow = rowData.map(cell => `"${cell}"`).join(',');
          csvContent += csvRow + "\n";
        }
      });

      const encodedUri = encodeURI(csvContent);
      const link = document.createElement("a");
      link.setAttribute("href", encodedUri);
      link.setAttribute("download", "data_absensi_" + new Date().toISOString().slice(0, 10) + ".csv");
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    }
  </script>
@endpush