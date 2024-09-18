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
                            {{-- <li>
                                <a href="{{ route('barang') }}" class="waves-effect">
                                    <i class="bx bxs-component"></i>
                                    <span key="t-barang">Barang</span>
                                    <span id="total-barangs" class="badge rounded-pill bg-danger float-end"></span>
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
                            </li> --}}

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

                        @if ($user->role == 'skm')
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
                            <li>
                                <a href="{{ route('stok-skm') }}" class="waves-effect">
                                    <i class="bx bxs-component"></i>
                                    <span key="t-stok-skm">Stok SKM</span>
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
