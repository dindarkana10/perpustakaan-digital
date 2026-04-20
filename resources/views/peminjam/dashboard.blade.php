<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Peminjam</title>
    <link rel="stylesheet" href="{{ asset('template/css/styles.min.css') }}" />
    <style>
        .card-stats {
            transition: transform 0.2s ease-in-out;
        }
        .card-stats:hover {
            transform: translateY(-5px);
        }
        .welcome-bg {
            background: linear-gradient(45deg, #5d87ff, #ecf2ff);
        }
    </style>
</head>

<body>
<div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6"
     data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">

    <!-- Navbar -->
    <x-navbar />

    <!-- Sidebar -->
    <x-sidebar />

    <div class="body-wrapper pt-4">
        <div class="container-fluid">

            <!-- Welcome Card -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 welcome-bg shadow-sm">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h2 class="fw-bold text-white mb-2">
                                        Selamat Datang, {{ Auth::user()->name ?? 'Peminjam' }}! 👋
                                    </h2>
                                    <p class="text-white opacity-75 mb-0">
                                        Hari ini adalah <strong>{{ date('d F Y') }}</strong>.<br>
                                        Pantau status peminjaman alatmu melalui ringkasan di bawah ini.
                                    </p>
                                </div>
                                <div class="d-none d-lg-block">
                                    <img src="https://bootstrapdemos.adminmart.com/modernize/dist/assets/images/backgrounds/welcome-bg.svg" 
                                         alt="welcome" width="200">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistik Peminjaman -->
            <div class="row">

                <!-- Menunggu Persetujuan -->
                <div class="col-md-4 mb-4">
                    <div class="card card-stats shadow-sm h-100">
                        <div class="card-body text-center">
                            <i class="ti ti-clock fs-8 text-warning mb-2"></i>
                            <h4 class="fw-semibold mb-1 text-warning">{{ $peminjamanMenunggu }}</h4>
                            <p class="text-muted mb-0">Menunggu Persetujuan</p>
                        </div>
                    </div>
                </div>

                <!-- Sedang Dipinjam -->
                <div class="col-md-4 mb-4">
                    <div class="card card-stats shadow-sm h-100">
                        <div class="card-body text-center">
                            <i class="ti ti-loader fs-8 text-primary mb-2"></i>
                            <h4 class="fw-semibold mb-1 text-primary">{{ $peminjamanDipinjam }}</h4>
                            <p class="text-muted mb-0">Sedang Dipinjam</p>
                        </div>
                    </div>
                </div>

                <!-- Sudah Dikembalikan -->
                <div class="col-md-4 mb-4">
                    <div class="card card-stats shadow-sm h-100">
                        <div class="card-body text-center">
                            <i class="ti ti-check fs-8 text-success mb-2"></i>
                            <h4 class="fw-semibold mb-1 text-success">{{ $peminjamanDikembalikan }}</h4>
                            <p class="text-muted mb-0">Sudah Dikembalikan</p>
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
