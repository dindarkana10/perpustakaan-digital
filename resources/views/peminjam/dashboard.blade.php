<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Peminjam</title>
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
                                    <h3 class="mb-0 fw-bold"></h3>
                                    <span class="text-muted">judul</span>
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