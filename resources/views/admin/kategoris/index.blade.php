<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard Admin - Kategori Buku</title>
  <link rel="stylesheet" href="{{ asset ('template/css/styles.min.css') }}" />
  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
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
                <h5 class="card-title fw-semibold">Data Kategori Buku</h5>

                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createKategoriModal">
                  <iconify-icon icon="solar:add-circle-outline" width="18" class="me-1"></iconify-icon>
                  Tambah Kategori Buku
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
                <table id="kategoriTable" class="table table-bordered table-striped">
                <thead class="table-light">
                    <tr>
                    <th>No</th>
                    <th>Nama Kategori</th>
                    <th>Deskripsi</th>
                    <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($kategoris as $kategori)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $kategori->nama_kategori }}</td>
                        <td>{{ $kategori->deskripsi ?? '-' }}</td>
                        <td class="text-center">
                        <button type="button" 
                                class="btn btn-sm btn-outline-primary me-1 editBtn"
                                data-id="{{ $kategori->id }}"
                                data-nama="{{ $kategori->nama_kategori }}"
                                data-deskripsi="{{ $kategori->deskripsi }}"
                                title="Edit">
                            <iconify-icon icon="solar:pen-2-outline" width="18"></iconify-icon>
                        </button>

                        <form action="{{ route('kategoris.destroy', $kategori->id) }}"
                                method="POST"
                                class="d-inline deleteForm">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="btn btn-sm btn-outline-danger"
                                    title="Hapus">
                            <iconify-icon icon="solar:trash-bin-trash-outline" width="18"></iconify-icon>
                            </button>
                        </form>
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

  <!-- Modal Create Kategori -->
  <div class="modal fade" id="createKategoriModal" tabindex="-1" aria-labelledby="createKategoriModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="createKategoriModalLabel">Tambah Kategori Buku Baru</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="{{ route('kategoris.store') }}" method="POST" id="createKategoriForm">
          @csrf
          <div class="modal-body">
            <div class="mb-3">
              <label for="nama_kategori" class="form-label">Nama Kategori <span class="text-danger">*</span></label>
              <input type="text" class="form-control @error('nama_kategori') is-invalid @enderror" 
                     id="nama_kategori" name="nama_kategori" value="{{ old('nama_kategori') }}" 
                     required minlength="3" maxlength="255">
              <div class="invalid-feedback">
                @error('nama_kategori') {{ $message }} @else Nama kategori minimal 3 karakter. @enderror
              </div>
            </div>
            <div class="mb-3">
              <label for="deskripsi" class="form-label">Deskripsi</label>
              <textarea class="form-control @error('deskripsi') is-invalid @enderror" 
                        id="deskripsi" name="deskripsi" rows="4" 
                        maxlength="1000">{{ old('deskripsi') }}</textarea>
              <div class="invalid-feedback">
                @error('deskripsi') {{ $message }} @else Deskripsi maksimal 1000 karakter. @enderror
              </div>
              <small class="text-muted">Opsional - Maksimal 1000 karakter</small>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Edit Kategori -->
  <div class="modal fade" id="editKategoriModal" tabindex="-1" aria-labelledby="editKategoriModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editKategoriModalLabel">Edit Kategori</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="editKategoriForm" method="POST">
          @csrf
          @method('PUT')
          <div class="modal-body">
            <div class="mb-3">
              <label for="edit_nama_kategori" class="form-label">Nama Kategori <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="edit_nama_kategori" name="nama_kategori" 
                     required minlength="3" maxlength="255">
              <div class="invalid-feedback">
                Nama kategori minimal 3 karakter.
              </div>
            </div>
            <div class="mb-3">
              <label for="edit_deskripsi" class="form-label">Deskripsi</label>
              <textarea class="form-control" id="edit_deskripsi" name="deskripsi" 
                        rows="4" maxlength="1000"></textarea>
              <div class="invalid-feedback">
                Deskripsi maksimal 1000 karakter.
              </div>
              <small class="text-muted">Opsional - Maksimal 1000 karakter</small>
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
    $('#kategoriTable').DataTable({
        language: { 
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
            emptyTable: 'Data kategori belum ada',
            zeroRecords: 'Tidak ada data yang cocok'
        },
        pageLength: 10,
        order: [[0, 'asc']],
        columnDefs: [{ orderable: false, targets: 3 }]
    });

    /* ================= EDIT BUTTON ================= */
    $(document).on('click', '.editBtn', function () {
        $('#edit_nama_kategori').val($(this).data('nama'));
        $('#edit_deskripsi').val($(this).data('deskripsi'));
        $('#editKategoriForm').attr('action', '/admin/kategoris/' + $(this).data('id'));
        $('#editKategoriModal').modal('show');
    });

    /* ================= RESET MODAL ================= */
    $('#createKategoriModal, #editKategoriModal').on('hidden.bs.modal', function () {
        $(this).find('form')[0].reset();
        $(this).find('.is-valid, .is-invalid').removeClass('is-valid is-invalid');
    });

    /* ================= DELETE CONFIRM ================= */
    $(document).on('submit', '.deleteForm', function (e) {
        e.preventDefault();
        const form = this;

        Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: 'Data kategori yang dihapus tidak dapat dikembalikan!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonText: 'Batal',
        confirmButtonText: 'Ya, Hapus'
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