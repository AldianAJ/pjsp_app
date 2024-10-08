<div class="vertical-menu">
    <div data-simplebar class="h-100">
        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">

                <!-- Check if the user is authenticated -->
                @if (auth()->check())
                @php
                $user = auth()->user();
                @endphp

                @if ($user->role == 'super')
                <li>
                    <a href="{{ route('dashboard') }}">
                        <i class="bx bx-home-circle"></i>
                        <span key="t-dashboards">Welcome</span>
                    </a>
                </li>
                <li class="menu-title" key="t-apps">Data Master</li>
                <li>
                    <a href="{{ route('super-admin') }}" class="waves-effect">
                        <i class="bx bxs-user-detail"></i>
                        <span key="t-user">User</span>
                    </a>
                </li>
                @endif

                @if ($user->role == 'gdb')
                <li class="menu-title" key="t-menu">Menu</li>
                <li>
                    <a href="{{ route('dashboard') }}">
                        <i class="bx bx-home-circle"></i>
                        <span key="t-dashboards">Welcome</span>
                    </a>
                </li>

                <li class="menu-title" key="t-apps">Laporan</li>

                <li>
                    <a href="{{ route('stok') }}" class="waves-effect">
                        <i class="bx bxs-component"></i>
                        <span key="t-stok-gudang">Stok</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('stok') }}" class="waves-effect">
                        <i class="bx bxs-component"></i>
                        <span key="t-mutasi">Mutasi</span>
                    </a>
                </li>

                <li class="menu-title" key="t-transactions">Form</li>

                <li>
                    <a href="{{ route('pengiriman-gudang-utama') }}" class="waves-effect">
                        <i class="bx bxs-duplicate"></i>
                        <span key="t-gudang">Proses Permintaan</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('penerimaan-supplier') }}" class="waves-effect">
                        <i class="bx bxs-widget"></i>
                        <span key="t-pemesanan">Penerimaan Supplier</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('return-barang') }}" class="waves-effect">
                        <i class="bx bxs-duplicate"></i>
                        <span key="t-return-barang">Return Barang</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('penjualan') }}" class="waves-effect">
                        <i class="bx bxs-widget"></i>
                        <span key="t-penjualan">Surat Jalan</span>
                    </a>
                </li>

                <li class="menu-title" key="t-history-minta">Riwayat</li>
                <li>
                    <a href="{{ route('permintaan-skm.history') }}" class="waves-effect">
                        <i class="bx bx-notepad"></i>
                        <span key="t-history-minta">Permintaan</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('pengiriman-gudang-utama.history') }}" class="waves-effect">
                        <i class="bx bx-task"></i>
                        <span key="t-kirim">Pengiriman</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('penerimaan-barang.history') }}" class=" waves-effect">
                        <i class="bx bx-task"></i>
                        <span key="t-terima">Penerimaan</span>
                    </a>
                </li>
                @endif

                @if ($user->role == 'skm')
                <li class="menu-title" key="t-menu">Menu</li>
                <li>
                    <a href="{{ route('dashboard') }}">
                        <i class="bx bx-home-circle"></i>
                        <span key="t-dashboards">Welcome</span>
                    </a>
                </li>

                {{-- <li class="menu-title" key="t-apps">Laporan</li>
                <li>
                    <a href="{{ route('stok') }}" class="waves-effect">
                        <i class="bx bxs-component"></i>
                        <span key="t-stok-skm">Stok</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('stok') }}" class="waves-effect">
                        <i class="bx bxs-component"></i>
                        <span key="t-mutasi">Mutasi</span>
                    </a>
                </li> --}}

                <li class="menu-title" key="t-transactions">Form</li>
                <li>
                    <a href="{{ route('permintaan-skm') }}" class="waves-effect">
                        <i class="bx bxs-duplicate"></i>
                        <span key="t-minta">Permintaan</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('penerimaan-barang') }}" class="waves-effect">
                        <i class="bx bx-check-square"></i>
                        <span key="t-terima">Terima Barang</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('pengiriman-batangan') }}" class="waves-effect">
                        <i class="bx bxs-duplicate"></i>
                        <span key="t-kirim-btg">Kirim Batangan</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('pengiriman-bjsk') }}" class="waves-effect">
                        <i class="bx bxs-duplicate"></i>
                        <span key="t-kirim-bjsk">Kirim BJSK</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('pengiriman-skm') }}" class="waves-effect">
                        <i class="bx bxs-duplicate"></i>
                        <span key="t-kirim-skm">Kirim ke Mesin</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('kinerja-hari') }}" class="waves-effect">
                        <i class="bx bxs-receipt"></i>
                        <span key="t-kinerja">Target SKM</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('log-produksi') }}" class="waves-effect">
                        <i class="bx bxs-receipt"></i>
                        <span key="t-kinerja">LogBook Produksi</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('closing-mesin') }}" class="waves-effect">
                        <i class="bx bxs-receipt"></i>
                        <span key="t-kinerja">Closing Mesin</span>
                    </a>
                </li>

                <li class="menu-title" key="t-history">Riwayat</li>
                <li>
                    <a href="{{ route('permintaan-skm.history') }}" class="waves-effect">
                        <i class="bx bx-task"></i>
                        <span key="t-history-minta">Permintaan</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('penerimaan-barang.history') }}" class=" waves-effect">
                        <i class="bx bx-task"></i>
                        <span key="t-terima">Penerimaan</span>
                    </a>
                </li>
                @endif

                @if (
                $user->role == 'mkr1' ||
                $user->role == 'mkr2' ||
                $user->role == 'hlp1' ||
                $user->role == 'hlp2' ||
                $user->role == 'hlp3' ||
                $user->role == 'hlp4')
                <li class="menu-title" key="t-menu">Menu</li>
                <li>
                    <a href="{{ route('dashboard') }}">
                        <i class="bx bx-home-circle"></i>
                        <span key="t-dashboards">Welcome</span>
                    </a>
                </li>
                <li class="menu-title" key="t-transactions">Form</li>
                <li>
                    <a href="{{ route('pengiriman-batangan') }}" class="waves-effect">
                        <i class="bx bxs-duplicate"></i>
                        <span key="t-kirim-btg">Kirim Batangan</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('pengiriman-bjsk') }}" class="waves-effect">
                        <i class="bx bxs-duplicate"></i>
                        <span key="t-kirim-bjsk">Kirim BJSK</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('closing-mesin') }}" class="waves-effect">
                        <i class="bx bxs-receipt"></i>
                        <span key="t-kinerja">Closing Mesin</span>
                    </a>
                </li>
                @endif

                @if (
                $user->role == 'pgws1' ||
                $user->role == 'pgws2' ||
                $user->role == 'pgws3')
                <li>
                    <a href="{{ route('dashboard') }}">
                        <i class="bx bx-home-circle"></i>
                        <span key="t-dashboards">Welcome</span>
                    </a>
                </li>
                <li class="menu-title" key="t-form">Form</li>
                <li>
                    <a href="{{ route('log-produksi') }}" class="waves-effect">
                        <i class="bx bxs-receipt"></i>
                        <span key="t-kinerja">LogBook Produksi</span>
                    </a>
                </li>
                @endif

                @if ($user->role == 'gdb1' || $user->role == 'off1')
                <li>
                    <a href="{{ route('dashboard') }}">
                        <i class="bx bx-home-circle"></i>
                        <span key="t-dashboards">Welcome</span>
                    </a>
                </li>
                <li class="menu-title" key="t-form">Form</li>
                <li>
                    <a href="{{ route('penerimaan-supplier') }}" class="waves-effect">
                        <i class="bx bxs-widget"></i>
                        <span key="t-pemesanan">Penerimaan Supplier</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('pengiriman-gudang-utama') }}" class="waves-effect">
                        <i class="bx bxs-duplicate"></i>
                        <span key="t-gudang">Proses Permintaan</span>
                    </a>
                </li>
                <li class="menu-title" key="t-history-minta">Riwayat</li>
                <li>
                    <a href="{{ route('permintaan-skm.history') }}" class="waves-effect">
                        <i class="bx bx-notepad"></i>
                        <span key="t-history-minta">Permintaan</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('pengiriman-gudang-utama.history') }}" class="waves-effect">
                        <i class="bx bx-task"></i>
                        <span key="t-kirim">Pengiriman</span>
                    </a>
                </li>
                @endif

                @if ($user->role == 'gdb2')
                <li>
                    <a href="{{ route('dashboard') }}">
                        <i class="bx bx-home-circle"></i>
                        <span key="t-dashboards">Welcome</span>
                    </a>
                </li>
                <li class="menu-title" key="t-form">Form</li>
                <li>
                    <a href="{{ route('penjualan') }}" class="waves-effect">
                        <i class="bx bxs-widget"></i>
                        <span key="t-penjualan">Surat Jalan</span>
                    </a>
                </li>
                @endif

                @if ($user->role == 'skm1')
                <li>
                    <a href="{{ route('dashboard') }}">
                        <i class="bx bx-home-circle"></i>
                        <span key="t-dashboards">Welcome</span>
                    </a>
                </li>
                <li class="menu-title" key="t-form">Form</li>
                <li>
                    <a href="{{ route('permintaan-skm') }}" class="waves-effect">
                        <i class="bx bxs-duplicate"></i>
                        <span key="t-minta">Permintaan</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('penerimaan-barang') }}" class="waves-effect">
                        <i class="bx bx-check-square"></i>
                        <span key="t-terima">Terima Barang</span>
                    </a>
                </li>
                <li class="menu-title" key="t-history">Riwayat</li>
                <li>
                    <a href="{{ route('permintaan-skm.history') }}" class="waves-effect">
                        <i class="bx bx-task"></i>
                        <span key="t-history-minta">Permintaan</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('penerimaan-barang.history') }}" class=" waves-effect">
                        <i class="bx bx-task"></i>
                        <span key="t-terima">Penerimaan</span>
                    </a>
                </li>
                @endif
                @endif
            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
