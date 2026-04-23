<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard Admin - User</title>
  <link rel="stylesheet" href="{{ asset ('template/css/styles.min.css') }}" />
  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
  <style>
    .password-toggle {
      position: relative;
    }
    .password-toggle-icon {
      position: absolute;
      right: 10px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      color: #6c757d;
    }
    .password-toggle-icon:hover {
      color: #495057;
    }
    /* Animasi smooth untuk field yang muncul/hilang */
    .field-peminjam {
      overflow: hidden;
      transition: max-height 0.35s ease, opacity 0.35s ease, margin 0.35s ease;
    }
    .field-peminjam.d-none {
      max-height: 0 !important;
      opacity: 0;
      margin-bottom: 0 !important;
    }
    .field-peminjam:not(.d-none) {
      max-height: 200px;
      opacity: 1;
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
              <h5 class="card-title fw-semibold">Data User</h5>
              <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
                <iconify-icon icon="solar:add-circle-outline" width="18" class="me-1"></iconify-icon>
                Tambah User
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
              <table id="userTable" class="table table-bordered table-striped">
                <thead class="table-light">
                  <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>NISN</th>
                    <th>Kelas / Jurusan</th>
                    <th>Role</th>
                    <th>Last Login</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse ($users as $user)
                  <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->NISN ?? '-' }}</td>
                    <td>{{ $user->kelas_jurusan ?? '-' }}</td>
                    <td>
                      <span class="badge 
                        @if($user->role == 'admin') bg-danger
                        @elseif($user->role == 'peminjam') bg-info
                        @endif">
                        {{ ucfirst($user->role) }}
                      </span>
                    </td>
                    <td>
                      {{ $user->last_login ? $user->last_login->format('d-m-Y H:i') : '-' }}
                    </td>
                    <td class="text-center">
                      <button type="button"
                              class="btn btn-sm btn-outline-primary me-1 editBtn"
                              data-id="{{ $user->id }}"
                              data-name="{{ $user->name }}"
                              data-email="{{ $user->email }}"
                              data-role="{{ $user->role }}"
                              data-nisn="{{ $user->NISN }}"
                              data-kelas="{{ $user->kelas_jurusan }}"
                              title="Edit">
                        <iconify-icon icon="solar:pen-2-outline" width="18"></iconify-icon>
                      </button>

                      <form action="{{ route('users.destroy', $user->id) }}"
                            method="POST"
                            class="d-inline deleteForm">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                          <iconify-icon icon="solar:trash-bin-trash-outline" width="18"></iconify-icon>
                        </button>
                      </form>
                    </td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="8" class="text-center">Data user belum ada</td>
                  </tr>
                  @endforelse
                </tbody>
              </table>
            </div>

          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- ==================== Modal Create User ==================== -->
  <div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="createUserModalLabel">Tambah User Baru</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="{{ route('users.store') }}" method="POST" id="createUserForm" autocomplete="off">
          @csrf
          {{-- Dummy anti-autofill --}}
          <input type="text" style="display:none" name="fake_username">
          <input type="password" style="display:none" name="fake_password">
          <div class="modal-body">

            {{-- ROLE (diletakkan PALING ATAS agar langsung bisa trigger tampil/sembunyikan field) --}}
            <div class="mb-3">
              <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
              <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                <option value="">Pilih Role</option>
                <option value="admin"    {{ old('role') == 'admin'    ? 'selected' : '' }}>Admin</option>
                <option value="peminjam" {{ old('role') == 'peminjam' ? 'selected' : '' }}>Peminjam</option>
              </select>
              <div class="invalid-feedback">
                @error('role') {{ $message }} @else Pilih role user. @enderror
              </div>
            </div>

            {{-- NAMA --}}
            <div class="mb-3">
              <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
              <input type="text"
                     class="form-control @error('name') is-invalid @enderror"
                     id="name" name="name"
                     value="{{ old('name') }}"
                     required minlength="3" autocomplete="off">
              <div class="invalid-feedback">
                @error('name') {{ $message }} @else Nama minimal 3 karakter. @enderror
              </div>
            </div>

            {{-- NISN — hanya tampil jika role = peminjam --}}
            <div class="mb-3 field-peminjam {{ old('role', '') !== 'peminjam' ? 'd-none' : '' }}" id="create_nisn_wrap">
              <label for="NISN" class="form-label">NISN <span class="text-danger">*</span></label>
              <input type="text"
                     class="form-control @error('NISN') is-invalid @enderror"
                     id="NISN" name="NISN"
                     value="{{ old('NISN') }}"
                     maxlength="20" autocomplete="off">
              <div class="invalid-feedback">
                @error('NISN') {{ $message }} @else NISN wajib diisi. @enderror
              </div>
            </div>

            {{-- KELAS & JURUSAN — hanya tampil jika role = peminjam --}}
            <div class="mb-3 field-peminjam {{ old('role', '') !== 'peminjam' ? 'd-none' : '' }}" id="create_kelas_wrap">
              <label for="kelas_jurusan" class="form-label">Kelas & Jurusan <span class="text-danger">*</span></label>
              <input type="text"
                     class="form-control @error('kelas_jurusan') is-invalid @enderror"
                     id="kelas_jurusan" name="kelas_jurusan"
                     value="{{ old('kelas_jurusan') }}"
                     placeholder="Contoh: XII RPL 1" maxlength="100" autocomplete="off">
              <div class="invalid-feedback">
                @error('kelas_jurusan') {{ $message }} @else Kelas & Jurusan wajib diisi. @enderror
              </div>
            </div>

            {{-- EMAIL --}}
            <div class="mb-3">
              <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
              <input type="email"
                     class="form-control @error('email') is-invalid @enderror"
                     id="email" name="email"
                     value="{{ old('email') }}"
                     required autocomplete="off">
              <div class="invalid-feedback">
                @error('email') {{ $message }} @else Masukkan email yang valid. @enderror
              </div>
            </div>

            {{-- PASSWORD --}}
            <div class="mb-3">
              <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
              <div class="password-toggle">
                <input type="password"
                       class="form-control @error('password') is-invalid @enderror"
                       id="password" name="password"
                       required minlength="8" autocomplete="new-password">
                <iconify-icon icon="solar:eye-outline" width="20" class="password-toggle-icon" id="togglePassword"></iconify-icon>
              </div>
              <div class="invalid-feedback">
                @error('password') {{ $message }} @else Password minimal 8 karakter. @enderror
              </div>
              <small class="text-muted">Minimal 8 karakter</small>
            </div>

            {{-- KONFIRMASI PASSWORD --}}
            <div class="mb-3">
              <label for="password_confirmation" class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
              <div class="password-toggle">
                <input type="password"
                       class="form-control"
                       id="password_confirmation" name="password_confirmation"
                       required minlength="8" autocomplete="new-password">
                <iconify-icon icon="solar:eye-outline" width="20" class="password-toggle-icon" id="togglePasswordConfirm"></iconify-icon>
              </div>
              <div class="invalid-feedback">Password tidak cocok.</div>
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

  <!-- ==================== Modal Edit User ==================== -->
  <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="editUserForm" method="POST" autocomplete="off">
          @csrf
          @method('PUT')
          <input type="text" style="display:none" name="fake_username_edit">
          <input type="password" style="display:none" name="fake_password_edit">
          <div class="modal-body">

            {{-- ROLE --}}
            <div class="mb-3">
              <label for="edit_role" class="form-label">Role <span class="text-danger">*</span></label>
              <select class="form-select" id="edit_role" name="role" required>
                <option value="">Pilih Role</option>
                <option value="admin">Admin</option>
                <option value="peminjam">Peminjam</option>
              </select>
              <div class="invalid-feedback">Pilih role user.</div>
            </div>

            {{-- NAMA --}}
            <div class="mb-3">
              <label for="edit_name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
              <input type="text"
                     class="form-control"
                     id="edit_name" name="name"
                     required minlength="3" autocomplete="off">
              <div class="invalid-feedback">Nama minimal 3 karakter.</div>
            </div>

            {{-- NISN — hanya tampil jika role = peminjam --}}
            <div class="mb-3 field-peminjam d-none" id="edit_nisn_wrap">
              <label for="edit_NISN" class="form-label">NISN <span class="text-danger">*</span></label>
              <input type="text"
                     class="form-control"
                     id="edit_NISN" name="NISN"
                     maxlength="20" autocomplete="off">
              <div class="invalid-feedback">NISN wajib diisi.</div>
            </div>

            {{-- KELAS & JURUSAN — hanya tampil jika role = peminjam --}}
            <div class="mb-3 field-peminjam d-none" id="edit_kelas_wrap">
              <label for="edit_kelas_jurusan" class="form-label">Kelas & Jurusan <span class="text-danger">*</span></label>
              <input type="text"
                     class="form-control"
                     id="edit_kelas_jurusan" name="kelas_jurusan"
                     placeholder="Contoh: XII RPL 1" maxlength="100" autocomplete="off">
              <div class="invalid-feedback">Kelas & Jurusan wajib diisi.</div>
            </div>

            {{-- EMAIL --}}
            <div class="mb-3">
              <label for="edit_email" class="form-label">Email <span class="text-danger">*</span></label>
              <input type="email"
                     class="form-control"
                     id="edit_email" name="email"
                     required autocomplete="off">
              <div class="invalid-feedback">Masukkan email yang valid.</div>
            </div>

            {{-- PASSWORD --}}
            <div class="mb-3">
              <label for="edit_password" class="form-label">
                Password <small class="text-muted">(kosongkan jika tidak ingin mengubah)</small>
              </label>
              <div class="password-toggle">
                <input type="password"
                       class="form-control"
                       id="edit_password" name="password"
                       minlength="8" autocomplete="new-password">
                <iconify-icon icon="solar:eye-outline" width="20" class="password-toggle-icon" id="toggleEditPassword"></iconify-icon>
              </div>
              <div class="invalid-feedback">Password minimal 8 karakter.</div>
              <small class="text-muted">Minimal 8 karakter</small>
            </div>

            {{-- KONFIRMASI PASSWORD --}}
            <div class="mb-3">
              <label for="edit_password_confirmation" class="form-label">Konfirmasi Password</label>
              <div class="password-toggle">
                <input type="password"
                       class="form-control"
                       id="edit_password_confirmation" name="password_confirmation"
                       minlength="8" autocomplete="new-password">
                <iconify-icon icon="solar:eye-outline" width="20" class="password-toggle-icon" id="toggleEditPasswordConfirm"></iconify-icon>
              </div>
              <div class="invalid-feedback">Password tidak cocok.</div>
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
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
  $(document).ready(function () {

    /* ================= DATATABLE ================= */
    $('#userTable').DataTable({
      language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json' },
      pageLength: 10,
      order: [[0, 'asc']],
      columnDefs: [{ orderable: false, targets: 7 }]
    });

    /* ================= HELPER: toggle field peminjam ================= */
    /**
     * Tampilkan/sembunyikan field NISN & Kelas sesuai role yang dipilih.
     * @param {string} role         - nilai role yg dipilih ('admin' | 'peminjam')
     * @param {string} prefix       - 'create' atau 'edit'
     */
    function togglePeminjamFields(role, prefix) {
      const isPeminjam = (role === 'peminjam');
      const nisnWrap  = $('#' + prefix + '_nisn_wrap');
      const kelasWrap = $('#' + prefix + '_kelas_wrap');

      if (isPeminjam) {
        nisnWrap.removeClass('d-none');
        kelasWrap.removeClass('d-none');
        // Tambahkan required secara dinamis
        $('#' + (prefix === 'create' ? 'NISN' : 'edit_NISN')).prop('required', true);
        $('#' + (prefix === 'create' ? 'kelas_jurusan' : 'edit_kelas_jurusan')).prop('required', true);
      } else {
        nisnWrap.addClass('d-none');
        kelasWrap.addClass('d-none');
        // Hapus required & kosongkan agar tidak ikut divalidasi
        $('#' + (prefix === 'create' ? 'NISN' : 'edit_NISN')).prop('required', false).val('');
        $('#' + (prefix === 'create' ? 'kelas_jurusan' : 'edit_kelas_jurusan')).prop('required', false).val('');
      }
    }

    /* ---- Event: role CREATE berubah ---- */
    $('#role').on('change', function () {
      togglePeminjamFields($(this).val(), 'create');
    });

    /* ---- Event: role EDIT berubah ---- */
    $('#edit_role').on('change', function () {
      togglePeminjamFields($(this).val(), 'edit');
    });

    /* ---- Jalankan sekali saat load halaman (untuk kasus old() setelah validasi gagal) ---- */
    togglePeminjamFields($('#role').val(), 'create');

    /* ================= EDIT BUTTON ================= */
    $(document).on('click', '.editBtn', function () {
      // Reset form dulu
      $('#editUserForm')[0].reset();
      $('#editUserForm .is-valid, #editUserForm .is-invalid').removeClass('is-valid is-invalid');

      const role = $(this).data('role');

      $('#edit_name').val($(this).data('name'));
      $('#edit_email').val($(this).data('email'));
      $('#edit_role').val(role);
      $('#edit_NISN').val($(this).data('nisn') || '');
      $('#edit_kelas_jurusan').val($(this).data('kelas') || '');

      // Kosongkan field password
      $('#edit_password').val('').attr('type', 'password');
      $('#edit_password_confirmation').val('').attr('type', 'password');
      $('#toggleEditPassword, #toggleEditPasswordConfirm').attr('icon', 'solar:eye-outline');

      // Tampilkan/sembunyikan field peminjam sesuai role
      togglePeminjamFields(role, 'edit');

      $('#editUserForm').attr('action', '/admin/users/' + $(this).data('id'));
      $('#editUserModal').modal('show');
    });

    /* ================= TOGGLE PASSWORD ================= */
    function togglePassword(btn, input) {
      const type = input.attr('type') === 'password' ? 'text' : 'password';
      input.attr('type', type);
      btn.attr('icon', type === 'text' ? 'solar:eye-closed-outline' : 'solar:eye-outline');
    }

    $('#togglePassword').click(() => togglePassword($('#togglePassword'), $('#password')));
    $('#togglePasswordConfirm').click(() => togglePassword($('#togglePasswordConfirm'), $('#password_confirmation')));
    $('#toggleEditPassword').click(() => togglePassword($('#toggleEditPassword'), $('#edit_password')));
    $('#toggleEditPasswordConfirm').click(() => togglePassword($('#toggleEditPasswordConfirm'), $('#edit_password_confirmation')));

    /* ================= PASSWORD CONFIRMATION ================= */
    function checkPassword(pw, confirm) {
      if (!confirm.val()) return;
      confirm.toggleClass('is-valid', pw.val() === confirm.val());
      confirm.toggleClass('is-invalid', pw.val() !== confirm.val());
    }

    $('#password, #password_confirmation').on('keyup', () =>
      checkPassword($('#password'), $('#password_confirmation'))
    );

    $('#edit_password, #edit_password_confirmation').on('keyup', () =>
      checkPassword($('#edit_password'), $('#edit_password_confirmation'))
    );

    /* ================= RESET MODAL CREATE ================= */
    $('#createUserModal').on('show.bs.modal', function () {
      const form = $(this).find('form')[0];
      form.reset();
      $(this).find('.is-valid, .is-invalid').removeClass('is-valid is-invalid');
      $(this).find('iconify-icon.password-toggle-icon').attr('icon', 'solar:eye-outline');
      // Sembunyikan field peminjam saat modal baru dibuka (role belum dipilih)
      togglePeminjamFields('', 'create');
      setTimeout(function () {
        $('#email').val('');
        $('#password').val('').attr('type', 'password');
        $('#password_confirmation').val('').attr('type', 'password');
      }, 50);
    });

    $('#createUserModal, #editUserModal').on('hidden.bs.modal', function () {
      $(this).find('form')[0].reset();
      $(this).find('.is-valid, .is-invalid').removeClass('is-valid is-invalid');
      $(this).find('iconify-icon.password-toggle-icon').attr('icon', 'solar:eye-outline');
    });

    /* ================= DELETE CONFIRM ================= */
    $(document).on('submit', '.deleteForm', function (e) {
      e.preventDefault();
      const form = this;
      Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: 'Data user yang dihapus tidak dapat dikembalikan!',
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