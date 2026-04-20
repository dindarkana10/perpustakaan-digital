<aside class="left-sidebar">
  <div>

    <nav class="sidebar-nav">
      <ul id="sidebarnav">

        <!-- HOME (SEMUA ROLE) -->
        <li class="nav-small-cap">
          <iconify-icon icon="solar:menu-dots-linear"
            class="nav-small-cap-icon fs-4"></iconify-icon>
          <span class="hide-menu">Home</span>
        </li>

        <li class="sidebar-item">
          <a class="sidebar-link" href="
            @if(auth()->user()->role === 'admin')
                {{ route('admin.dashboard') }}
            @elseif(auth()->user()->role === 'petugas')
                {{ route('petugas.dashboard') }}
            @else
                {{ route('peminjam.dashboard') }}
            @endif
          ">
            <i class="ti ti-layout-dashboard"></i>
            <span class="hide-menu">Dashboard</span>
          </a>
        </li>
        <li>
          <span class="sidebar-divider lg"></span>
        </li>

        {{-- ================= ADMIN ================= --}}
        @if(auth()->user()->role === 'admin')
        <li class="nav-small-cap">
          <iconify-icon icon="solar:menu-dots-linear"
            class="nav-small-cap-icon fs-4"></iconify-icon>
          <span class="hide-menu">Kelola Data</span>
        </li>

        <li class="sidebar-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
          <a class="sidebar-link" href="{{ route( 'users.index' )}}">
            <i class="ti ti-users"></i>
            <span class="hide-menu">Data User</span>
          </a>
        </li>

        <li class="sidebar-item {{ request()->routeIs('kategoris.*') ? 'active' : '' }}">
          <a class="sidebar-link" href="{{ route( 'kategoris.index' )}}">
            <i class="ti ti-category"></i>
            <span class="hide-menu">Kategori Alat</span>
          </a>
        </li>

        <li class="sidebar-item {{ request()->routeIs('alats.*') ? 'active' : '' }}">
          <a class="sidebar-link" href="{{ route( 'alats.index' )}}">
            <i class="ti ti-tool"></i>
            <span class="hide-menu">Data Alat</span>
          </a>
        </li>

        <li class="sidebar-item {{ request()->routeIs('peminjamen.*') ? 'active' : '' }} ">
          <a class="sidebar-link" href="{{ route( 'peminjaman.index' )}}">
            <i class="ti ti-clipboard-list"></i>
            <span class="hide-menu">Data Peminjaman</span>
          </a>
        </li>

        <li class="sidebar-item">
          <a class="sidebar-link" href="">
            <i class="ti ti-clipboard-check"></i>
            <span class="hide-menu">Pengembalian</span>
          </a>
        </li>

        <li class="sidebar-item">
          <a class="sidebar-link" href="">
            <i class="ti ti-history"></i>
            <span class="hide-menu">Log Aktivitas</span>
          </a>
        </li>
        @endif


        {{-- ================= PETUGAS ================= --}}
        @if(auth()->user()->role === 'petugas')
        <li class="nav-small-cap">
          <iconify-icon icon="solar:menu-dots-linear"
            class="nav-small-cap-icon fs-4"></iconify-icon>
          <span class="hide-menu">Transaksi</span>
        </li>

        <li class="sidebar-item {{ request()->routeIs('peminjaman.*') ? 'active' : '' }}">
          <a class="sidebar-link" href="{{ route( 'petugas.peminjaman.index' )}}">
            <i class="ti ti-checklist"></i>
            <span class="hide-menu">Persetujuan Peminjaman</span>
          </a>
        </li>

        <li class="sidebar-item">
          <a class="sidebar-link" href="">
            <i class="ti ti-eye"></i>
            <span class="hide-menu">Pantau Pengembalian</span>
          </a>
        </li>

        <li class="sidebar-item">
          <a class="sidebar-link" href="">
            <i class="ti ti-file-text"></i>
            <span class="hide-menu">Cetak Laporan</span>
          </a>
        </li>
        @endif


        {{-- ================= PEMINJAM ================= --}}
        @if(auth()->user()->role === 'peminjam')
        <li class="nav-small-cap">
          <iconify-icon icon="solar:menu-dots-linear"
            class="nav-small-cap-icon fs-4"></iconify-icon>
          <span class="hide-menu">Peminjaman</span>
        </li>

        <li class="sidebar-item {{ request()->routeIs('alat.*') ? 'active' : '' }}">
          <a class="sidebar-link" href="{{ route( 'alat.index' )}}">
            <i class="ti ti-list"></i>
            <span class="hide-menu">Daftar Alat</span>
          </a>
        </li>

        <li class="sidebar-item {{ request()->is('peminjaman/create*') ? 'active' : '' }}">
          <a class="sidebar-link" href="{{ route( 'peminjaman.create' )}}">
            <i class="ti ti-pencil-plus"></i>
            <span class="hide-menu">Ajukan Peminjaman</span>
          </a>
        </li>

        <li class="sidebar-item {{ request()->routeIs('peminjaman.index') ? 'active' : '' }}">
          <a class="sidebar-link" href="{{ route( 'peminjaman.index' )}}">
            <i class="ti ti-history"></i>
            <span class="hide-menu">Riwayat Peminjaman</span>
          </a>
        </li>

        {{-- <li class="sidebar-item">
          <a class="sidebar-link" href="">
            <i class="ti ti-rotate"></i>
            <span class="hide-menu">Pengembalian Alat</span>
          </a>
        </li> --}}
        @endif

      </ul>
    </nav>

  </div>
</aside>
