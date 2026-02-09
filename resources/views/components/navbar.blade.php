<div class="app-topstrip bg-dark py-3 px-4 w-100 d-lg-flex align-items-center justify-content-between">

    <!-- JUDUL SISTEM -->
    <div class="text-center text-lg-start mb-2 mb-lg-0">
        <h1 class="text-white fw-bold mb-0 fs-4">
            Sistem Manajemen Peminjaman dan Pengembalian Alat
        </h1>
    </div>

    <!-- USER INFO -->
    <div class="d-flex align-items-center gap-3 justify-content-center">

        <span class="badge bg-info text-dark text-uppercase">
            {{ auth()->user()->role }}
        </span>

        <span class="text-white">
            {{ auth()->user()->name }}
        </span>

        <div class="dropdown">
            <a class="btn btn-outline-light btn-sm d-flex align-items-center gap-1"
               href="#"
               id="userDropdown"
               data-bs-toggle="dropdown"
               aria-expanded="false">
                <i class="ti ti-user-circle fs-5"></i>
                <i class="ti ti-chevron-down"></i>
            </a>

            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="dropdown-item text-danger">
                            <i class="ti ti-logout me-2"></i> Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</div>