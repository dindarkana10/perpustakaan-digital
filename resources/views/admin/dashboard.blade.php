<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Admin</title>
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

                <!-- Total User -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="round-48 d-flex align-items-center justify-content-center bg-primary-subtle">
                                    <i class="ti ti-users fs-6 text-primary"></i>
                                </div>
                                <div class="ms-3">
                                    <h3 class="mb-0 fw-bold">{{ $totalUser }}</h3>
                                    <span class="text-muted">Total User</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Alat -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="round-48 d-flex align-items-center justify-content-center bg-warning-subtle">
                                    <i class="ti ti-tool fs-6 text-warning"></i>
                                </div>
                                <div class="ms-3">
                                    <h3 class="mb-0 fw-bold">{{ $totalAlat }}</h3>
                                    <span class="text-muted">Total Alat</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="round-48 d-flex align-items-center justify-content-center bg-info-subtle">
                                    <i class="ti ti-clipboard-list fs-6 text-info"></i>
                                </div>
                                <div class="ms-3">
                                    <h3 class="mb-0 fw-bold">{{ $totalPeminjaman }}</h3>
                                    <span class="text-muted">Total Peminjaman</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="round-48 d-flex align-items-center justify-content-center bg-primary-subtle">
                                    <i class="ti ti-clock fs-6 text-primary"></i>
                                </div>
                                <div class="ms-3">
                                    <h3 class="mb-0 fw-bold">{{ $peminjamanDipinjam }}</h3>
                                    <span class="text-muted">Sedang Dipinjam</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="round-48 d-flex align-items-center justify-content-center bg-success-subtle">
                                    <i class="ti ti-check fs-6 text-success"></i>
                                </div>
                                <div class="ms-3">
                                    <h3 class="mb-0 fw-bold">{{ $peminjamanDikembalikan }}</h3>
                                    <span class="text-muted">Dikembalikan</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="round-48 d-flex align-items-center justify-content-center bg-danger-subtle">
                                    <i class="ti ti-alert-circle fs-6 text-danger"></i>
                                </div>
                                <div class="ms-3">
                                    <h3 class="mb-0 fw-bold">{{ $peminjamanTerlambat }}</h3>
                                    <span class="text-muted">Terlambat</span>
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