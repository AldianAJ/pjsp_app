@extends('layouts.app')

@section('title')
    Persetujuan Permintaan
@endsection

@push('after-app-style')
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@push('after-app-script')
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <!-- Responsive examples -->
    <script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/datatables.init.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/autonumeric/4.10.5/autoNumeric.min.js"></script>
    <script>
        $(document).ready(function() {
            var no_reqskm = "{{ $no_reqskm }}";

            $('#showDataBarangButton').on('click', function() {
                if ($.fn.DataTable.isDataTable('#datatable-barang')) {
                    $('#datatable-barang').DataTable().clear().destroy();
                }

                $('#datatable-barang').DataTable({
                    ajax: {
                        url: "{{ url('pengiriman-gudang-utama/create') }}/" + no_reqskm,
                        data: {
                            type: 'details'
                        }
                    },
                    lengthMenu: [5],
                    columns: [{
                            data: null,
                            render: (data, type, row, meta) => meta.row + 1
                        },
                        {
                            data: "nm_brg"
                        },
                        {
                            data: null,
                            render: (data, type, row) => `<button type="button" class="btn btn-primary font-size-10 waves-effect waves-light" onclick="showQtyModal('${row.brg_id}', '${row.nm_brg}', '${row.qty_beli}','${row.satuan1}', '${row.qty_std}','${row.satuan2}', '${row.konversi1}', '${row.spek_id}', '${row.spek}')">
                            <i class="fas fa-plus align-middle"></i>
                        </button>`
                        }
                    ],
                });
                $('#dataBarang').modal('show');
            });

            $('#showDataPermintaanButton').on('click', function() {
                if ($.fn.DataTable.isDataTable('#datatable-permintaan')) {
                    $('#datatable-permintaan').DataTable().clear().destroy();
                }

                $('#datatable-permintaan').DataTable({
                    ajax: {
                        url: "{{ url('pengiriman-gudang-utama/create') }}/" + no_reqskm,
                        data: {
                            type: 'details'
                        }
                    },
                    lengthMenu: [5],
                    columns: [{
                            data: null,
                            render: (data, type, row, meta) => meta.row + 1
                        },
                        {
                            data: "nm_brg"
                        },
                        {
                            data: "qty_beli"
                        },
                        {
                            data: "satuan_beli"
                        }
                    ],
                });

                $('#permintaanModal').modal('show');
            });

            window.showQtyModal = function(brg_id, nm_brg, qty_beli, satuan1, qty_std, satuan2, konversi1, spek_id,
                spek) {
                const existingIndex = selectedItems.findIndex(item => item.brg_id === brg_id);
                if (existingIndex !== -1) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning',
                        text: 'Item sudah ditambahkan!',
                    });
                    return; // Hentikan proses jika item sudah ada
                }

                // Set nilai modal jika item belum ada
                document.getElementById('modal-brg-id').value = brg_id;
                document.getElementById('modal-nm-brg').value = nm_brg;
                AutoNumeric.set('#modal-qty-beli', qty_beli);
                document.getElementById('modal-satuan1').innerText = satuan1;
                AutoNumeric.set('#modal-qty-std', qty_std);
                document.getElementById('modal-konversi1').value = konversi1;
                document.getElementById('modal-satuan2').innerText = satuan2;
                document.getElementById('modal-ket').value = spek;
                document.getElementById('modal-spek-id').value = spek_id;

                $('#dataBarang').modal('hide');
                new bootstrap.Modal(document.getElementById('qtyModal')).show();
            };



            $('#qtyModal .btn-primary').on('click', function() {
                addItem();
                $('#qtyModal').modal('hide');
                $('#dataBarang').modal('show');
            });

            new AutoNumeric('#modal-qty-beli', {
                decimalCharacter: ',',
                digitGroupSeparator: '.'
            });
            new AutoNumeric('#modal-qty-std', {
                decimalCharacter: ',',
                digitGroupSeparator: '.'
            });

            $('#modal-qty-beli').on('input', function() {
                const qtyBeli = parseFloat(AutoNumeric.getNumericString(this));
                const konversi1 = parseFloat($('#modal-konversi1').val());
                const qtyStd = qtyBeli * konversi1;

                AutoNumeric.set('#modal-qty-std', qtyStd);
            });

            $('#modal-qty-std').on('input', function() {
                const qtyStd = parseFloat(AutoNumeric.getNumericString(this));
                const konversi1 = ($('#modal-konversi1').val());
                const qtyBeli = qtyStd / konversi1;

                AutoNumeric.set('#modal-qty-beli', qtyBeli);
            });

            let selectedItems = [];

            function addItem() {
                const brg_id = document.getElementById('modal-brg-id').value;
                const nm_brg = document.getElementById('modal-nm-brg').value;
                const qty_beli = parseFloat(AutoNumeric.getNumericString(document.getElementById(
                    'modal-qty-beli')));
                const satuan1 = document.getElementById('modal-satuan1').innerText;
                const qty_std = parseFloat(AutoNumeric.getNumericString(document.getElementById(
                    'modal-qty-std')));
                const satuan2 = document.getElementById('modal-satuan2').innerText;
                const ket = document.getElementById('modal-ket').value;
                const spek_id = document.getElementById('modal-spek-id').value;

                if (qty_beli <= 0 || qty_std <= 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Qty harus lebih dari 0',
                    });
                    return;
                }


                selectedItems.push({
                    brg_id,
                    nm_brg,
                    qty_beli,
                    satuan1,
                    qty_std,
                    satuan2,
                    ket,
                    spek_id,
                });
                updateItems();
            }

            window.removeItem = function(index) {
                const removedItem = selectedItems[index];
                const table = $('#datatable-barang').DataTable();
                table.row.add({
                    brg_id: removedItem.brg_id,
                    nm_brg: removedItem.nm_brg,
                }).draw();
                selectedItems.splice(index, 1);
                updateItems();
            }

            function updateItems() {
                const itemsTable = document.getElementById('selected-items');
                const itemsContainer = document.getElementById('items-container');
                const saveButton = document.getElementById('saveButton');

                itemsTable.innerHTML = selectedItems.map((item, index) => `
                <tr>
                    <td>${index + 1}</td>
                    <td>${item.nm_brg}</td>
                    <td>${item.ket}</td>
                    <td>${item.qty_beli}</td>
                    <td>${item.satuan1}</td>
                    <td>
                        <button class="btn btn-danger waves-effect waves-light" onclick="removeItem(${index})">
                            <i class="bx bxs-trash align-middle font-size-14"></i>
                        </button>
                    </td>
                </tr>
            `).join('');

                itemsContainer.innerHTML = selectedItems.map((item, index) => `
                <input type="hidden" name="items[${index}][brg_id]" value="${item.brg_id}">
                <input type="hidden" name="items[${index}][qty_beli]" value="${item.qty_beli}">
                <input type="hidden" name="items[${index}][satuan_beli]" value="${item.satuan1}">
                <input type="hidden" name="items[${index}][qty_std]" value="${item.qty_std}">
                <input type="hidden" name="items[${index}][satuan_std]" value="${item.satuan2}">
                <input type="hidden" name="items[${index}][ket]" value="${item.ket}">
                <input type="hidden" name="items[${index}][spek_id]" value="${item.spek_id}">
            `).join('');

                saveButton.disabled = selectedItems.length === 0;
            }
        });
    </script>
