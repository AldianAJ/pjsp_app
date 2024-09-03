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

                        @if ($user->role == 'GDB')
                            <li class="menu-title" key="t-menu">Menu</li>
                            <li>
                                <a href="#">
                                    <i class="bx bx-home-circle"></i>
                                    <span key="t-dashboards">Dashboards</span>
                                </a>
                            </li>

                            <li class="menu-title" key="t-apps">Data Master</li>
                            <li>
                                <a href="{{ route('barang') }}" class="waves-effect">
                                    <i class="bx bxs-component"></i>
                                    <span key="t-barang">Barang</span>
                                    <span class="badge rounded-pill bg-danger float-end">10</span>
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0);" class="has-arrow waves-effect">
                                    <i class="bx bxs-building-house"></i>
                                    <span key="t-gudang">Gudang</span>
                                </a>
                                <ul class="sub-menu">
                                    <li>
                                        <a href="{{ route('gudang') }}" key="t-data-gudang">Data Gudang</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('mesin') }}" key="t-mesin">Data Mesin</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('jenis-mesin') }}" key="t-jenis-mesin">Data Jenis Mesin</a>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <a href="{{ route('supplier') }}" class="waves-effect">
                                    <i class="bx bxs-business"></i>
                                    <span key="t-supplier">Supplier</span>
                                </a>
                            </li>

                            <li class="menu-title" key="t-transactions">Transaksi</li>

                            <li>
                                <a href="{{ route('pengiriman') }}" class="waves-effect">
                                    <i class="bx bxs-duplicate"></i>
                                    <span key="t-gudang">Permintaan</span>
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
                                <a href="#" class="waves-effect">
                                    <i class="bx bx-notepad"></i>
                                    <span key="t-history-minta">Permintaan</span>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="waves-effect">
                                    <i class="bx bx-task"></i>
                                    <span key="t-kasir">Pengiriman Barang</span>
                                </a>
                            </li>
                        @endif

                        @if ($user->role == 'SKM')
                            <li class="menu-title" key="t-menu">Menu</li>
                            <li>
                                <a href="#">
                                    <i class="bx bx-home-circle"></i>
                                    <span key="t-dashboards">Dashboards</span>
                                </a>
                            </li>

                            <li class="menu-title" key="t-apps">Data Master</li>
                            <li>
                                <a href="{{ route('barang') }}" class="waves-effect">
                                    <i class="bx bxs-component"></i>
                                    <span key="t-gudang">Barang</span>
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
                                    <span key="t-kinerja">Target Harian</span>
                                </a>
                                <a href="{{ route('kinerja-shift') }}" class="waves-effect">
                                    <i class="bx bxs-receipt"></i>
                                    <span key="t-kinerja">Target Shift</span>
                                </a>
                                <a href="{{ route('kinerja-mesin') }}" class="waves-effect">
                                    <i class="bx bxs-receipt"></i>
                                    <span key="t-kinerja">Target Mesin</span>
                                </a>
                            </li>

                            <li class="menu-title" key="t-transactions">Transaksi</li>
                            <li>
                                <a href="{{ route('permintaan') }}" class="waves-effect">
                                    <i class="bx bxs-duplicate"></i>
                                    <span key="t-minta">Permintaan</span>
                                </a>
                            </li>

                            <li class="menu-title" key="t-history">Riwayat</li>
                            <li>
                                <a href="#" class="waves-effect">
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
