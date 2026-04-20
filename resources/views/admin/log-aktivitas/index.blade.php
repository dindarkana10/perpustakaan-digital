<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard Admin - Log Aktivitas</title>

  <link rel="stylesheet" href="{{ asset('template/css/styles.min.css') }}" />
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    .badge-login { background-color: #198754; }
    .badge-logout { background-color: #6c757d; }
    .badge-create { background-color: #0d6efd; }
    .badge-update { background-color: #ffc107; color:#000; }
    .badge-delete { background-color: #dc3545; }
    .badge-peminjaman { background-color: #6f42c1; } /* Ungu */
    .badge-pengembalian { background-color: #fd7e14; } /* Oranye */
    .filter-section { background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #e9ecef; }
  </style>
</head>

<body>
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6"
    data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">

    <x-navbar></x-navbar>
    <x-sidebar></x-sidebar>

    <div class="body-wrapper">
      <div class="container-fluid">

        <div class="card">
          <div class="card-body">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
              <h5 class="card-title fw-semibold">Log Aktivitas</h5>
              <form action="{{ route('admin.log-aktivitas.deleteAll') }}" method="POST" id="formDeleteAll">
                @csrf
                @method('DELETE')
                <button type="button" class="btn btn-danger btn-sm" onclick="confirmDeleteAll()">
                  <i class="ti ti-trash"></i> Hapus Semua Log
                </button>
              </form>
            </div>

            <div class="filter-section">
              <form action="{{ route('admin.log-aktivitas.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                  <label class="form-label">User</label>
                  <select name="user_id" class="form-select form-select-sm">
                    <option value="">Semua User</option>
                    @foreach($users as $user)
                      <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                        {{ $user->name }}
                      </option>
                    @endforeach
                  </select>
                </div>
               <div class="col-md-2">
                    <label class="form-label">Aktivitas</label>
                    <select name="activity" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        <option value="Login" {{ request('activity') == 'Login' ? 'selected' : '' }}>Login</option>
                        <option value="Peminjaman" {{ request('activity') == 'Peminjaman' ? 'selected' : '' }}>Peminjaman (Semua)</option>
                        <option value="Pengembalian" {{ request('activity') == 'Pengembalian' ? 'selected' : '' }}>Pengembalian (Semua)</option>
                        <option value="Tambah" {{ request('activity') == 'Tambah' ? 'selected' : '' }}>Tambah Data</option>
                        <option value="Edit" {{ request('activity') == 'Edit' ? 'selected' : '' }}>Edit Data</option>
                        <option value="Hapus" {{ request('activity') == 'Hapus' ? 'selected' : '' }}>Hapus Data</option>
                    </select>
                </div>
                <div class="col-md-2">
                  <label class="form-label">Dari Tanggal</label>
                  <input type="date" name="date_start" class="form-control form-control-sm" value="{{ request('date_start') }}">
                </div>
                <div class="col-md-2">
                  <label class="form-label">Sampai Tanggal</label>
                  <input type="date" name="date_end" class="form-control form-control-sm" value="{{ request('date_end') }}">
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                  <button type="submit" class="btn btn-primary btn-sm w-100">Filter</button>
                  <a href="{{ route('admin.log-aktivitas.index') }}" class="btn btn-light btn-sm w-100">Reset</a>
                </div>
              </form>
            </div>

            <div class="table-responsive">
              <table id="logTable" class="table table-bordered table-striped">
                <thead class="table-light">
                  <tr>
                    <th>No</th>
                    <th>Waktu</th>
                    <th>User</th>
                    <th>Role</th>
                    <th>Aktivitas</th>
                    <th>Model</th>
                    <th>Keterangan</th>
                    <th>IP Address</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($logs as $log)
                  <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $log->created_at->format('d-m-Y H:i') }}</td>
                    <td>{{ $log->user->name ?? 'System' }}</td>
                    <td>
                      <span class="badge bg-secondary">
                        {{ $log->user->role ?? '-' }}
                      </span>
                    </td>
                    <td>
                      @php
                        $badge = 'bg-secondary';
                        $act = strtolower($log->activity);
                        if(str_contains($act,'login')) $badge='badge-login';
                        elseif(str_contains($act,'peminjaman')) $badge='badge-peminjaman';
                        elseif(str_contains($act,'pengembalian')) $badge='badge-pengembalian';
                        elseif(str_contains($act,'tambah')) $badge='badge-create';
                        elseif(str_contains($act,'edit')) $badge='badge-update';
                        elseif(str_contains($act,'hapus')) $badge='badge-delete';
                      @endphp
                      <span class="badge {{ $badge }}">
                        {{ $log->activity }}
                      </span>
                    </td>
                    <td>
                      {{ $log->model }}
                      @if($log->model_id)
                        <small class="text-muted">(#{{ $log->model_id }})</small>
                      @endif
                    </td>
                    <td>{{ $log->keterangan ?? '-' }}</td>
                    <td>{{ $log->ip_address }}</td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>

          </div>
        </div>

      </div>
    </div>
  </div>

  <script src="{{ asset('template/libs/jquery/dist/jquery.min.js') }}"></script>
  <script src="{{ asset('template/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('template/js/sidebarmenu.js') }}"></script>
  <script src="{{ asset('template/js/app.min.js') }}"></script>
  <script src="{{ asset('template/libs/simplebar/dist/simplebar.js') }}"></script>

  <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

  <script>
    $(document).ready(function () {
      $('#logTable').DataTable({
        language: {
          url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        },
        pageLength: 25,
        order: [[1, 'desc']]
      });
    });

    function confirmDeleteAll() {
      Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Semua data log aktivitas akan dihapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus Semua!',
        cancelButtonText: 'Batal'
      }).then((result) => {
        if (result.isConfirmed) {
          document.getElementById('formDeleteAll').submit();
        }
      })
    }
  </script>

  @if(session('success'))
    <script>
      Swal.fire('Berhasil!', "{{ session('success') }}", 'success');
    </script>
  @endif
</body>
</html>