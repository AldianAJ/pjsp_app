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

                    @if ($user->role == 'gdb')
                        <li class="menu-title" key="t-menu">Menu</li>
                        <li>
                            <a href="#">
                                <i class="bx bx-home-circle"></i>
                                <span key="t-dashboards">Dashboards</span>
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

                        <li class="menu-title" key="t-transactions">Transaksi</li>

                        <li>
                            <a href="{{ route('pengiriman-gudang-utama') }}" class="waves-effect">
                                <i class="bx bxs-duplicate"></i>
                                <span key="t-gudang">Persetujuan Permintaan</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('stok-masuk') }}" class="waves-effect">
                                <i class="bx bxs-widget"></i>
                                <span key="t-pemesanan">Persediaan Masuk</span>
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
                                <span key="t-kirim">Pengiriman Barang</span>
                            </a>
                        </li>
                    @endif

                    @if ($user->role == 'skm')
                        <li class="menu-title" key="t-menu">Menu</li>
                        <li>
                            <a href="#">
                                <i class="bx bx-home-circle"></i>
                                <span key="t-dashboards">Dashboards</span>
                            </a>
                        </li>

                        <li class="menu-title" key="t-apps">Laporan</li>
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
                        </li>

                        <li class="menu-title" key="t-apps">Kinerja SKM</li>
                        <li>
                            <a href="{{ route('kinerja-minggu') }}" class="waves-effect">
                                <i class="bx bxs-receipt"></i>
                                <span key="t-kinerja">Target Mingguan</span>
                            </a>
                            <a href="{{ route('kinerja-hari') }}" class="waves-effect">
                                <i class="bx bxs-receipt"></i>
                                <span key="t-kinerja">Target Harian & Shift</span>
                            </a>
                            <a href="{{ route('kinerja-mesin') }}" class="waves-effect">
                                <i class="bx bxs-receipt"></i>
                                <span key="t-kinerja">Target Mesin</span>
                            </a>
                        </li>

                        <li class="menu-title" key="t-apps">Closing SKM</li>
                        <li>
                            <a href="{{ route('closing-mesin') }}" class="waves-effect">
                                <i class="bx bxs-receipt"></i>
                                <span key="t-kinerja">Closing Mesin</span>
                            </a>
                        </li>

                        <li class="menu-title" key="t-transactions">Transaksi</li>
                        <li>
                            <a href="{{ route('permintaan-skm') }}" class="waves-effect">
                                <i class="bx bxs-duplicate"></i>
                                <span key="t-minta">Permintaan</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('penerimaan-barang') }}" class="waves-effect">
                                <i class="bx bxs-duplicate"></i>
                                <span key="t-terima">Terima Barang</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('pengiriman-skm') }}" class="waves-effect">
                                <i class="bx bxs-duplicate"></i>
                                <span key="t-kirim-skm">Pengiriman ke Mesin</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('return-mesin') }}" class="waves-effect">
                                <i class="bx bxs-duplicate"></i>
                                <span key="t-return-mesin">Return ke SKM</span>
                            </a>
                        </li>

                        <li class="menu-title" key="t-history">Riwayat</li>
                        <li>
                            <a href="{{ route('permintaan-skm.history') }}" class="waves-effect">
                                <i class="bx bx-notepad"></i>
                                <span key="t-history-minta">Permintaan</span>
                            </a>
                        </li>
                    @endif

                @endif
            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
