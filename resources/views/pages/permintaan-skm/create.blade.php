@extends('layouts.app')

@section('title')
    Tambah Permintaan
@endsection

@push('after-app-script')
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <!-- Responsive examples -->
    <script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/datatables.init.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#showDataBarangButton').on('click', function() {
                if ($.fn.DataTable.isDataTable('#datatable')) {
                    $('#datatable').DataTable().clear().destroy();
                }

                $('#datatable').DataTable({
                    ajax: "{{ route('permintaan-skm.create') }}",
                    lengthMenu: [5],
                    columns: [{
                            data: "barang.nm_brg"
                        },
                        {
                            data: "satuan_besar"
                        },
                        {
                            data: null,
                            render: function(data, type, row) {
                                return `<button type="button" class="btn btn-primary btn-sm" onclick="showModal('${row.brg_id}', '${row.nm_brg}', '${row.satuan_besar}')">
                            <i class="fas fa-plus"></i>
                        </button>`;
                            },
                        }
                    ],
                });
                $('#dataModal').modal('show')
            });

            $('#dataModal').on('hidden.bs.modal', function() {
                if ($.fn.DataTable.isDataTable('#datatable')) {}
            });

            $('#qtyModal').on('hidden.bs.modal', function() {
                $('#showDataBarangButton').show();
                $('#dataModal').modal('show');
            });

            $('#qtyModal .btn-primary').on('click', function() {
                $('#showDataBarangButton').hide();
            });
        });



        let selectedItems = [];

        function showModal(brg_id, nm_brg, satuan_besar) {
            const modal = document.getElementById('qtyModal');
            document.getElementById('modal-brg-id').value = brg_id;
            document.getElementById('modal-nm-brg').value = nm_brg;
            document.getElementById('modal-satuan-besar').value = satuan_besar;
            document.getElementById('modal-qty').value = '';

            new bootstrap.Modal(modal).show();
            $('#dataModal').modal('hide');
        }

        function addItem() {
            const brg_id = document.getElementById('modal-brg-id').value;
            const nm_brg = document.getElementById('modal-nm-brg').value;
            const qty = parseFloat(document.getElementById('modal-qty').value);
            const satuan_besar = document.getElementById('modal-satuan-besar').value;

            if (qty <= 0) {
                alert('Jumlah harus lebih dari 0');
                return;
            }

            selectedItems.push({
                brg_id,
                nm_brg,
                qty,
                satuan_besar,
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

            itemsTable.innerHTML = selectedItems.map((item, index) => `
            <tr>
                <td>${index + 1}</td>
                <td>${item.nm_brg}</td>
                <td>${item.qty}</td>
                <td>
                    <button class="btn btn-danger waves-effect waves-light" onclick="removeItem(${index})"><i class="bx bxs-trash align-middle font-size-14"></i></button>
                </td>
            </tr>
        `).join('');

            itemsContainer.innerHTML = selectedItems.map((item, index) => `
            <input type="hidden" name="items[${index}][brg_id]" value="${item.brg_id}">
            <input type="hidden" name="items[${index}][qty]" value="${item.qty}">
            <input type="hidden" name="items[${index}][satuan_besar]" value="${item.satuan_besar}">
        `).join('');

            saveButton.disabled = selectedItems.length === 0;
        }
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
                    <h5 class="card-title">Data Transaksi</h5>
                    <form action="{{ route('permintaan-skm.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        {{-- <div class="form-group mt-3">
                            <label for="no_trm">No. Dokumen</label>
                            <input type="text" class="form-control" name="no_trm" value="{{ old('no_trm', $no_trm) }}"
                                required>
                        </div> --}}
                        <div class="form-group mt-3">
                            <label for="tgl">Tanggal</label>
                            <input type="date" class="form-control" name="tgl"
                                value="{{ old('tgl', \Carbon\Carbon::now()->format('Y-m-d')) }}" required readonly>
                        </div>
                        <input type="hidden" name="gudang_id" id="gudang_id"
                            value="{{ old('gudang_id', $gudang_id ?? '') }}">
                        <div id="items-container"></div> <!-- Container for items input fields -->
                        <div class="d-flex justify-content-end mt-3">
                            <a href="{{ route('permintaan-skm') }}"
                                class="btn btn-secondary waves-effect waves-light me-2">
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

    <div class="modal fade" id="dataModal" tabindex="-1" role="dialog" aria-labelledby="dataModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="dataModalLabel">Data Barang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="table-responsive">
                                <table id="datatable" class="table align-middle table-nowrap">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Nama Barang</th>
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
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Selesai</button>
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
                            <label for="modal-satuan-besar">Satuan</label>
                            <input type="text" class="form-control" id="modal-satuan-besar" readonly>
                        </div>
                    </form>
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
                    <h5 class="card-title">List Permintaan</h5>
                    <table class="table table-striped" id="selected-items-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Barang</th>
                                <th>Qty</th>
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

@endsection
