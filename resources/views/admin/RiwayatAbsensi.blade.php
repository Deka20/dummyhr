@extends('admin.master')

@section('title', 'Data Absensi')
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.absensi') }}">Absensi</a></li>
@endsection

@section('content')
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">Data Absensi Pegawai</h5>
        </div>

        <div class="card-body">
          @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              {{ session('success') }}
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          @endif

          <!-- Filter Section -->
          <div class="row mb-3">
            <div class="col-md-4">
              <select class="form-select" id="filterStatus">
                <option value="">Semua Status</option>
                <option value="Hadir">Hadir</option>
                <option value="Terlambat">Terlambat</option>
                <option value="Izin">Izin</option>
                <option value="Sakit">Sakit</option>
                <option value="Tidak Hadir">Tidak Hadir</option>
              </select>
            </div>
            <div class="col-md-4">
              <input type="date" class="form-control" id="filterTanggalMulai" placeholder="Dari Tanggal">
            </div>
            <div class="col-md-4">
              <input type="date" class="form-control" id="filterTanggalAkhir" placeholder="Sampai Tanggal">
            </div>
          </div>

          <!-- Table Section -->
          <div class="table-responsive">
            <table id="absensi-table" class="table table-striped">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Nama Pegawai</th>
                  <th>Departemen</th>
                  <th>Tanggal</th>
                  <th>Status</th>
                  <th>Jam Masuk</th>
                  <th>Jam Pulang</th>
                  <th>Total Jam</th>
                  <th>Status Jam</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                @forelse($absensi as $index => $item)
                  <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->pegawai->nama ?? 'N/A' }}</td>
                    <td>{{ $item->pegawai->departemen->nama_departemen ?? 'N/A' }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                    <td>
                      @if($item->status_kehadiran == 'Hadir')
                        <span class="badge bg-success">Hadir</span>
                      @elseif($item->status_kehadiran == 'Terlambat')
                        <span class="badge bg-warning">Terlambat</span>
                      @elseif($item->status_kehadiran == 'Izin')
                        <span class="badge bg-info">Izin</span>
                      @elseif($item->status_kehadiran == 'Sakit')
                        <span class="badge bg-secondary">Sakit</span>
                      @else
                        <span class="badge bg-danger">Tidak Hadir</span>
                      @endif
                    </td>
                    <td>
                      {{ $item->waktu_masuk ? \Carbon\Carbon::parse($item->waktu_masuk)->format('H:i') : '-' }}
                    </td>
                    <td>
                      {{ $item->waktu_pulang ? \Carbon\Carbon::parse($item->waktu_pulang)->format('H:i') : '-' }}
                    </td>
                    <td>{{ $item->durasi_kerja ?? '-' }}</td>
                    <td>
                      @if($item->status_jam_kerja == 'Memenuhi')
                        <span class="badge bg-success">Memenuhi</span>
                      @elseif($item->status_jam_kerja == 'Setengah Hari')
                        <span class="badge bg-warning">Setengah Hari</span>
                      @elseif($item->status_jam_kerja == 'Kurang')
                        <span class="badge bg-danger">Kurang</span>
                      @else
                        <span class="badge bg-secondary">-</span>
                      @endif
                    </td>
                    <td>
                      <button type="button" 
                              class="btn btn-sm btn-info" 
                              data-bs-toggle="modal" 
                              data-bs-target="#detailModal{{ $item->id_kehadiran }}">
                        Detail
                      </button>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="10" class="text-center">Tidak ada data absensi</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Detail Modals -->
  @foreach($absensi as $item)
    <div class="modal fade" id="detailModal{{ $item->id_kehadiran }}" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Detail Absensi</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="row g-3">
              <div class="col-12">
                <strong>Nama Pegawai:</strong><br>
                {{ $item->pegawai->nama ?? 'N/A' }}
              </div>
              <div class="col-12">
                <strong>Departemen:</strong><br>
                {{ $item->pegawai->departemen->nama_departemen ?? 'N/A' }}
              </div>
              <div class="col-6">
                <strong>Tanggal:</strong><br>
                {{ \Carbon\Carbon::parse($item->tanggal)->format('d F Y') }}
              </div>
              <div class="col-6">
                <strong>Status Kehadiran:</strong><br>
                @if($item->status_kehadiran == 'Hadir')
                  <span class="badge bg-success">Hadir</span>
                @elseif($item->status_kehadiran == 'Terlambat')
                  <span class="badge bg-warning">Terlambat</span>
                @elseif($item->status_kehadiran == 'Izin')
                  <span class="badge bg-info">Izin</span>
                @elseif($item->status_kehadiran == 'Sakit')
                  <span class="badge bg-secondary">Sakit</span>
                @else
                  <span class="badge bg-danger">Tidak Hadir</span>
                @endif
              </div>
              <div class="col-6">
                <strong>Jam Masuk:</strong><br>
                {{ $item->waktu_masuk ? \Carbon\Carbon::parse($item->waktu_masuk)->format('H:i') . ' WIB' : '-' }}
              </div>
              <div class="col-6">
                <strong>Jam Pulang:</strong><br>
                {{ $item->waktu_pulang ? \Carbon\Carbon::parse($item->waktu_pulang)->format('H:i') . ' WIB' : '-' }}
              </div>
              <div class="col-6">
                <strong>Total Jam Kerja:</strong><br>
                {{ $item->durasi_kerja ?? '-' }}
              </div>
              <div class="col-6">
                <strong>Status Jam Kerja:</strong><br>
                @if($item->status_jam_kerja == 'Memenuhi')
                  <span class="badge bg-success">Memenuhi</span>
                @elseif($item->status_jam_kerja == 'Setengah Hari')
                  <span class="badge bg-warning">Setengah Hari</span>
                @elseif($item->status_jam_kerja == 'Kurang')
                  <span class="badge bg-danger">Kurang</span>
                @else
                  <span class="badge bg-secondary">-</span>
                @endif
              </div>
              @if($item->lokasi_kantor)
                <div class="col-12">
                  <strong>Lokasi Kantor:</strong><br>
                  {{ $item->lokasiKantor->nama_lokasi ?? 'N/A' }}
                </div>
              @endif
              @if($item->keterangan)
                <div class="col-12">
                  <strong>Keterangan:</strong><br>
                  <div class="p-2 bg-light rounded">
                    {{ $item->keterangan }}
                  </div>
                </div>
              @endif
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
          </div>
        </div>
      </div>
    </div>
  @endforeach
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#absensi-table').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
        },
        "order": [[ 3, "desc" ]], // Sort by date
        "pageLength": 25,
        "columnDefs": [
            { "orderable": false, "targets": [0, 9] } // Disable sorting for No and Action
        ]
    });

    // Filter by Status
    $('#filterStatus').on('change', function() {
        var val = $(this).val();
        table.column(4).search(val ? val : '', true, false).draw();
    });

    // Date range filter
    $.fn.dataTable.ext.search.push(
        function(settings, data, dataIndex) {
            var min = $('#filterTanggalMulai').val();
            var max = $('#filterTanggalAkhir').val();
            var dateText = data[3]; // Date column
            
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
});
</script>
@endpush