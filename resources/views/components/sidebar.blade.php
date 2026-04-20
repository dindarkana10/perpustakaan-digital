<aside class="left-sidebar">
  <div>

    <nav class="sidebar-nav">
      <ul id="sidebarnav">

        {{-- ================= GLOBAL ================= --}}
        <li class="nav-small-cap">
          <iconify-icon icon="solar:home-2-linear" class="nav-small-cap-icon fs-4"></iconify-icon>
          <span class="hide-menu">Main</span>
        </li>

        <li class="sidebar-item {{ request()->routeIs('*dashboard') ? 'active' : '' }}">
          <a class="sidebar-link" href="
            @if(auth()->user()->role === 'admin')
              {{ route('admin.dashboard') }}
            @elseif(auth()->user()->role === 'petugas')
              {{ route('petugas.dashboard') }}
            @else
              {{ route('peminjam.dashboard') }}
            @endif
          ">
            <i class="ti ti-smart-home"></i>
            <span class="hide-menu">Dashboard</span>
          </a>
        </li>

        <li>
          <span class="sidebar-divider lg"></span>
        </li>


        {{-- ================= ADMIN ================= --}}
        @if(auth()->user()->role === 'admin')

        {{-- MASTER DATA --}}
        <li class="nav-small-cap">
          <iconify-icon icon="solar:database-linear" class="nav-small-cap-icon fs-4"></iconify-icon>
          <span class="hide-menu">Master Data</span>
        </li>

        <li class="sidebar-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
          <a class="sidebar-link" href="{{ route('users.index') }}">
            <i class="ti ti-users"></i>
            <span class="hide-menu">Manajemen User</span>
          </a>
        </li>

        <li class="sidebar-item {{ request()->routeIs('kategoris.*') ? 'active' : '' }}">
          <a class="sidebar-link" href="{{ route('kategoris.index') }}">
            <i class="ti ti-category-2"></i>
            <span class="hide-menu">Kategori Alat</span>
          </a>
        </li>

        <li class="sidebar-item {{ request()->routeIs('alats.*') ? 'active' : '' }}">
          <a class="sidebar-link" href="{{ route('alats.index') }}">
            <i class="ti ti-tool"></i>
            <span class="hide-menu">Data Alat</span>
          </a>
        </li>

        <li>
          <span class="sidebar-divider"></span>
        </li>

        {{-- TRANSAKSI --}}
        <li class="nav-small-cap">
          <iconify-icon icon="solar:clipboard-text-linear" class="nav-small-cap-icon fs-4"></iconify-icon>
          <span class="hide-menu">Transaksi</span>
        </li>

        <li class="sidebar-item {{ request()->routeIs('admin.peminjaman.*') ? 'active' : '' }}">
          <a class="sidebar-link" href="{{ route('admin.peminjaman.index') }}">
            <i class="ti ti-clipboard-list"></i>
            <span class="hide-menu">Peminjaman</span>
          </a>
        </li>

        <li class="sidebar-item {{ request()->routeIs('admin.pengembalian.*') ? 'active' : '' }}">
          <a class="sidebar-link" href="{{ route('admin.pengembalian.index') }}">
            <i class="ti ti-clipboard-check"></i>
            <span class="hide-menu">Pengembalian</span>
          </a>
        </li>

        <li>
          <span class="sidebar-divider"></span>
        </li>

        {{-- MONITORING --}}
        <li class="nav-small-cap">
          <iconify-icon icon="solar:chart-linear" class="nav-small-cap-icon fs-4"></iconify-icon>
          <span class="hide-menu">Monitoring</span>
        </li>

        <li class="sidebar-item {{ request()->routeIs('admin.log-aktivitas.*') ? 'active' : '' }}">
          <a class="sidebar-link" href="{{ route('admin.log-aktivitas.index') }}">
            <i class="ti ti-history"></i>
            <span class="hide-menu">Log Aktivitas</span>
          </a>
        </li>

        @endif


        {{-- ================= PETUGAS ================= --}}
        @if(auth()->user()->role === 'petugas')

        <li class="nav-small-cap">
          <iconify-icon icon="solar:clipboard-check-linear" class="nav-small-cap-icon fs-4"></iconify-icon>
          <span class="hide-menu">Transaksi</span>
        </li>

        <li class="sidebar-item {{ request()->routeIs('petugas.peminjaman.*') ? 'active' : '' }}">
          <a class="sidebar-link" href="{{ route('petugas.peminjaman.index') }}">
            <i class="ti ti-checklist"></i>
            <span class="hide-menu">Persetujuan</span>
          </a>
        </li>

        <li class="sidebar-item {{ request()->routeIs('petugas.pengembalian.index') ? 'active' : '' }}">
          <a class="sidebar-link" href="{{ route('petugas.pengembalian.index') }}">
            <i class="ti ti-eye-check"></i>
            <span class="hide-menu">Validasi</span>
          </a>
        </li>

        <li class="sidebar-item {{ request()->routeIs('petugas.pengembalian.riwayat') ? 'active' : '' }}">
          <a class="sidebar-link" href="{{ route('petugas.pengembalian.riwayat') }}">
            <i class="ti ti-history"></i>
            <span class="hide-menu">Riwayat</span>
          </a>
        </li>

        <li>
          <span class="sidebar-divider"></span>
        </li>

        <li class="nav-small-cap">
          <iconify-icon icon="solar:document-text-linear" class="nav-small-cap-icon fs-4"></iconify-icon>
          <span class="hide-menu">Laporan</span>
        </li>

        <li class="sidebar-item {{ request()->routeIs('petugas.laporan.*') ? 'active' : '' }}">
          <a class="sidebar-link" href="{{ route('petugas.laporan.index') }}">
            <i class="ti ti-file-export"></i>
            <span class="hide-menu">Cetak Laporan</span>
          </a>
        </li>

        @endif


        {{-- ================= PEMINJAM ================= --}}
        @if(auth()->user()->role === 'peminjam')

        <li class="nav-small-cap">
          <iconify-icon icon="solar:box-linear" class="nav-small-cap-icon fs-4"></iconify-icon>
          <span class="hide-menu">Peminjaman</span>
        </li>

        <li class="sidebar-item {{ request()->routeIs('alat.*') ? 'active' : '' }}">
          <a class="sidebar-link" href="{{ route('alat.index') }}">
            <i class="ti ti-package"></i>
            <span class="hide-menu">Daftar Alat</span>
          </a>
        </li>

        <li class="sidebar-item {{ request()->routeIs('peminjaman.index') ? 'active' : '' }}">
          <a class="sidebar-link" href="{{ route('peminjaman.index') }}">
            <i class="ti ti-package"></i>
            <span class="hide-menu">Peminjaman</span>
          </a>
        </li>

        <li class="sidebar-item {{ request()->routeIs('pengembalian.index') ? 'active' : '' }}">
          <a class="sidebar-link" href="{{ route('pengembalian.index') }}">
            <i class="ti ti-arrow-back-up"></i>
            <span class="hide-menu">Pengembalian</span>
          </a>
        </li>

        @endif

      </ul>
    </nav>

  </div>
</aside>

<style>
  .sidebar {
    position: fixed;
    top: 70px; 
    left: 0;
    width: 250px;
    height: calc(100% - 70px);
    z-index: 900; 
    background: #fff;
}

.main-content {
    margin-top: 70px;
    margin-left: 250px;
    padding: 20px;
}
</style>