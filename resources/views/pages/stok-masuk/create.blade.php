@extends('layouts.app')

@section('title')
    Tambah Persediaan Masuk
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
    <!-- Responsive examples -->
    <script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/datatables.init.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#supplier_id').select2({
                width: 'resolve'
            });
        });

        $('#datatable').DataTable({
            ajax: "{{ route('stok-masuk.create') }}",
            lengthMenu: [5],
            columns: [{
                    data: "nm_brg"
                },
                {
                    data: "satuan_beli"
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        return `<button type="button" class="btn btn-primary btn-sm" onclick="showModal('${row.brg_id}', '${row.nm_brg}', '${row.satuan_beli}')">
                            <i class="fas fa-plus"></i>
                        </button>`;
                    },
                }
            ],
        });
    </script>
@endpush

@section('content')
    <!-- Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Tambah Persediaan Masuk</h4>
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
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Data Transaksi</h5>
                    <form action="{{ route('stok-masuk.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        {{-- <div class="form-group mt-3">
                            <label for="no_trm">No. Dokumen</label>
                            <input type="text" class="form-control" name="no_trm" value="{{ old('no_trm', $no_trm) }}"
                                required>
                        </div> --}}
                        <div class="form-group mt-3">
                            <label for="no_sj">No. SJ Supplier</label>
                            <input type="text" class="form-control" name="no_sj" value="{{ old('no_sj') }}"
                                placeholder="Masukkan No. Surat Jalan dari Supplier" required>
                        </div>
                        <input type="hidden" name="gudang_id" id="gudang_id"
                            value="{{ old('gudang_id', $gudang_id ?? '') }}">
                        <div class="form-group mt-3">
                            <label for="supplier_id">Nama Supplier</label>
                            <select name="supplier_id" id="supplier_id" class="form-control" style="width: 100%;" required>
                                <option selected="selected">-- Pilih Supplier --</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->supplier_id }}">
                                        {{ $supplier->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mt-3">
                            <label for="tgl">Tanggal</label>
                            <input type="date" class="form-control" name="tgl"
                                value="{{ old('tgl', \Carbon\Carbon::now()->format('Y-m-d')) }}" required readonly>
                        </div>
                        <input type="hidden" name="gudang_id" id="gudang_id"
                            value="{{ old('gudang_id', $gudang_id ?? '') }}">
                        <div id="items-container"></div> <!-- Container for items input fields -->
                        <div class="d-flex justify-content-end mt-3">
                            <a href="{{ route('stok-masuk') }}" class="btn btn-secondary waves-effect waves-light me-2">
                                <i class="bx bx-caret-left align-middle me-2 font-size-18"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-primary waves-effect waves-light"><i
                                    class="bx bx bxs-save align-middle me-2 font-size-18"></i>Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <!-- Data Barang Table -->
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">Data Barang</h4>
                    <div class="table-responsive">
                        <table id="datatable" class="table align-middle table-nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Barang</th>
                                    <th>Satuan</th>
                                    <th style="text-align: center;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <!-- List Persediaan Masuk -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">List Persediaan Masuk</h5>
                    <table class="table table-striped" id="selected-items-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Barang</th>
                                <th>Jumlah</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="selected-items">
                            <!-- Selected items will be appended here dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Input Quantity -->
    <div class="modal fade" id="qtyModal" tabindex="-1" role="dialog" aria-labelledby="qtyModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="qtyModalLabel">Input Quantity</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="qtyForm">
                        <input type="hidden" id="modal-brg-id">
                        <div class="form-group">
                            <label for="modal-nm-brg">Nama Barang</label>
                            <input type="text" class="form-control" id="modal-nm-brg" readonly>
                        </div>
                        <div class="form-group mt-3">
                            <label for="modal-qty">Jumlah</label>
                            <input type="number" class="form-control" id="modal-qty" required>
                        </div>
                        <div class="form-group mt-3">
                            <label for="modal-satuan-beli">Satuan</label>
                            <input type="text" class="form-control" id="modal-satuan-beli" readonly>
                        </div>
                        <div class="form-group mt-3">
                            <label for="modal-ket">Keterangan</label>
                            <input type="text" class="form-control" id="modal-ket">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal"
                        onclick="addItem()">Tambah</button>
                </div>
            </div>
        </div>
    </div>


    <script>
        let selectedItems = [];

        function showModal(brg_id, nm_brg, satuan_beli) {
            const modal = document.getElementById('qtyModal');
            document.getElementById('modal-brg-id').value = brg_id;
            document.getElementById('modal-nm-brg').value = nm_brg;
            document.getElementById('modal-satuan-beli').value = satuan_beli;
            document.getElementById('modal-qty').value = '';
            document.getElementById('modal-ket').value = '';
            new bootstrap.Modal(modal).show();
        }

        function addItem() {
            const brg_id = document.getElementById('modal-brg-id').value;
            const nm_brg = document.getElementById('modal-nm-brg').value;
            const qty = parseFloat(document.getElementById('modal-qty').value);
            const satuan_beli = document.getElementById('modal-satuan-beli').value;
            const ket = document.getElementById('modal-ket').value;

            if (qty <= 0) {
                alert('Jumlah harus lebih dari 0');
                return;
            }

            selectedItems.push({
                brg_id,
                nm_brg,
                qty,
                satuan_beli,
                ket
            });
            updateItems();
        }

        function removeItem(index) {
            selectedItems.splice(index, 1);
            updateItems();
        }

        function updateItems() {
            const itemsTable = document.getElementById('selected-items');
            const itemsContainer = document.getElementById('items-container');

            itemsTable.innerHTML = selectedItems.map((item, index) => `
                <tr>
                    <td>${index + 1}</td>
                    <td>${item.nm_brg}</td>
                    <td>${item.qty}</td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeItem(${index})">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </td>
                </tr>
            `).join('');

            itemsContainer.innerHTML = selectedItems.map((item, index) => `
                <input type="hidden" name="items[${index}][brg_id]" value="${item.brg_id}">
                <input type="hidden" name="items[${index}][qty]" value="${item.qty}">
                <input type="hidden" name="items[${index}][satuan_beli]" value="${item.satuan_beli}">
                <input type="hidden" name="items[${index}][ket]" value="${item.ket}">
            `).join('');
        }
    </script>


@endsection
