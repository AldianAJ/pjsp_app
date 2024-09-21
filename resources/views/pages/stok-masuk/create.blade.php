@extends('layouts.app')

@section('title')
    Tambah Stok Masuk
@endsection

@push('after-app-style')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container .select2-selection--single {
            padding: 0.30rem 0.45rem;
            height: 38.2px;
        }
    </style>
@endpush

@push('after-app-script')
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/datatables.init.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/autonumeric/4.10.5/autoNumeric.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#supplier_id').select2({
                width: 'resolve'
            });

            $('#showDataBarangButton').on('click', function() {
                if ($.fn.DataTable.isDataTable('#datatable-barang')) {
                    $('#datatable-barang').DataTable().clear().destroy();
                }

                $('#datatable-barang').DataTable({
                    ajax: {
                        url: "{{ route('stok-masuk.create') }}",
                        data: {
                            type: 'barangs'
                        }
                    },
                    lengthMenu: [5],
                    ordering: false,
                    columns: [{
                            data: null,
                            render: (data, type, row, meta) => meta.row + 1
                        },
                        {
                            data: "nm_brg"
                        },
                        {
                            data: null,
                            render: (data, type, row) => `<button type="button" class="btn btn-primary font-size-10 waves-effect waves-light" onclick="showSpecs('${row.brg_id}', '${row.nm_brg}')">
                            <i class="fas fa-plus align-middle"></i>
                        </button>`
                        }
                    ],
                });

                $('#dataBarang').modal('show');
            });

            window.showSpecs = function(brg_id, nm_brg) {
                if ($.fn.DataTable.isDataTable('#datatable-spek')) {
                    $('#datatable-spek').DataTable().clear().destroy();
                }

                $('#datatable-spek').DataTable({
                    ajax: {
                        url: "{{ route('stok-masuk.create') }}",
                        data: {
                            type: 'speks',
                            brg_id: brg_id,
                            nm_brg: nm_brg
                        }
                    },
                    lengthMenu: [5],
                    columns: [{
                            data: null,
                            render: (data, type, row, meta) => meta.row + 1
                        },
                        {
                            data: "spek"
                        },
                        {
                            data: "satuan1"
                        },
                        {
                            data: null,
                            render: (data, type, row) => `<button type="button" class="btn btn-primary font-size-10 waves-effect waves-light" onclick="showQtyModal('${row.brg_id}', '${row.nm_brg}', '${row.satuan1}', '${row.satuan2}', '${row.konversi1}', '${row.spek_id}', '${row.spek}')">
                            <i class="fas fa-plus align-middle"></i>
                        </button>`
                        }
                    ],
                });

                $('#dataBarang').modal('hide');
                $('#dataSpek').modal('show');
            };

            window.showQtyModal = function(brg_id, nm_brg, satuan1, satuan2, konversi1, spek_id, spek) {
                const modal = document.getElementById('qtyModal');
                modal.dataset.konversi1 = konversi1;
                document.getElementById('modal-brg-id').value = brg_id;
                document.getElementById('modal-nm-brg').value = nm_brg;
                document.getElementById('modal-qty-beli').value = '';
                document.getElementById('modal-satuan1').innerText = satuan1;
                document.getElementById('modal-qty-std').value = '';
                document.getElementById('modal-satuan2').innerText = satuan2;
                document.getElementById('modal-ket').value = spek;
                document.getElementById('modal-spek-id').value = spek_id;

                $('#dataSpek').modal('hide');
                $('#dataBarang').modal('hide');
                new bootstrap.Modal(modal).show();
            };

            $('#qtyModal .btn-primary').on('click', function() {
                addItem();
                $('#qtyModal').modal('hide');
                $('#dataBarang').modal('show');
            });

            // $('#dataSpek').on('hidden.bs.modal', function() {
            //     $('#dataBarang').modal('show');
            // });

            // $('#qtyModal').on('hidden.bs.modal', function() {
            //     $('#dataSpek').modal('show');
            // });

            new AutoNumeric('#modal-qty-beli', {
                decimalCharacter: ',',
                digitGroupSeparator: '.'
            });
            new AutoNumeric('#modal-qty-std', {
                decimalCharacter: ',',
                digitGroupSeparator: '.'
            });

            $('#modal-qty-beli').on('input', function() {
                const qtyBeli = parseFloat(AutoNumeric.getNumericString(this)) || 0;
                const konversi1 = parseFloat($('#modal-qty-std').data('konversi1')) || 0;
                const qtyStd = qtyBeli * konversi1;

                AutoNumeric.set('#modal-qty-std', qtyStd);
            });

            $('#modal-qty-std').on('input', function() {
                const qtyStd = parseFloat(AutoNumeric.getNumericString(this)) || 0;
                const konversi1 = parseFloat($('#modal-qty-std').data('konversi1')) || 0;
                const qtyBeli = qtyStd / konversi1;

                AutoNumeric.set('#modal-qty-beli', qtyBeli);
            });

            let selectedItems = [];

            function addItem() {
                const brg_id = document.getElementById('modal-brg-id').value;
                const nm_brg = document.getElementById('modal-nm-brg').value;
                const qty_beli = parseFloat(document.getElementById('modal-qty-beli').value);
                const satuan1 = document.getElementById('modal-satuan1').innerText;
                const qty_std = parseFloat(document.getElementById('modal-qty-std').value);
                const satuan2 = document.getElementById('modal-satuan2').innerText;
                const ket = document.getElementById('modal-ket').value;
                const spek_id = document.getElementById('modal-spek-id').value;
                const spek = document.getElementById('modal-ket').value;

                if (qty_beli <= 0 || qty_std <= 0) {
                    alert('Jumlah harus lebih dari 0');
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
                    spek,
                });
                updateItems();

                const qtyModal = bootstrap.Modal.getInstance(document.getElementById('qtyModal'));
                qtyModal.hide();
            }

            function removeItem(index) {
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
                    <td>${item.spek}</td>
                    <td>${item.qty_beli}</td>
                    <td>${item.satuan1}</td>
                    <td>
                        <button class="btn btn-danger waves-effect waves-light" onclick="removeItem(${index})"><i class="bx bxs-trash align-middle font-size-14"></i></button>
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
                <h4 class="mb-sm-0 font-size-18">Tambah Stok Masuk</h4>
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
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Data Transaksi</h5>
                    <form action="{{ route('stok-masuk.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group mt-3">
                            <label for="no_sj">No. SJ Supplier :</label>
                            <input type="text" class="form-control @error('no_sj') is-invalid @enderror" name="no_sj"
                                value="{{ old('no_sj') }}" placeholder="Masukkan No. Surat Jalan dari Supplier" required>
                            @error('no_sj')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group mt-3">
                            <label for="supplier_id">Nama Supplier </label>
                            <select name="supplier_id" id="supplier_id"
                                class="form-control @error('supplier_id') is-invalid @enderror" style="width: 100%;"
                                required>
                                <option value="">-- Pilih Supplier --</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->supplier_id }}"
                                        @if (isset($data_supplier) && $data_supplier->supplier_id == $supplier->supplier_id) selected @endif>
                                        {{ $supplier->nama }}
                                    </option>
                                @endforeach
                            </select>
                            @error('supplier_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <input type="hidden" name="gudang_id" id="gudang_id"
                            value="{{ old('gudang_id', $gudang_id ?? '') }}">
                        <div class="form-group mt-3">
                            <label for="tgl">Tanggal Terima :</label>
                            <input type="date" class="form-control" name="tgl"
                                value="{{ old('tgl', \Carbon\Carbon::now()->format('Y-m-d')) }}" required>
                        </div>
                        <div id="items-container"></div> <!-- Container for items input fields -->
                        <div class="d-flex justify-content-end mt-3">
                            <a href="{{ route('stok-masuk') }}" class="btn btn-secondary waves-effect waves-light me-2">
                                <i class="bx bx-caret-left align-middle me-2 font-size-18"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-primary waves-effect waves-light" id="saveButton"
                                disabled>
                                <i class="bx bx bxs-save align-middle me-2 font-size-18"></i>Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <button type="button" class="btn btn-dark waves-effect waves-light" id="showDataBarangButton">
        <i class="bx bx-plus-circle align-middle me-2 font-size-18"></i>Tambah Data Barang
    </button>

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

    <div class="modal fade" id="dataSpek" tabindex="-1" role="dialog" aria-labelledby="dataSpekLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="dataSpekLabel">Data Spesifikasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table id="datatable-spek" class="table align-middle table-nowrap">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Spesifikasi</th>
                                            <th>Satuan</th>
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
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
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
                            <input type="text" class="form-control me-2" id="modal-qty-beli" min="1"
                                data-konversi1="2" required>
                            <label for="modal-satuan1" class="form-label fw-bolder" id="modal-satuan1"></label>
                        </div>
                    </div>
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
        <div class="col-lg-12 mt-4">
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
