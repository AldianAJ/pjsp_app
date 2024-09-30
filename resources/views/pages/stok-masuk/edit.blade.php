@extends('layouts.app')

@section('title')
Edit Penerimaan Supplier
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
<script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/datatables.init.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>

<script>
    $(document).ready(function() {
            $('#supplier_id').select2({
                width: 'resolve'
            });

            var no_trm = "{{ $no_trm }}";
            var dataTableInitialized = false;

            function initializeDataTable() {
                $('#datatable-detail').DataTable({
                    ajax: {
                        url: "{{ url('stok-masuk/edit') }}/" + no_trm,
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
                            data: "barang.nm_brg",
                        },
                        {
                            data: "qty_beli",
                            render: function(data, type, row) {
                                return `
                                    <span class="qty-value" style="width: 5.5rem;">${data}</span>
                                    <input type="number" class="form-control qty-input d-none" style="width: 5.5rem;" value="${data}">
                                `;
                            }
                        },
                        {
                            data: "satuan_beli",
                        },
                        {
                            data: null,
                            render: function(data, type, row) {
                                return `
                                    <button class="btn btn-success waves-effect waves-light edit-btn"><i class="bx bx-edit align-middle font-size-14"></i> Edit</button>
                                    <button class="btn btn-danger waves-effect waves-light cancel-btn d-none"><i class="bx bx-x-circle align-middle font-size-14"></i> Batal</button>
                                    <button class="btn btn-primary waves-effect waves-light save-btn d-none"><i class="bx bx-save align-middle font-size-14"></i> Simpan</button>
                                `;
                            }
                        }
                    ],
                    rowCallback: function(row, data) {
                        $(row).attr('data-brg-id', data.brg_id);
                    }
                });
            }

            $('#showEditDataBarang').on('click', function() {
                $('#editDataBarangModal').modal('show');

                if (!dataTableInitialized) {
                    initializeDataTable();
                    dataTableInitialized = true;
                }
            });

            $('#editDataBarangModal').on('click', '.edit-btn', function() {
                var $row = $(this).closest('tr');
                $row.find('.qty-value').addClass('d-none');
                $row.find('.qty-input').removeClass('d-none');
                $(this).addClass('d-none');
                $row.find('.save-btn').removeClass('d-none');
                $row.find('.cancel-btn').removeClass('d-none');
            });

            $('#editDataBarangModal').on('click', '.save-btn', function() {
                var $row = $(this).closest('tr');
                var qty_beli = $row.find('.qty-input').val();
                var brg_id = $row.data('brg-id');

                $.ajax({
                    url: "{{ route('stok-masuk.update', ['no_trm' => $no_trm]) }}",
                    method: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        no_trm: "{{ $no_trm }}",
                        items: [{
                            brg_id: brg_id,
                            qty_beli: qty_beli
                        }]
                    },
                    success: function(response) {
                        $row.find('.qty-value').text(qty_beli).removeClass('d-none');
                        $row.find('.qty-input').addClass('d-none');
                        $row.find('.save-btn').addClass('d-none');
                        $row.find('.edit-btn').removeClass('d-none');
                        $row.find('.cancel-btn').addClass('d-none');
                        Swal.fire({
                            toast: true,
                            position: 'top-right',
                            icon: response.success ? 'success' : 'error',
                            title: response.message,
                            showConfirmButton: false,
                            timer: 5000
                        });
                    },
                });
            });

            $('#editDataBarangModal').on('click', '.cancel-btn', function() {
                var $row = $(this).closest('tr');
                var originalQty = $row.find('.qty-value').text();
                $row.find('.qty-value').text(originalQty).removeClass('d-none');
                $row.find('.qty-input').addClass('d-none');
                $(this).addClass('d-none');
                $row.find('.save-btn').addClass('d-none');
                $row.find('.edit-btn').removeClass('d-none');
            });

            function checkFormChanges() {
                let isChanged = false;

                $('#updateForm input, #updateForm select').each(function() {
                    if ($(this).is(':visible') && $(this).data('original-value') !== $(this).val()) {
                        isChanged = true;
                    }
                });
            }

            $('#updateForm input, #updateForm select').each(function() {
                $(this).data('original-value', $(this).val());
            });

            $('#updateForm input, #updateForm select').on('input change', checkFormChanges);
        });

        document.getElementById('updateForm').addEventListener('submit', function(e) {
            e.preventDefault();

            var form = this;
            var formData = new FormData(form);

            fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-right'
                        }).then(() => {
                            window.location.href = "{{ route('stok-masuk') }}";
                        });
                    }
                });
        });
</script>
@endpush

@section('content')
<!-- Page Title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Edit Penerimaan Supplier</h4>
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
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Data Transaksi</h5>
                <form id="updateForm" action="{{ route('stok-masuk.update', ['no_trm' => $no_trm]) }}" method="post"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="form-group mt-3">
                        <label for="no_trm">No. Penerimaan :</label>
                        <input type="text" name="no_trm" id="no_trm" class="form-control" value="{{ $no_trms }}"
                            readonly>
                    </div>
                    <div class="form-group mt-3">
                        <label for="no_sj">No. SJ Supplier :</label>
                        <input type="text" class="form-control" name="no_sj"
                            placeholder="Masukkan No. Surat Jalan dari Supplier" value="{{ $no_sj }}">
                    </div>
                    <div class="form-group mt-3">
                        <label for="supplier_id">Nama Supplier :</label>
                        <select name="supplier_id" id="supplier_id" class="form-control" style="width: 100%;" required>
                            <option value="">Pilih Supplier</option>
                            @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->supplier_id }}" @if ($supplier->supplier_id == $data_supplier)
                                selected @endif>
                                {{ $supplier->nama }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mt-3">
                        <label for="tgl">Tanggal Terima :</label>
                        <input type="date" class="form-control" name="tgl" value="{{ $tgl }}" required>
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        <a href="{{ route('stok-masuk') }}" class="btn btn-secondary waves-effect waves-light me-2">
                            <i class="bx bx-caret-left align-middle me-2 font-size-18"></i>Kembali
                        </a>
                        <button type="submit" id="submitButton" class="btn btn-primary waves-effect waves-light">
                            <i class="bx bx bxs-save align-middle me-2 font-size-18"></i>Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-3">
            <button type="button" class="btn btn-dark waves-effect waves-light" id="showEditDataBarang"
                data-toggle="modal" data-target="#editDataBarangModal">
                <i class="bx bx-edit align-middle me-2 font-size-18"></i>Edit Data Barang
            </button>
        </div>
        {{-- <div>
            <button type="button" class="btn btn-dark waves-effect waves-light" id="showTambahDataBarang"
                data-toggle="modal" data-target="#tambahDataBarangModal">
                <i class="bx bx-plus-circle align-middle me-2 font-size-18"></i>Tambah Data Barang
            </button>
        </div> --}}
    </div>
</div>

<div class="modal fade" id="editDataBarangModal" tabindex="-1" role="dialog" aria-labelledby="editDataBarangModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editDataBarangModalLabel">Data Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="datatable-detail" class="table align-middle table-nowrap">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Nama Barang</th>
                                <th>Qty</th>
                                <th>Satuan</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection