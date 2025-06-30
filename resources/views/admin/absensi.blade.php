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
              <button class="btn btn-outline-secondary btn-sm" onclick="refreshTable()">
                <i class="fas fa-sync-alt me-1"></i> Refresh
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

          <!-- Filter Section -->
          <div class="p-3 border-bottom bg-light">
            <div class="row g-3">
              <div class="col-md-3">
                <label class="form-label small fw-bold">Filter Departemen</label>
                <select name="departemen" id="filterDepartemen" class="form-select form-select-sm">
                  <option value="">Semua Departemen</option>
                  @forelse($departemen as $dept)
                    <option value="{{ $dept->nama_departemen }}" 
                            {{ request('departemen') == $dept->id_departemen ? 'selected' : '' }}>
                      {{ $dept->nama_departemen }}
                    </option>
                  @empty
                    <option disabled>Tidak ada departemen tersedia</option>
                  @endforelse
                </select>
              </div>
              <div class="col-md-3">
                <label class="form-label small fw-bold">Filter Status</label>
                <select class="form-select form-select-sm" id="filterStatus">
                  <option value="">Semua Status</option>
                  <option value="Hadir">Hadir</option>
                  <option value="Izin">Izin</option>
                  <option value="Sakit">Sakit</option>
                  <option value="Tidak Hadir">Tidak Hadir</option>
                </select>
              </div>
              <div class="col-md-3">
                <label class="form-label small fw-bold">Dari Tanggal</label>
                <input type="date" class="form-control form-control-sm" id="filterTanggalMulai">
              </div>
              <div class="col-md-3">
                <label class="form-label small fw-bold">Sampai Tanggal</label>
                <input type="date" class="form-control form-control-sm" id="filterTanggalAkhir">
              </div>
            </div>
          </div>

          <!-- Table Section -->
          <div class="table-responsive">
            <table id="" class="table table-striped table-bordered nowrap">
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
                    <td class="text-center fw-bold">{{ $index + 1 }}</td>
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
                        <span class="text-muted">-</span>
                      @endif
                    </td>
                    <td class="text-center">
                      <div class="btn-group btn-group-sm" role="group">
                        <button type="button" 
                                class="btn btn-outline-info" 
                                data-bs-toggle="modal" 
                                data-bs-target="#detailModal{{ $item->id }}"
                                title="Detail">
                          <i class="fas fa-eye"></i>
                        </button>
                        <a href="" 
                           class="btn btn-outline-warning" 
                           data-bs-toggle="tooltip" 
                           title="Edit">
                          <i class="fas fa-edit"></i>
                        </a>
                        <form action="" 
                              method="POST" 
                              class="d-inline">
                          @csrf
                          @method('DELETE')
                          <button type="submit" 
                                  class="btn btn-outline-danger" 
                                  onclick="return confirm('Yakin ingin menghapus data absensi ini?')"
                                  data-bs-toggle="tooltip" 
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

          <!-- Table Footer Info -->
          <div class="card-footer bg-light">
            <div class="d-flex justify-content-between align-items-center">
              <div class="text-muted small">
                <i class="fas fa-info-circle me-1"></i>
                Menampilkan {{ $absensi->count() }} data absensi pegawai
              </div>
              <div class="d-flex gap-2">
                <span class="badge bg-success"><i class="fas fa-check me-1"></i>Hadir</span>
                <span class="badge bg-warning"><i class="fas fa-exclamation me-1"></i>Izin</span>
                <span class="badge bg-info"><i class="fas fa-heartbeat me-1"></i>Sakit</span>
                <span class="badge bg-danger"><i class="fas fa-times me-1"></i>Alpa</span>
              </div>
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
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="{{ asset('assets/js/plugins/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/dataTables.bootstrap5.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/dataTables.buttons.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/buttons.bootstrap5.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/jszip.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/buttons.html5.min.js') }}"></script>

  <script>
    $(document).ready(function() {
      // Initialize DataTable
      var table = $('#attendance-table').DataTable({
        "language": {
          "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
        },
        "order": [[ 3, "desc" ]], // Urutkan berdasarkan tanggal terbaru
        "pageLength": 25,
        "responsive": true,
        "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
               '<"row"<"col-sm-12"tr>>' +
               '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        "columnDefs": [
          { "orderable": false, "targets": [0, 7] }, // Disable sorting for No and Action columns
          { "searchable": false, "targets": [0, 7] }
        ],
        "drawCallback": function(settings) {
          // Reinitialize tooltips after table redraw
          $('[data-bs-toggle="tooltip"]').tooltip();
        }
      });

      // Filter functions
      $('#filterDepartemen').on('change', function() {
        var val = $(this).val();
        table.column(2).search(val ? '^' + val + '$' : '', true, false).draw();
      });

      $('#filterStatus').on('change', function() {
        var val = $(this).val();
        table.column(4).search(val ? val : '', true, false).draw();
      });

      // Date range filter
      $.fn.dataTable.ext.search.push(
        function(settings, data, dataIndex) {
          var min = $('#filterTanggalMulai').val();
          var max = $('#filterTanggalAkhir').val();
          var dateText = $(data[3]).text() || data[3]; // Get text from HTML or plain text
          
          if (min === '' && max === '') {
            return true;
          }
          
          // Convert date from d/m/Y to Y-m-d for comparison
          var dateParts = dateText.split('/');
          if (dateParts.length !== 3) return true;
          
          var dateFormatted = dateParts[2] + '-' + dateParts[1].padStart(2, '0') + '-' + dateParts[0].padStart(2, '0');
          
          if (min === '') {
            return dateFormatted <= max;
          } else if (max === '') {
            return dateFormatted >= min;
          } else {
            return dateFormatted >= min && dateFormatted <= max;
          }
        }
      );

      $('#filterTanggalMulai, #filterTanggalAkhir').on('change', function() {
        table.draw();
      });

      // Initialize tooltips
      $('[data-bs-toggle="tooltip"]').tooltip();
    });

    // Refresh table function
    function refreshTable() {
      location.reload();
    }

    // Export function
    function exportTable() {
      var table = $('#attendance-table').DataTable();
      
      // Get all data including filtered data
      var data = table.rows({ search: 'applied' }).data().toArray();
      
      // Create CSV content
      var csvContent = "data:text/csv;charset=utf-8,";
      csvContent += "No,Nama Pegawai,Departemen,Tanggal,Status,Waktu Masuk,Waktu Keluar\n";
      
      data.forEach(function(row, index) {
        var cleanRow = [
          index + 1,
          $(row[1]).find('.fw-bold').text() || 'N/A',
          $(row[2]).text() || 'N/A',
          $(row[3]).find('.fw-bold').text() || 'N/A',
          $(row[4]).text().trim() || 'N/A',
          $(row[5]).find('.fw-bold').text() || '-',
          $(row[6]).find('.fw-bold').text() || '-'
        ];
        
        var csvRow = cleanRow.map(function(cell) {
          return '"' + String(cell).replace(/"/g, '""') + '"';
        });
        csvContent += csvRow.join(",") + "\n";
      });

      var encodedUri = encodeURI(csvContent);
      var link = document.createElement("a");
      link.setAttribute("href", encodedUri);
      link.setAttribute("download", "data_absensi_" + new Date().toISOString().slice(0, 10) + ".csv");
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    }
  </script>
@endpush