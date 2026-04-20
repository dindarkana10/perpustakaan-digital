<div class="app-topstrip custom-navbar px-4 d-flex align-items-center justify-content-between">

    <!-- JUDUL -->
    <div>
        <h1 class="text-white fw-bold mb-0 fs-5">
            Sistem Manajemen Peminjaman Alat
        </h1>
        <small class="text-white-50">
            Kelola peminjaman & pengembalian dengan mudah
        </small>
    </div>

    <!-- USER -->
    <div class="d-flex align-items-center gap-3">

        <!-- NAMA -->
        <div class="user-info d-flex align-items-center gap-2 px-3 py-2">
            <div class="avatar">
                <i class="ti ti-user"></i>
            </div>
            <span class="text-white fw-semibold">
                {{ auth()->user()->name }}
            </span>
        </div>

        <!-- LOGOUT ICON -->
        <div class="dropdown">
            <a class="btn btn-light btn-sm logout-btn"
               href="#"
               data-bs-toggle="dropdown">
                <i class="ti ti-logout text-danger"></i>
            </a>

            <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="dropdown-item text-danger fw-semibold">
                            <i class="ti ti-logout me-2"></i> Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>

    </div>
</div>

<style>
.custom-navbar {
    background: linear-gradient(90deg, #5d87ff, #224abe);
    height: 70px;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1000;
}

/* USER BOX */
.user-info {
    background: rgba(255,255,255,0.1);
    border-radius: 12px;
}

/* AVATAR */
.avatar {
    width: 32px;
    height: 32px;
    background: white;
    color: #224abe;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* LOGOUT BUTTON */
.logout-btn {
    border-radius: 10px;
    transition: 0.2s;
}

.logout-btn:hover {
    background-color: #ecf2ff;
    transform: scale(1.05);
}
</style>
