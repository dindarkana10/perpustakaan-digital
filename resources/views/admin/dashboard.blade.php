<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Admin - Sistem Peminjaman</title>
    <link rel="stylesheet" href="{{ asset('template/css/styles.min.css') }}" />
    <style>
        .card-stats {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        .card-stats:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.10) !important;
        }
        .welcome-bg {
            background: linear-gradient(45deg, #5d87ff, #ecf2ff);
        }
        .icon-box {
            width: 52px;
            height: 52px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            flex-shrink: 0;
        }
    </style>
</head>

<body>
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">

        <x-navbar />
        <x-sidebar />

        <div class="body-wrapper">
            <div class="container-fluid">

                {{-- Welcome Card --}}
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border-0 welcome-bg shadow-sm">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h2 class="fw-bold text-white mb-2">Selamat Datang, {{ Auth::user()->name ?? 'Administrator' }}! 👋</h2>
                                        <p class="text-white opacity-75 mb-0">
                                            Hari ini adalah <strong>{{ date('d F Y') }}</strong>. <br>
                                            Pantau aktivitas peminjaman buku dengan mudah melalui ringkasan di bawah ini.
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

                {{-- Row 1: Total Statistik --}}
                <div class="row">
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card card-stats shadow-sm h-100 border-0">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="icon-box bg-primary-subtle">
                                        <i class="ti ti-users fs-6 text-primary"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h3 class="mb-0 fw-bold">{{ $totalUser }}</h3>
                                        <span class="text-muted fs-3">Total User Terdaftar</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card card-stats shadow-sm h-100 border-0">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="icon-box bg-warning-subtle">
                                        <i class="ti ti-book fs-6 text-warning"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h3 class="mb-0 fw-bold">{{ $totalBuku }}</h3>
                                        <span class="text-muted fs-3">Total Koleksi Buku</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card card-stats shadow-sm h-100 border-0">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="icon-box bg-info-subtle">
                                        <i class="ti ti-clipboard-list fs-6 text-info"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h3 class="mb-0 fw-bold">{{ $totalPeminjaman }}</h3>
                                        <span class="text-muted fs-3">Total Peminjaman</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Row 2: Status Peminjaman --}}
                    <div class="row">
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card card-stats shadow-sm h-100 border-0">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="icon-box bg-warning-subtle">
                                        <i class="ti ti-hourglass fs-6 text-warning"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h3 class="mb-0 fw-bold">{{ $peminjamanMenunggu }}</h3>
                                        <span class="text-muted fs-3">Menunggu Persetujuan</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card card-stats shadow-sm h-100 border-0">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="icon-box bg-primary-subtle">
                                        <i class="ti ti-clock-hour-4 fs-6 text-primary"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h3 class="mb-0 fw-bold">{{ $peminjamanDipinjam }}</h3>
                                        <span class="text-muted fs-3">Sedang Dipinjam</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card card-stats shadow-sm h-100 border-0">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="icon-box bg-success-subtle">
                                        <i class="ti ti-circle-check fs-6 text-success"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h3 class="mb-0 fw-bold">{{ $peminjamanDikembalikan }}</h3>
                                        <span class="text-muted fs-3">Sudah Dikembalikan</span>
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