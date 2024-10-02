@extends('layouts.app')

@section('title')
Tambah Permintaan
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
            $('#showDataBarangButton').on('click', function() {
                if ($.fn.DataTable.isDataTable('#datatable-barang')) {
                    $('#datatable-barang').DataTable().clear().destroy();
                }

                $('#datatable-barang').DataTable({
                    ajax: {
                        url: "{{ route('permintaan-skm.create') }}",
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
                            render: (data, type, row) => `<button type="button" class="btn btn-primary font-size-14 waves-effect waves-light" onclick="showSpecs('${row.brg_id}', '${row.nm_brg}')">
                        Pilih
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
                        url: "{{ route('permintaan-skm.create') }}",
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
                            render: (data, type, row) => `<button type="button" class="btn btn-primary font-size-14 waves-effect waves-light" onclick="showQtyModal('${row.brg_id}', '${row.nm_brg}', '${row.satuan1}', '${row.satuan2}', '${row.konversi1}', '${row.spek_id}', '${row.spek}')">
                        Pilih
                    </button>`
                        }
                    ],
                });

                $('#dataBarang').modal('hide');
                $('#dataSpek').modal('show');
            };

            window.showQtyModal = function(brg_id, nm_brg, satuan1, satuan2, konversi1, spek_id, spek) {
                const modal = document.getElementById('qtyModal');
                document.getElementById('modal-brg-id').value = brg_id;
                document.getElementById('modal-nm-brg').value = nm_brg;
                document.getElementById('modal-qty-beli').value = '';
                document.getElementById('modal-satuan1').innerText = satuan1;
                document.getElementById('modal-qty-std').value = '';
                document.getElementById('modal-konversi1').value = konversi1;
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

            new AutoNumeric('#modal-qty-beli', {
                decimalCharacter: ',',
                digitGroupSeparator: '.'
            });
            new AutoNumeric('#modal-qty-std', {
                decimalCharacter: ',',
                digitGroupSeparator: '.'
            });

            $('#modal-qty-beli').on('keyup', function() {
                const qtyBeli = parseFloat(AutoNumeric.getNumericString(this)) || 0;
                const konversi1 = parseFloat($('#modal-konversi1').val());
                const qtyStd = qtyBeli * konversi1;

                AutoNumeric.set('#modal-qty-std', qtyStd);
            });

            $('#modal-qty-std').on('keyup', function() {
                const qtyStd = parseFloat(AutoNumeric.getNumericString(this)) || 0;
                const konversi1 = ($('#modal-konversi1').val());
                const qtyBeli = qtyStd / konversi1;

                AutoNumeric.set('#modal-qty-beli', qtyBeli);
            });

            let selectedItems = [];

            function addItem() {
                const brg_id = document.getElementById('modal-brg-id').value;
                const nm_brg = document.getElementById('modal-nm-brg').value;
                const qty_beli = parseFloat(AutoNumeric.getNumericString(document.getElementById(
                    'modal-qty-beli'))) || 0;
                const satuan1 = document.getElementById('modal-satuan1').innerText;
                const konversi1 = parseFloat(document.getElementById('modal-konversi1').value) || 0;
                const qty_std = parseFloat(AutoNumeric.getNumericString(document.getElementById(
                    'modal-qty-std'))) || 0;
                const satuan2 = document.getElementById('modal-satuan2').innerText;
                const ket = document.getElementById('modal-ket').value;
                const spek_id = document.getElementById('modal-spek-id').value;

                if (qty_beli <= 0 || qty_std <= 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Tidak Bisa',
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

                AutoNumeric.set('#modal-qty-beli', 0);
                AutoNumeric.set('#modal-qty-std', 0);
            }

            window.removeItem = function(index) {
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
            <h4 class="mb-sm-0 font-size-18">Tambah Permintaan</h4>
        </div>
    </div>
</div>

<!-- Main Form -->
<div class="row">
    <div class="col-md-12">
        <!-- Display validation errors -->
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
                <h3 class="card-title fw-bolder">Data Transaksi</h3>
                <form action="{{ route('permintaan-skm.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    {{-- <div class="form-group mt-3">
                        <label for="no_trm">No. Dokumen</label>
                        <input type="text" class="form-control" name="no_trm" value="{{ old('no_trm', $no_trm) }}"
                            required>
                    </div> --}}
                    <div class="form-group mt-3">
                        <label for="tgl">Tanggal :</label>
                        <input type="date" class="form-control" name="tgl"
                            value="{{ old('tgl', \Carbon\Carbon::now()->format('Y-m-d')) }}" required>
                    </div>
                    <input type="hidden" name="gudang_id" id="gudang_id"
                        value="{{ old('gudang_id', $gudang_id ?? '') }}">
                    <div id="items-container"></div> <!-- Container for items input fields -->
                    <div class="d-flex justify-content-end mt-3">
                        <a href="{{ route('permintaan-skm') }}" class="btn btn-secondary waves-effect waves-light me-2">
                            <i class="bx bx-caret-left align-middle me-2 font-size-18"></i>Kembali
                        </a>
                        <button type="submit" class="btn btn-primary waves-effect waves-light" id="saveButton" disabled>
                            <i class="bx bx bxs-save align-middle me-2 font-size-18"></i>Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <button type="button" class="btn btn-dark waves-effect waves-light" id="showDataBarangButton">
            <i class="bx bx-plus-circle align-middle me-2 font-size-18"></i>Tambah Data Barang
        </button>
    </div>
</div>

<div class="modal fade" id="dataBarang" tabindex="-1" role="dialog" aria-labelledby="dataModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title fw-bolder" id="dataModalLabel">Data Barang</h3>
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

<!-- Modal for Input Quantity -->
<div class="modal fade" id="dataSpek" tabindex="-1" role="dialog" aria-labelledby="dataSpekLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title fw-bolder" id="dataSpekLabel">Data Spesifikasi</h3>
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

<div class="modal fade" id="qtyModal" tabindex="-1" role="dialog" aria-labelledby="qtyModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title fw-bolder" id="qtyModalLabel">Input Qty</h3>
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
                        <input type="text" inputmode="numeric" class="form-control me-2" id="modal-qty-beli" min="1"
                            required>
                        <label for="modal-satuan1" class="form-label fw-bolder" id="modal-satuan1"></label>
                    </div>
                </div>
                <input type="hidden" id="modal-konversi1">
                <div class="mb-3">
                    <label for="modal-qty-std" class="form-label">Qty Konversi</label>
                    <div class="d-flex align-items-center">
                        <input type="text" inputmode="numeric" class="form-control me-2" id="modal-qty-std" min="1"
                            required>
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
                <h4 class="card-title fw-bolder">List Stok Masuk</h4>
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