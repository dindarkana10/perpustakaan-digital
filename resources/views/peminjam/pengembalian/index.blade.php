<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Riwayat Pengembalian - Peminjam</title>
    <link rel="stylesheet" href="{{ asset('template/css/styles.min.css') }}" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
</head>

<body>
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">
        
        <x-navbar></x-navbar>
        <x-sidebar></x-sidebar>

        <div class="body-wrapper">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="card-title fw-semibold">Riwayat Pengembalian Saya</h5>
                            <a href="{{ route('pengembalian.create') }}" class="btn btn-primary d-flex align-items-center gap-1">
                                <iconify-icon icon="solar:add-circle-outline" width="20"></iconify-icon>
                                Ajukan Pengembalian
                            </a>
                        </div>

                        {{-- Alert Messages --}}
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table id="pengembalianTable" class="table table-bordered table-striped align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50">No</th>
                                        <th>Buku</th>
                                        <th>Tgl Kembali Aktual</th>
                                        <th>Total Denda</th>
                                        <th>Status Pengembalian</th>
                                        <th>Pembayaran</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pengembalians as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                @foreach($item->peminjaman->details as $detail)
                                                    <div class="d-flex align-items-center gap-2 mb-1">
                                                        <img src="{{ asset('storage/bukus/' . $detail->buku->gambar) }}" 
                                                            width="40" height="60" 
                                                            style="object-fit: cover; border-radius: 5px;">
                                                        
                                                        <span class="badge bg-light text-dark">{{ $detail->buku->judul_buku }} ({{ $detail->jumlah }})</span>
                                                    </div>
                                                @endforeach
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($item->tanggal_kembali_aktual)->format('d/m/Y') }}</td>
                                            <td>
                                                <span class="text-{{ $item->total_denda > 0 ? 'danger' : 'success' }} fw-semibold">
                                                    Rp {{ number_format($item->total_denda, 0, ',', '.') }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($item->status_pengembalian == 'diajukan')
                                                    <span class="badge bg-warning">Diajukan</span>
                                                @else
                                                    <span class="badge bg-success">Dikonfirmasi</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($item->status_pengembalian === 'diajukan')
                                                <span class="text-muted">—</span>
                                                @elseif($item->status_pembayaran === 'lunas')
                                                <span class="badge bg-success">Lunas</span>
                                                @elseif($item->status_pembayaran === 'tidak_ada_denda')
                                                <span class="badge bg-light text-dark border">Tidak Ada Denda</span>
                                                @else
                                                <span class="badge bg-danger">Belum Lunas</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-1">
                                                    <a href="{{ route('pengembalian.show', $item->id) }}" class="btn btn-sm btn-outline-info" title="Detail">
                                                        <iconify-icon icon="solar:eye-outline" width="18"></iconify-icon>
                                                    </a>
                                                    @if($item->status_pengembalian == 'diajukan')
                                                        <a href="{{ route('pengembalian.edit', $item->id) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                                            <iconify-icon icon="solar:pen-new-square-outline" width="18"></iconify-icon>
                                                        </a>
                                                        <form action="{{ route('pengembalian.destroy', $item->id) }}" method="POST" class="d-inline delete-form">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="btn btn-sm btn-outline-danger btn-delete" title="Batal">
                                                                <iconify-icon icon="solar:trash-bin-trash-outline" width="18"></iconify-icon>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('template/libs/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('template/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('template/js/app.min.js') }}"></script>
    
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function () {
            /* Init DataTable */
            $('#pengembalianTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Indonesian.json"
                }
            });

            /* Delete Confirmation */
            $('.btn-delete').on('click', function (e) {
                e.preventDefault();
                let form = $(this).closest('form');
                
                Swal.fire({
                    title: 'Batalkan Pengajuan?',
                    text: "Data pengajuan pengembalian akan dihapus permanen.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Batalkan!',
                    cancelButtonText: 'Tutup'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            /* Auto Hide Alert */
            setTimeout(() => {
                $('.alert').fadeOut();
            }, 3000);
        });
    </script>
</body>

</html>