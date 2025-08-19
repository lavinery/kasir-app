<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="{{ url(auth()->user()->foto ?? '/img/default-user.png') }}" class="img-circle img-profil" alt="User Image">
            </div>
            <div class="pull-left info">
                <p>{{ auth()->user()->name }}</p>
                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>

        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu" data-widget="tree">
            <li class="{{ request()->is('dashboard') ? 'active' : '' }}">
                <a href="{{ route('dashboard') }}">
                    <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                </a>
            </li>

            {{-- MENU UNTUK SEMUA USER --}}
            <li class="header">MASTER</li>
            <li class="{{ request()->is('kategori*') ? 'active' : '' }}">
                <a href="{{ route('kategori.index') }}">
                    <i class="fa fa-cube"></i> <span>Kategori</span>
                </a>
            </li>
            <li class="{{ request()->is('member') && !request()->is('member-stats*') ? 'active' : '' }}">
                <a href="{{ route('member.index') }}">
                    <i class="fa fa-id-card"></i> <span>Member</span>
                </a>
            </li>
            <li class="{{ request()->is('supplier*') ? 'active' : '' }}">
                <a href="{{ route('supplier.index') }}">
                    <i class="fa fa-truck"></i> <span>Supplier</span>
                </a>
            </li>

            {{-- MENU UNTUK ADMIN DAN KASIR --}}
            @if (auth()->user()->level == 1 || auth()->user()->level == 2)
                <li class="{{ request()->is('produk*') ? 'active' : '' }}">
                    <a href="{{ route('produk.index') }}">
                        <i class="fa fa-cubes"></i> <span>Produk</span>
                    </a>
                </li>
                
                {{-- Menu Barang Habis --}}
                <li class="{{ request()->is('barang-habis*') ? 'active' : '' }}">
                    <a href="{{ route('barang_habis.index') }}">
                        <i class="fa fa-exclamation-triangle text-warning"></i>
                        <span>Barang Habis</span>
                        {{-- Badge notifikasi jumlah barang habis --}}
                        <span class="pull-right-container">
                            @php
                                $jumlahBarangHabis = \App\Models\BarangHabis::count();
                            @endphp
                            @if($jumlahBarangHabis > 0)
                                <span class="label label-danger pull-right">{{ $jumlahBarangHabis }}</span>
                            @endif
                        </span>
                    </a>
                </li>
                
                <li class="header">TRANSAKSI</li>
                <li class="{{ request()->is('penjualan*') ? 'active' : '' }}">
                    <a href="{{ route('penjualan.index') }}">
                        <i class="fa fa-shopping-cart"></i> <span>Penjualan</span>
                    </a>
                </li>
                <li class="{{ request()->is('pembelian*') ? 'active' : '' }}">
                    <a href="{{ route('pembelian.index') }}">
                        <i class="fa fa-download"></i> <span>Pembelian</span>
                    </a>
                </li>
                <li class="{{ request()->is('pengeluaran*') ? 'active' : '' }}">
                    <a href="{{ route('pengeluaran.index') }}">
                        <i class="fa fa-money"></i> <span>Pengeluaran</span>
                    </a>
                </li>
                <li class="{{ request()->is('transaksi') && !request()->is('transaksi/baru') ? 'active' : '' }}">
                    <a href="{{ route('transaksi.index') }}">
                        <i class="fa fa-cart-arrow-down"></i> <span>Transaksi Aktif</span>
                    </a>
                </li>
                <li class="{{ request()->is('transaksi/baru') ? 'active' : '' }}">
                    <a href="{{ route('transaksi.baru') }}">
                        <i class="fa fa-cart-plus"></i> <span>Transaksi Baru</span>
                    </a>
                </li>
            @endif

            {{-- MENU KHUSUS ADMIN --}}
            @if (auth()->user()->level == 1)
                <li class="header">REPORT</li>
                <li class="{{ request()->is('laporan') && !request()->is('laporan/kasir*') ? 'active' : '' }}">
                    <a href="{{ route('laporan.index') }}">
                        <i class="fa fa-file-pdf-o"></i> <span>Laporan</span>
                    </a>
                </li>
                <li class="{{ request()->is('laporan/kasir*') ? 'active' : '' }}">
                    <a href="{{ route('laporan.kasir.index') }}">
                        <i class="fa fa-file-text-o"></i> <span>Laporan Kasir</span>
                    </a>
                </li>
                
                <li class="header">SYSTEM</li>
                <li class="{{ request()->is('user*') ? 'active' : '' }}">
                    <a href="{{ route('user.index') }}">
                        <i class="fa fa-users"></i> <span>User</span>
                    </a>
                </li>
                
                {{-- Menu Pengaturan dengan submenu --}}
                <li class="treeview {{ request()->is('setting*') ? 'active' : '' }}">
                    <a href="#">
                        <i class="fa fa-cogs"></i> <span>Pengaturan</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        <li class="{{ request()->is('setting') && !request()->is('setting/favorites') ? 'active' : '' }}">
                            <a href="{{ route('setting.index') }}">
                                <i class="fa fa-circle-o"></i> Pengaturan Umum
                            </a>
                        </li>
                        <li class="{{ request()->is('setting/favorites*') ? 'active' : '' }}">
                            <a href="{{ route('setting.favorites') }}">
                                <i class="fa fa-star"></i> Favorit Produk
                                {{-- Badge untuk jumlah favorit aktif --}}
                                <span class="pull-right-container">
                                    @php
                                        $jumlahFavorit = \App\Models\FavoriteProduct::where('is_active', true)->count();
                                    @endphp
                                    @if($jumlahFavorit > 0)
                                        <span class="label label-primary pull-right">{{ $jumlahFavorit }}</span>
                                    @endif
                                </span>
                            </a>
                        </li>
                    </ul>
                </li>
            @endif

            {{-- MENU KHUSUS KASIR --}}
            @if (auth()->user()->level == 2)
                <li class="header">REPORT</li>
                <li class="{{ request()->is('laporan/kasir*') ? 'active' : '' }}">
                    <a href="{{ route('laporan.kasir.index') }}">
                        <i class="fa fa-file-text-o"></i> <span>Laporan Kasir</span>
                    </a>
                </li>
            @endif
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>