@endpush

@section('content')
    <!-- Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Tambah Pengiriman</h4>
            </div>
        </div>
    </div>

    <!-- Main Form -->
    <div class="row">
        <div class="col-md-12">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Data Transaksi</h5>
                    <form action="{{ route('pengiriman-gudang-utama.store') }}" method="post"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="form-group mt-3">
                            <label for="no_reqskm">No. Dokumen Permintaan SKM</label>
                            <input type="text" name="no_reqskm" id="no_reqskm" class="form-control"
                                value="{{ $no_req }}" readonly>
                        </div>
                        <div class="form-group mt-3">
                            <label for="tgl_krm">Tanggal</label>
                            <input type="date" class="form-control" name="tgl_krm"
                                value="{{ old('tgl_krm', \Carbon\Carbon::now()->format('Y-m-d')) }}" required>
                        </div>
                        <input type="hidden" name="gudang_id" id="gudang_id"
                            value="{{ old('gudang_id', $gudang_id ?? '') }}">
                        <div id="items-container"></div> <!-- Container for items input fields -->
                        <div class="d-flex justify-content-end mt-3">
                            <a href="{{ route('pengiriman-gudang-utama') }}"
                                class="btn btn-secondary waves-effect waves-light me-2">
                                <i class="bx bx-caret-left align-middle me-2 font-size-18"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-primary waves-effect waves-light" id="saveButton"
                                disabled><i class="bx bx bxs-save align-middle me-2 font-size-18"></i>Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <button type="button" class="btn btn-dark waves-effect waves-light me-2" id="showDataPermintaanButton">
                    <i class="bx bx-show-alt align-middle me-2 font-size-18"></i> Lihat Data Permintaan
                </button>
            </div>
            <div>
                <button type="button" class="btn btn-dark waves-effect waves-light" id="showDataBarangButton">
                    <i class="bx bx-plus-circle align-middle me-2 font-size-18"></i>Tambah Data Barang
                </button>
            </div>
        </div>
    </div>

    <div class="modal fade" id="permintaanModal" tabindex="-1" role="dialog" aria-labelledby="permintaanModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="permintaanModalLabel">Data Permintaan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table id="datatable-permintaan" class="table align-middle table-nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Barang</th>
                                    <th>Qty</th>
                                    <th>Satuan</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="dataBarang" tabindex="-1" role="dialog" aria-labelledby="dataModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="dataModalLabel">Data Barang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="table-responsive">
                                <table id="datatable-barang" class="table align-middle table-nowrap">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Barang</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Selesai</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="qtyModal" tabindex="-1" role="dialog" aria-labelledby="qtyModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="qtyModalLabel">Input Qty</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="modal-brg-id">
                    <div class="mb-3">
                        <label for="modal-nm-brg" class="form-label">Nama Barang</label>
                        <input type="text" class="form-control" id="modal-nm-brg" readonly>
                    </div>
                    <input type="hidden" id="modal-spek-id">
                    <div class="mb-3">
                        <label for="modal-qty" class="form-label">Qty</label>
                        <div class="d-flex align-items-center">
                            <input type="text" class="form-control me-2" id="modal-qty-beli" min="1" required>
                            <label for="modal-satuan1" class="form-label fw-bolder" id="modal-satuan1"></label>
                        </div>
                    </div>
                    <input type="hidden" id="modal-konversi1">
                    <div class="mb-3">
                        <label for="modal-qty-std" class="form-label">Qty Konversi</label>
                        <div class="d-flex align-items-center">
                            <input type="text" class="form-control me-2" id="modal-qty-std" min="1"
                                data-konversi1="2" required>
                            <label for="modal-satuan2" class="form-label fw-bolder" id="modal-satuan2"></label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="modal-ket" class="form-label">Keterangan</label>
                        <textarea class="form-control" id="modal-ket"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" onclick="addItem()">Tambah</button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 mt-2">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">List Stok Masuk</h5>
                    <div class="table-responsive">
                        <table class="table table-striped" id="selected-items-table">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Ket</th>
                                    <th>Qty</th>
                                    <th>Satuan</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="selected-items">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
