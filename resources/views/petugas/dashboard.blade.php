<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Petugas</title>
    <link rel="stylesheet" href="{{ asset('template/css/styles.min.css') }}" />
</head>

<body>
<div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6"
     data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">

    <!-- Navbar -->
    <x-navbar />

    <!-- Sidebar -->
    <x-sidebar />

    <!-- Body -->
    <div class="body-wrapper pt-4">
        <div class="container-fluid">

            <!-- Page Title -->
            <div class="row mb-4">
                <div class="col-12">
                    <h4 class="fw-bold">Dashboard</h4>
                    <p class="text-muted">Ringkasan Data Sistem Peminjaman Alat</p>
                </div>
            </div>

            <!-- Card Statistik -->
            <div class="row">

                <!-- Menunggu Persetujuan -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="round-48 d-flex align-items-center justify-content-center bg-warning-subtle">
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
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="round-48 d-flex align-items-center justify-content-center bg-primary-subtle">
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

                <!-- Terlambat -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="round-48 d-flex align-items-center justify-content-center bg-danger-subtle">
                                    <i class="ti ti-alert-triangle fs-6 text-danger"></i>
                                </div>
                                <div class="ms-3">
                                    <h3 class="mb-0 fw-bold">{{ $terlambat }}</h3>
                                    <span class="text-muted">Terlambat</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Peminjaman -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="round-48 d-flex align-items-center justify-content-center bg-success-subtle">
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