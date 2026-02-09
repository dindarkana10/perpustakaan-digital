<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard Admin - Peminjaman</title>
  <link rel="stylesheet" href="{{ asset ('template/css/styles.min.css') }}" />
  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
  <style>
    .img-preview {
      max-width: 200px;
      max-height: 200px;
      margin-top: 10px;
      display: none;
    }
  </style>
</head>

<body>
  <!--  Body Wrapper -->
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">

    <!--  App Topstrip -->
    <x-navbar></x-navbar>

    <!-- Sidebar Start -->
    <x-sidebar></x-sidebar>
    <!--  Sidebar End -->

    <!--  Main wrapper -->
      <div class="body-wrapper">
        <div class="container-fluid">

        <div class="card">
            <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title fw-semibold">Data Peminjaman</h5>

                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPeminjamanModal">
                  <iconify-icon icon="solar:add-circle-outline" width="18" class="me-1"></iconify-icon>
                  Tambah Peminjaman
                </button>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                  {{ session('success') }}
                  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                  {{ session('error') }}
                  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error!</strong>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="table-responsive">
                <form method="GET" class="row g-2 mb-3">
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="menunggu_persetujuan" {{ request('status') == 'menunggu_persetujuan' ? 'selected' : '' }}>Menunggu Persetujuan</option>
                            <option value="dipinjam" {{ request('status') == 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                            <option value="dikembalikan" {{ request('status') == 'dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                            <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                            <option value="terlambat" {{ request('status') == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <select name="user_id" class="form-select">
                            <option value="">Semua Peminjam</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <button class="btn btn-primary w-100">Filter</button>
                    </div>

                    <div class="col-md-2">
                        <a href="{{ route('peminjaman.index') }}" class="btn btn-danger w-100">
                            <iconify-icon icon="solar:restart-outline" width="18" class="me-1"></iconify-icon>
                            Reset
                        </a>
                    </div>
                </form>


                <table id="peminjamanTable" class="table table-bordered table-striped">
                <thead class="table-light">
                    <tr>
                    <th>No</th>
                    <th>Peminjam</th>
                    <th>Disetujui Oleh</th>
                    <th>Tanggal Pinjam</th>
                    <th>Tanggal Kembali</th>
                    {{-- <th>Keperluan</th> --}}
                    <th>Status</th>
                    <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($peminjamen as $peminjaman)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $peminjaman->user->name }}</td>
                        <td>
                            @if($peminjaman->petugas)
                                {{ $peminjaman->petugas->name }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('d/m/Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($peminjaman->tanggal_kembali_rencana)->format('d/m/Y') }}</td>
                        {{-- <td>{{ Str::limit($peminjaman->keperluan, 30) }}</td> --}}
                        <td>
                            @if($peminjaman->status == 'menunggu_persetujuan')
                                <span class="badge bg-info text-white">Menunggu Persetujuan</span>
                            @elseif($peminjaman->status == 'disetujui')
                                <span class="badge bg-primary">Disetujui</span>
                            @elseif($peminjaman->status == 'dipinjam')
                                <span class="badge bg-warning text-dark">Dipinjam</span>
                            @elseif($peminjaman->status == 'dikembalikan')
                                <span class="badge bg-success">Dikembalikan</span>
                            @elseif($peminjaman->status == 'ditolak')
                                <span class="badge bg-danger">Ditolak</span>
                            @else
                                <span class="badge bg-dark">Terlambat</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <!-- Tombol Detail (selalu ada) -->
                            <button type="button" class="btn btn-sm btn-outline-info me-1 showBtn" data-id="{{ $peminjaman->id }}">
                                <iconify-icon icon="solar:eye-outline" width="18"></iconify-icon>
                            </button>

                            <!-- HANYA BISA EDIT & HAPUS YANG MENUNGGU PERSETUJUAN -->
                            @if($peminjaman->status == 'menunggu_persetujuan')
                                <!-- Tombol Edit -->
                                <button type="button" class="btn btn-sm btn-outline-primary me-1 editBtn"
                                    data-id="{{ $peminjaman->id }}"
                                    data-user="{{ $peminjaman->user_id }}"
                                    data-tanggal-pinjam="{{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('Y-m-d') }}"
                                    data-tanggal-kembali="{{ \Carbon\Carbon::parse($peminjaman->tanggal_kembali_rencana)->format('Y-m-d') }}"
                                    data-keperluan="{{ $peminjaman->keperluan }}"
                                    title="Edit">
                                    <iconify-icon icon="solar:pen-2-outline" width="18"></iconify-icon>
                                </button>

                                <!-- Tombol Hapus -->
                                <form action="{{ route('peminjaman.destroy', $peminjaman->id) }}" method="POST" class="d-inline deleteForm">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                        <iconify-icon icon="solar:trash-bin-trash-outline" width="18"></iconify-icon>
                                    </button>
                                </form>
                            @endif

                            <!-- JIKA STATUS DITOLAK, BISA DIHAPUS -->
                            @if($peminjaman->status == 'ditolak')
                                <form action="{{ route('peminjaman.destroy', $peminjaman->id) }}" method="POST" class="d-inline deleteForm">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                        <iconify-icon icon="solar:trash-bin-trash-outline" width="18"></iconify-icon>
                                    </button>
                                </form>
                            @endif

                            <!-- STATUS LAIN: TIDAK ADA AKSI (hanya lihat detail) -->
                        </td>
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

  <!-- Modal Create Peminjaman -->
  <div class="modal fade" id="createPeminjamanModal" tabindex="-1" aria-labelledby="createPeminjamanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="createPeminjamanModalLabel">Tambah Peminjaman Baru</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="{{ route('peminjaman.store') }}" method="POST" id="createPeminjamanForm">
          @csrf
          <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                      <label for="user_id" class="form-label">Peminjam <span class="text-danger">*</span></label>
                      <select class="form-select @error('user_id') is-invalid @enderror" 
                              id="user_id" name="user_id" required>
                        <option value="">Pilih Peminjam</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                      </select>
                      <div class="invalid-feedback">
                        @error('user_id') {{ $message }} @else Pilih peminjam. @enderror
                      </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                      <label for="tanggal_pinjam" class="form-label">Tanggal Pinjam <span class="text-danger">*</span></label>
                      <input type="date" class="form-control @error('tanggal_pinjam') is-invalid @enderror" 
                             id="tanggal_pinjam" name="tanggal_pinjam" value="{{ old('tanggal_pinjam', date('Y-m-d')) }}" 
                             required>
                      <div class="invalid-feedback">
                        @error('tanggal_pinjam') {{ $message }} @else Tanggal pinjam wajib diisi. @enderror
                      </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                      <label for="tanggal_kembali_rencana" class="form-label">Tanggal Kembali <span class="text-danger">*</span></label>
                      <input type="date" class="form-control @error('tanggal_kembali_rencana') is-invalid @enderror" 
                             id="tanggal_kembali_rencana" name="tanggal_kembali_rencana" value="{{ old('tanggal_kembali_rencana') }}" 
                             required>
                      <div class="invalid-feedback">
                        @error('tanggal_kembali_rencana') {{ $message }} @else Tanggal kembali wajib diisi. @enderror
                      </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="mb-3">
                      <label for="keperluan" class="form-label">Keperluan <span class="text-danger">*</span></label>
                      <textarea class="form-control @error('keperluan') is-invalid @enderror" 
                                id="keperluan" name="keperluan" rows="2" 
                                maxlength="1000" required>{{ old('keperluan') }}</textarea>
                      <div class="invalid-feedback">
                        @error('keperluan') {{ $message }} @else Keperluan wajib diisi. @enderror
                      </div>
                    </div>
                </div>
            </div>

            <hr>
            <h6 class="mb-3">Detail Alat yang Dipinjam</h6>
            
            <div id="alatContainer">
                <div class="row alat-item mb-2">
                    <div class="col-md-5">
                        <label class="form-label">Alat <span class="text-danger">*</span></label>
                        <select class="form-select" name="alat_id[]" required>
                            <option value="">Pilih Alat</option>
                            @foreach($alats as $alat)
                                <option value="{{ $alat->id }}" data-stok="{{ $alat->stok_tersedia }}">
                                    {{ $alat->nama_alat }} (Stok: {{ $alat->stok_tersedia }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Jumlah <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="jumlah[]" min="1" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Kondisi Pinjam <span class="text-danger">*</span></label>
                        <select class="form-select" name="kondisi_pinjam[]" required>
                            <option value="">Pilih</option>
                            <option value="baik">Baik</option>
                            <option value="rusak_ringan">Rusak Ringan</option>
                            <option value="rusak_berat">Rusak Berat</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="button" class="btn btn-danger w-100 removeAlat" style="display:none;">
                            <iconify-icon icon="solar:trash-bin-trash-outline" width="18"></iconify-icon>
                        </button>
                    </div>
                </div>
            </div>

            <button type="button" class="btn btn-sm btn-outline-primary" id="addAlat">
                <iconify-icon icon="solar:add-circle-outline" width="18"></iconify-icon>
                Tambah Alat
            </button>

          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Edit Peminjaman -->
    <div class="modal fade" id="editPeminjamanModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title">Edit Peminjaman</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editPeminjamanForm" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                        <label for="edit_user_id" class="form-label">Peminjam <span class="text-danger">*</span></label>
                        <select class="form-select" id="edit_user_id" name="user_id" required>
                            <option value="">Pilih Peminjam</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                        <label for="edit_tanggal_pinjam" class="form-label">Tanggal Pinjam <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="edit_tanggal_pinjam" name="tanggal_pinjam" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                        <label for="edit_tanggal_kembali_rencana" class="form-label">Tanggal Kembali <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="edit_tanggal_kembali_rencana" name="tanggal_kembali_rencana" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                        <label for="edit_keperluan" class="form-label">Keperluan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="edit_keperluan" name="keperluan" rows="3" maxlength="1000" required></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
            </form>
        </div>
        </div>
    </div>

  <!-- Modal Show Peminjaman -->
  <div class="modal fade" id="showPeminjamanModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Detail Peminjaman</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <div class="row">
            <div class="col-md-3 mb-3">
                <label class="form-label">Peminjam</label>
                <input type="text" class="form-control" id="show_peminjam" readonly>
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Petugas</label>
                <input type="text" class="form-control" id="show_petugas" readonly>
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Tanggal Pinjam</label>
                <input type="text" class="form-control" id="show_tanggal_pinjam" readonly>
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Tanggal Kembali</label>
                <input type="text" class="form-control" id="show_tanggal_kembali" readonly>
            </div>
            </div>

            <div class="row">
            <div class="col-md-9 mb-3">
                <label class="form-label">Keperluan</label>
                <textarea class="form-control" id="show_keperluan" rows="2" readonly></textarea>
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Status</label>
                <input type="text" class="form-control" id="show_status" readonly>
            </div>
            </div>

            <hr>
            <h6 class="mb-3">Detail Alat yang Dipinjam</h6>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Alat</th>
                            <th>Kategori</th>
                            <th>Jumlah</th>
                            <th>Kondisi Pinjam</th>
                        </tr>
                    </thead>
                    <tbody id="show_detail_alat">
                    </tbody>
                </table>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
        </div>
        </div>
    </div>
  </div>

    <script src="{{ asset ('template/libs/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset ('template/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset ('template/js/sidebarmenu.js') }}"></script>
    <script src="{{ asset ('template/js/app.min.js') }}"></script>
    <script src="{{ asset ('template/libs/simplebar/dist/simplebar.js') }}"></script>
    <!-- solar icons -->
    <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    
    <!-- Sweetalert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    $(document).ready(function () {

    /* ================= DATATABLE ================= */
    $('#peminjamanTable').DataTable({
        language: { 
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
            emptyTable: 'Data peminjaman belum ada',
            zeroRecords: 'Tidak ada data yang cocok'
        },
        pageLength: 10,
        order: [[0, 'asc']],
        columnDefs: [
            { orderable: false, targets: [6] }
        ]
    });

    /* ================= ADD/REMOVE ALAT ================= */
    let alatItemTemplate = $('.alat-item').first().clone();
    
    $('#addAlat').click(function() {
        let newItem = alatItemTemplate.clone();
        newItem.find('select, input').val('');
        newItem.find('.removeAlat').show();
        $('#alatContainer').append(newItem);
        updateRemoveButtons();
    });

    $(document).on('click', '.removeAlat', function() {
        $(this).closest('.alat-item').remove();
        updateRemoveButtons();
    });

    function updateRemoveButtons() {
        let count = $('.alat-item').length;
        if (count === 1) {
            $('.removeAlat').hide();
        } else {
            $('.removeAlat').show();
        }
    }

    /* ================= EDIT BUTTON ================= */
    $(document).on('click', '.editBtn', function () {
        $('#edit_user_id').val($(this).data('user'));
        $('#edit_tanggal_pinjam').val($(this).data('tanggal-pinjam'));
        $('#edit_tanggal_kembali_rencana').val($(this).data('tanggal-kembali'));
        $('#edit_keperluan').val($(this).data('keperluan'));
        
        $('#editPeminjamanForm').attr('action', '/admin/peminjaman/' + $(this).data('id'));
        $('#editPeminjamanModal').modal('show');
    });

    /* ================= SHOW DETAIL ================= */
    $(document).on('click', '.showBtn', function () {
        let id = $(this).data('id');
        
        $.ajax({
            url: '/admin/peminjaman/' + id,
            type: 'GET',
            success: function(response) {
                $('#show_peminjam').val(response.user.name);
                
                // Cek apakah ada petugas
                if (response.petugas) {
                    $('#show_petugas').val(response.petugas.name);
                } else {
                    $('#show_petugas').val('-');
                }
                
                $('#show_tanggal_pinjam').val(new Date(response.tanggal_pinjam).toLocaleDateString('id-ID'));
                $('#show_tanggal_kembali').val(new Date(response.tanggal_kembali_rencana).toLocaleDateString('id-ID'));
                $('#show_keperluan').val(response.keperluan);
                
                let statusBadge = '';
                if (response.status === 'menunggu_persetujuan') {
                    statusBadge = 'Menunggu Persetujuan';
                } else if (response.status === 'disetujui') {
                    statusBadge = 'Disetujui';
                } else if (response.status === 'dipinjam') {
                    statusBadge = 'Dipinjam';
                } else if (response.status === 'dikembalikan') {
                    statusBadge = 'Dikembalikan';
                } else if (response.status === 'ditolak') {
                    statusBadge = 'Ditolak';
                } else {
                    statusBadge = 'Terlambat';
                }
                $('#show_status').val(statusBadge);
                
                let detailHtml = '';
                response.details.forEach(function(detail, index) {
                    let kondisiBadge = '';
                    if (detail.kondisi_pinjam === 'baik') {
                        kondisiBadge = '<span class="badge bg-success">Baik</span>';
                    } else if (detail.kondisi_pinjam === 'rusak_ringan') {
                        kondisiBadge = '<span class="badge bg-warning">Rusak Ringan</span>';
                    } else {
                        kondisiBadge = '<span class="badge bg-danger">Rusak Berat</span>';
                    }
                    
                    detailHtml += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${detail.alat.nama_alat}</td>
                            <td>${detail.alat.kategori.nama_kategori}</td>
                            <td class="text-center">${detail.jumlah}</td>
                            <td>${kondisiBadge}</td>
                        </tr>
                    `;
                });
                $('#show_detail_alat').html(detailHtml);
                
                $('#showPeminjamanModal').modal('show');
            },
            error: function() {
                Swal.fire('Error!', 'Gagal mengambil data peminjaman', 'error');
            }
        });
    });

    /* ================= VALIDATE DATE ================= */
    function validateDate() {
        const tanggalPinjam = new Date($('#tanggal_pinjam').val());
        const tanggalKembali = new Date($('#tanggal_kembali_rencana').val());
        
        if (tanggalKembali < tanggalPinjam) {
            $('#tanggal_kembali_rencana').addClass('is-invalid');
            return false;
        } else {
            $('#tanggal_kembali_rencana').removeClass('is-invalid');
            return true;
        }
    }

    $('#tanggal_pinjam, #tanggal_kembali_rencana').on('change', validateDate);

    /* ================= FORM SUBMIT VALIDATION ================= */
    $('#createPeminjamanForm').on('submit', function(e) {
        if (!validateDate()) {
            e.preventDefault();
            Swal.fire('Error!', 'Tanggal kembali tidak boleh sebelum tanggal pinjam!', 'error');
        }
    });

    /* ================= RESET MODAL ================= */
    $('#createPeminjamanModal, #editPeminjamanModal').on('hidden.bs.modal', function () {
        $(this).find('form')[0].reset();
        $(this).find('.is-valid, .is-invalid').removeClass('is-valid is-invalid');
        
        // Reset alat items to just one
        $('.alat-item').not(':first').remove();
        updateRemoveButtons();
    });

    /* ================= DELETE CONFIRM ================= */
    $(document).on('submit', '.deleteForm', function (e) {
        e.preventDefault();
        const form = this;

        Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: 'Data peminjaman yang dihapus tidak dapat dikembalikan!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonText: 'Batal',
        confirmButtonText: 'Ya, Hapus'
        }).then((result) => {
        if (result.isConfirmed) form.submit();
        });
    });

    /* ================= RETURN CONFIRM ================= */
    $(document).on('submit', '.returnForm', function (e) {
        e.preventDefault();
        const form = this;

        Swal.fire({
        title: 'Proses Pengembalian?',
        text: 'Alat akan dikembalikan dan stok akan bertambah.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonText: 'Batal',
        confirmButtonText: 'Ya, Kembalikan'
        }).then((result) => {
        if (result.isConfirmed) form.submit();
        });
    });

    /* ================= APPROVE CONFIRM ================= */
    $(document).on('submit', '.approveForm', function (e) {
        e.preventDefault();
        const form = this;

        Swal.fire({
            title: 'Setujui Peminjaman?',
            text: 'Stok alat akan dikurangi setelah disetujui.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonText: 'Batal',
            confirmButtonText: 'Ya, Setujui'
        }).then((result) => {
            if (result.isConfirmed) form.submit();
        });
    });

    /* ================= REJECT CONFIRM ================= */
    $(document).on('submit', '.rejectForm', function (e) {
        e.preventDefault();
        const form = this;

        Swal.fire({
            title: 'Tolak Peminjaman?',
            text: 'Peminjaman akan ditandai sebagai ditolak.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonText: 'Batal',
            confirmButtonText: 'Ya, Tolak'
        }).then((result) => {
            if (result.isConfirmed) form.submit();
        });
    });

    /* ================= AUTO HIDE ALERT ================= */
    setTimeout(() => $('.alert').fadeOut(), 3000);

    });
    </script>

</body>

</html>