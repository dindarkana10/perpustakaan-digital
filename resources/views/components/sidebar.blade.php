<style>
/* Sidebar utama */
.left-sidebar {
  position: fixed;
  top: 0;
  left: 0;
  width: 260px;
  height: 100vh;
  background: #ffffff;
  border-right: 1px solid #e5e7eb;
  /* Perbaikan: Pastikan overflow hanya pada sumbu Y dan handle tinggi dengan benar */
  overflow-y: auto; 
  overflow-x: hidden;
  transition: all 0.3s ease;
  z-index: 1000;
  display: flex;
  flex-direction: column;
}

/* Scrollbar custom agar lebih modern */
.left-sidebar::-webkit-scrollbar {
  width: 5px;
}
.left-sidebar::-webkit-scrollbar-track {
  background: transparent;
}
.left-sidebar::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 10px;
}
.left-sidebar::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

/* Nav styling */
.sidebar-nav {
  padding: 15px;
  /* Perbaikan: Tambahkan padding bawah ekstra agar item terakhir (Log Aktivitas) 
     tidak mepet ke bawah layar dan mudah diklik */
  padding-bottom: 50px; 
  flex: 1;
}

#sidebarnav {
  list-style: none;
  padding: 0;
  margin: 0;
}

.sidebar-item {
  margin-bottom: 5px;
  list-style: none;
}

.sidebar-link {
  display: flex;
  align-items: center;
  padding: 10px 12px;
  border-radius: 10px;
  color: #374151;
  text-decoration: none;
  transition: all 0.2s ease;
  font-size: 14px;
}

.sidebar-link i {
  font-size: 18px;
  margin-right: 10px;
  flex-shrink: 0; /* Mencegah icon mengecil jika teks panjang */
}

/* Hover effect */
.sidebar-link:hover {
  background: #f3f4f6;
  color: #2563eb;
}

/* Active menu */
.sidebar-item.active .sidebar-link {
  background: #2563eb;
  color: #ffffff;
  box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);
}

/* Section title */
.nav-small-cap {
  font-size: 11px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  font-weight: 700;
  color: #9ca3af;
  margin-top: 20px;
  margin-bottom: 8px;
  padding-left: 10px;
  display: flex;
  align-items: center;
}

.nav-small-cap-icon {
  margin-right: 8px;
}

/* Divider */
.sidebar-divider {
  display: block;
  height: 1px;
  background: #f1f5f9;
  margin: 15px 10px;
}
</style>

<aside class="left-sidebar">
  <div class="sidebar-nav">
    <ul id="sidebarnav">

      <li class="nav-small-cap">
        <iconify-icon icon="solar:home-2-linear" class="nav-small-cap-icon fs-4"></iconify-icon>
        <span>Main</span>
      </li>

      <li class="sidebar-item {{ request()->routeIs('*dashboard') ? 'active' : '' }}">
        <a class="sidebar-link" href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('peminjam.dashboard') }}">
          <i class="ti ti-smart-home"></i>
          <span>Dashboard</span>
        </a>
      </li>

      <li class="sidebar-divider"></li>

      @if(auth()->user()->role === 'admin')

      <li class="nav-small-cap">
        <iconify-icon icon="solar:database-linear" class="nav-small-cap-icon fs-4"></iconify-icon>
        <span>Master Data</span>
      </li>

      <li class="sidebar-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
        <a class="sidebar-link" href="{{ route('users.index') }}">
          <i class="ti ti-users"></i><span>Manajemen User</span>
        </a>
      </li>

      <li class="sidebar-item {{ request()->routeIs('kategoris.*') ? 'active' : '' }}">
        <a class="sidebar-link" href="{{ route('kategoris.index') }}">
          <i class="ti ti-category-2"></i><span>Kategori Buku</span>
        </a>
      </li>

      <li class="sidebar-item {{ request()->routeIs('bukus.*') ? 'active' : '' }}">
        <a class="sidebar-link" href="{{ route('bukus.index') }}">
          <i class="ti ti-book"></i><span>Data Buku</span>
        </a>
      </li>

      <li class="sidebar-divider"></li>

      <li class="nav-small-cap">
        <iconify-icon icon="solar:clipboard-text-linear" class="nav-small-cap-icon fs-4"></iconify-icon>
        <span>Transaksi</span>
      </li>

      <li class="sidebar-item {{ request()->routeIs('admin.peminjaman.*') ? 'active' : '' }}">
        <a class="sidebar-link" href="{{ route('admin.peminjaman.index') }}">
          <i class="ti ti-clipboard-list"></i><span>Peminjaman</span>
        </a>
      </li>

      <li class="sidebar-item {{ request()->routeIs('admin.pengembalian.*') && !request()->routeIs('admin.pengembalian.riwayat') ? 'active' : '' }}">
        <a class="sidebar-link" href="{{ route('admin.pengembalian.index') }}">
          <i class="ti ti-clipboard-check"></i><span>Pengembalian</span>
        </a>
      </li>

      <li class="sidebar-item {{ request()->routeIs('admin.pengembalian.riwayat') ? 'active' : '' }}">
        <a class="sidebar-link" href="{{ route('admin.pengembalian.riwayat') }}">
          <i class="ti ti-history"></i><span>Riwayat Pengembalian</span>
        </a>
      </li>

      <li class="sidebar-divider"></li>

      <li class="nav-small-cap">
        <iconify-icon icon="solar:file-text-linear" class="nav-small-cap-icon fs-4"></iconify-icon>
        <span>Analisis & Log</span>
      </li>

      <li class="sidebar-item {{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}">
        <a class="sidebar-link" href="{{ route('admin.laporan.index') }}">
          <i class="ti ti-file-analytics"></i><span>Laporan Peminjaman</span>
        </a>
      </li>

      <li class="sidebar-item {{ request()->routeIs('admin.log-aktivitas.*') ? 'active' : '' }}">
        <a class="sidebar-link" href="{{ route('admin.log-aktivitas.index') }}">
          <i class="ti ti-list-details"></i><span>Log Aktivitas</span>
        </a>
      </li>

      @endif

      @if(auth()->user()->role === 'peminjam')

      <li class="nav-small-cap">
        <iconify-icon icon="solar:box-linear" class="nav-small-cap-icon fs-4"></iconify-icon>
        <span>Perpustakaan</span>
      </li>

      <li class="sidebar-item {{ request()->routeIs('buku.*') ? 'active' : '' }}">
        <a class="sidebar-link" href="{{ route('buku.index') }}">
          <i class="ti ti-book"></i><span>Cari Buku</span>
        </a>
      </li>

      <li class="sidebar-item {{ request()->routeIs('peminjam.peminjaman.index') ? 'active' : '' }}">
        <a class="sidebar-link" href="{{ route('peminjam.peminjaman.index') }}">
          <i class="ti ti-clipboard-list"></i><span>Peminjaman</span>
        </a>
      </li>

      <li class="sidebar-item {{ request()->routeIs('pengembalian.index') ? 'active' : '' }}">
        <a class="sidebar-link" href="{{ route('pengembalian.index') }}">
          <i class="ti ti-arrow-back-up"></i><span>Pengembalian</span>
        </a>
      </li>

      @endif

    </ul>
  </div>
</aside>