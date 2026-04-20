<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Pengajuan Pengembalian</title>
    <link rel="stylesheet" href="{{ asset('template/css/styles.min.css') }}" />
</head>

<body>
<div class="page-wrapper" id="main-wrapper"
     data-layout="vertical"
     data-navbarbg="skin6"
     data-sidebartype="full"
     data-sidebar-position="fixed"
     data-header-position="fixed">

    <x-navbar />
    <x-sidebar />

    <div class="body-wrapper">
        <div class="container-fluid">

            <div class="card">
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title fw-semibold mb-0">
                            Edit Pengajuan Pengembalian
                        </h5>

                        <a href="{{ route('pengembalian.index') }}"
                           class="btn btn-outline-secondary d-flex align-items-center gap-1">
                            <iconify-icon icon="solar:arrow-left-outline" width="18"></iconify-icon>
                            Kembali
                        </a>
                    </div>

                    <form action="{{ route('pengembalian.update', $pengembalian->id) }}"
                          method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Tanggal Pinjam</label>
                                <input type="text"
                                       class="form-control"
                                       value="{{ \Carbon\Carbon::parse($pengembalian->peminjaman->tanggal_pinjam)->format('d/m/Y') }}"
                                       readonly>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Tanggal Kembali Rencana</label>
                                <input type="text"
                                       class="form-control"
                                       value="{{ \Carbon\Carbon::parse($pengembalian->peminjaman->tanggal_kembali_rencana)->format('d/m/Y') }}"
                                       readonly>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">
                                    Tanggal Kembali Aktual
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="date"
                                       name="tanggal_kembali_aktual"
                                       class="form-control @error('tanggal_kembali_aktual') is-invalid @enderror"
                                       value="{{ old('tanggal_kembali_aktual', $pengembalian->tanggal_kembali_aktual) }}"
                                       required>

                                @error('tanggal_kembali_aktual')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>

                        <div class="alert alert-warning d-flex align-items-center gap-2">
                            <iconify-icon icon="solar:info-circle-outline" width="20"></iconify-icon>
                            Perubahan hanya bisa dilakukan selama status masih
                            <strong>Menunggu Validasi</strong>.
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit"
                                    class="btn btn-primary d-flex align-items-center gap-1">
                                <iconify-icon icon="solar:check-circle-bold" width="18"></iconify-icon>
                                Simpan Perubahan
                            </button>

                            <a href="{{ route('pengembalian.index') }}"
                               class="btn btn-outline-secondary d-flex align-items-center gap-1">
                                <iconify-icon icon="solar:close-circle-outline" width="18"></iconify-icon>
                                Batal
                            </a>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

<script src="{{ asset('template/libs/jquery/dist/jquery.min.js') }}"></script>
<script src="{{ asset('template/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('template/js/sidebarmenu.js') }}"></script>
<script src="{{ asset('template/js/app.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
</body>
</html>