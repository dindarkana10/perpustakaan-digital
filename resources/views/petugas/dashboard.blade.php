<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Petugas - Sistem Peminjaman</title>
    <link rel="stylesheet" href="{{ asset('template/css/styles.min.css') }}" />

    <style>
        .card-stats {
            transition: transform 0.2s ease-in-out;
        }
        .card-stats:hover {
            transform: translateY(-5px);
        }
        .welcome-bg {
            background: linear-gradient(445deg, #5d87ff, #ecf2ff);
        }
    </style>
</head>

<body>
<div class="page-wrapper" id="main-wrapper"
     data-layout="vertical"
     data-navbarbg="skin6"
     data-sidebartype="full"
     data-sidebar-position="fixed"
     data-header-position="fixed">

    <!-- Navbar -->
    <x-navbar />

    <!-- Sidebar -->
    <x-sidebar />

    <!-- Body -->
    <div class="body-wrapper">
        <div class="container-fluid">

            <!-- ✅ WELCOME SECTION -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 welcome-bg shadow-sm">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h2 class="fw-bold text-white mb-2">
                                        Selamat Datang, {{ Auth::user()->name ?? 'Petugas' }}! 👋
                                    </h2>
                                    <p class="text-white opacity-75 mb-0">
                                        Hari ini adalah <strong>{{ date('d F Y') }}</strong>. <br>
                                        Kelola dan pantau proses peminjaman serta pengembalian alat dengan mudah.
                                    </p>
                                </div>
                                <div class="d-none d-lg-block">
                                    <img src="https://bootstrapdemos.adminmart.com/modernize/dist/assets/images/backgrounds/welcome-bg.svg"
                                         alt="welcome"
                                         width="200">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistik -->
            <div class="row">

                <!-- Menunggu Persetujuan -->
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card card-stats shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="round-48 d-flex align-items-center justify-content-center bg-warning-subtle rounded-3">
                                    <i class="ti ti-clock fs-6 text-warning"></i>
                                </div>
                                <div class="ms-3">
                                    <h3 class="mb-0 fw-bold">{{ $menunggu }}</h3>
                                    <span class="text-muted">Menunggu Persetujuan</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sedang Dipinjam -->
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card card-stats shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="round-48 d-flex align-items-center justify-content-center bg-primary-subtle rounded-3">
                                    <i class="ti ti-package fs-6 text-primary"></i>
                                </div>
                                <div class="ms-3">
                                    <h3 class="mb-0 fw-bold">{{ $dipinjam }}</h3>
                                    <span class="text-muted">Sedang Dipinjam</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Peminjaman -->
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card card-stats shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="round-48 d-flex align-items-center justify-content-center bg-success-subtle rounded-3">
                                    <i class="ti ti-clipboard-list fs-6 text-success"></i>
                                </div>
                                <div class="ms-3">
                                    <h3 class="mb-0 fw-bold">{{ $totalPeminjaman }}</h3>
                                    <span class="text-muted">Total Peminjaman</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4 mb-4">
                    <a href="{{ route('petugas.pengembalian.index') }}" class="text-decoration-none">
                        <div class="card card-stats shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="round-48 d-flex align-items-center justify-content-center bg-info-subtle rounded-3">
                                        <i class="ti ti-arrow-back-up fs-6 text-info"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h3 class="mb-0 fw-bold">{{ $menungguPengembalian }}</h3>
                                        <span class="text-muted">Konfirmasi Pengembalian</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

            </div>

        </div>
    </div>
</div>

<script src="{{ asset('template/libs/jquery/dist/jquery.min.js') }}"></script>
<script src="{{ asset('template/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('template/js/sidebarmenu.js') }}"></script>
<script src="{{ asset('template/js/app.min.js') }}"></script>
</body>
</html>