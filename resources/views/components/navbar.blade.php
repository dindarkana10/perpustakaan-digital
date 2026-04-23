<div class="app-topstrip custom-navbar px-3 px-md-4 d-flex align-items-center justify-content-between">

    <div class="brand-wrapper">
        <h1 class="text-white fw-bold mb-0 fs-5 d-flex align-items-center gap-2">
            <i class="ti ti-books d-none d-md-block"></i>
            <span class="brand-text">   Perpustakaan Digital SMKN 1 Ciomas</span>
        </h1>
        <small class="text-white-50 d-none d-sm-block">
            Sistem Informasi Perpustakaan Digital
        </small>
    </div>

    <div class="d-flex align-items-center gap-2 gap-md-3">

        <div class="user-profile-box d-flex align-items-center gap-2 px-2 px-md-3 py-1 py-md-2">
            <div class="avatar-circle">
                <i class="ti ti-user fs-5"></i>
            </div>
            <div class="user-details d-none d-md-block">
                <p class="text-white fw-semibold mb-0 lh-1">{{ auth()->user()->name }}</p>
                <small class="text-white-50" style="font-size: 0.7rem;">Online</small>
            </div>
        </div>

        <div class="dropdown">
            <button class="btn btn-logout-toggle shadow-sm" 
                    type="button" 
                    data-bs-toggle="dropdown" 
                    aria-expanded="false">
                <i class="ti ti-power text-danger fs-5"></i>
            </button>

            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2 p-2 animation-fade">
                <li class="px-3 py-2 border-bottom mb-2 d-md-none">
                    <span class="fw-bold d-block text-dark">{{ auth()->user()->name }}</span>
                    <small class="text-muted">Username</small>
                </li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item rounded-2 py-2 text-danger fw-bold">
                            <i class="ti ti-logout me-2"></i> Keluar Aplikasi
                        </button>
                    </form>
                </li>
            </ul>
        </div>

    </div>
</div>

<style>
/* KONFIGURASI NAVBAR UTAMA */
.custom-navbar {
    background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
    height: 75px;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1050;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

/* USER PROFILE BOX (Glassmorphism) */
.user-profile-box {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(8px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 50px; /* Capsule shape */
    transition: all 0.3s ease;
}

.avatar-circle {
    width: 35px;
    height: 35px;
    background: white;
    color: #224abe;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

/* LOGOUT TOGGLE */
.btn-logout-toggle {
    background: white;
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.btn-logout-toggle:hover {
    background: #fff5f5;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.15);
}

/* DROPDOWN STYLING */
.dropdown-menu {
    border-radius: 15px;
    min-width: 200px;
}

.dropdown-item {
    transition: all 0.2s;
}

.dropdown-item:hover {
    background-color: #fff1f0;
    padding-left: 1.5rem;
}

/* RESPONSIVE ADJUSTMENTS */
@media (max-width: 576px) {
    .custom-navbar {
        height: 65px;
    }
    .brand-text {
        font-size: 1.1rem;
    }
    .user-profile-box {
        padding: 5px !important;
    }
}

/* ANIMATION */
.animation-fade {
    animation: fadeInUp 0.3s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* PENTING: Tambahkan margin-top pada pembungkus konten utama Anda 
   agar tidak tertutup navbar (sekitar 80px-90px) */
</style>