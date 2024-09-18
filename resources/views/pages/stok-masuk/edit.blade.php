@extends('layouts.app')

@section('title')
    Edit Stok Masuk
@endsection

@push('after-app-script')
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/datatables.init.js') }}"></script>
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>

    <script>
        var no_trm = "{{ $no_trm }}";

        $('#datatable-barang').DataTable({
            ajax: {
                url: "{{ url('stok-masuk/edit') }}/" + no_trm,
            },
            lengthMenu: [5],
            columns: [{
                    data: "barang.nm_brg"
                },
                {
                    data: "qty",
                    render: function(data, type, row) {
                        return `
                            <span class="qty-value" style="width: 5.5rem;">${data}</span>
                            <input type="number" class="form-control qty-input d-none" style="width: 5.5rem;" value="${data}">
                        `;
                    }
                },
                {
                    data: "satuan_beli"
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

        $('#datatable-barang').on('click', '.edit-btn', function() {
            var $row = $(this).closest('tr');
            $row.find('.qty-value').addClass('d-none');
            $row.find('.qty-input').removeClass('d-none');
            $(this).addClass('d-none');
            $row.find('.save-btn').removeClass('d-none');
            $row.find('.cancel-btn').removeClass('d-none'); // Show the Cancel button
        });

        $('#datatable-barang').on('click', '.save-btn', function() {
            var $row = $(this).closest('tr');
            var qty = $row.find('.qty-input').val();
            var brg_id = $row.data('brg-id');

            $.ajax({
                url: "{{ route('stok-masuk.update', ['no_trm' => $no_trm]) }}",
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    no_trm: "{{ $no_trm }}",
                    items: [{
                        brg_id: brg_id,
                        qty: qty
                    }]
                },
                success: function(response) {
                    $row.find('.qty-value').text(qty).removeClass('d-none');
                    $row.find('.qty-input').addClass('d-none');
                    $row.find('.save-btn').addClass('d-none');
                    $row.find('.edit-btn').removeClass('d-none');
                    $row.find('.cancel-btn').addClass('d-none'); // Hide the Cancel button
                    Swal.fire({
                        toast: true,
                        position: 'bottom-right',
                        icon: response.success ? 'success' : 'error',
                        title: response.message,
                        showConfirmButton: false,
                        timer: 5000
                    });
                },
            });
        });

        $('#datatable-barang').on('click', '.cancel-btn', function() {
            var $row = $(this).closest('tr');
            var originalQty = $row.find('.qty-value').text(); // Get original quantity
            $row.find('.qty-value').text(originalQty).removeClass('d-none');
            $row.find('.qty-input').addClass('d-none');
            $(this).addClass('d-none'); // Hide the Cancel button
            $row.find('.save-btn').addClass('d-none'); // Hide the Save button
            $row.find('.edit-btn').removeClass('d-none'); // Show the Edit button
        });
    </script>
@endpush

@section('content')
    <!-- Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Edit Stok Masuk</h4>
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
                    <form action="{{ route('stok-masuk.update', ['no_trm' => $no_trm]) }}" method="post"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="form-group mt-3">
                            <label for="no_trm">No. Penerimaan :</label>
                            <input type="text" name="no_trm" id="no_trm" class="form-control"
                                value="{{ $no_trm }}" readonly>
                        </div>
                        <div class="form-group mt-3">
                            <label for="no_sj">No. SJ Supplier :</label>
                            <input type="text" class="form-control" name="no_sj" value="{{ $no_sj }}">
                        </div>
                        <div class="form-group mt-3">
                            <label for="supplier_id">Nama Supplier </label>
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
                            <label for="tgl">Tanggal Terima :</label>
                            <input type="date" class="form-control" name="tgl" value="{{ $tgl }}" required>
                        </div>
                        <div id="items-container"></div> <!-- Container for items input fields -->
                        <div class="d-flex justify-content-end mt-3">
                            <a href="{{ route('stok-masuk') }}" class="btn btn-secondary waves-effect waves-light me-2">
                                <i class="bx bx-caret-left align-middle me-2 font-size-18"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-primary waves-effect waves-light">
                                <i class="bx bx bxs-save align-middle me-2 font-size-18"></i>Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">Data Barang</h4>
                    <div class="table-responsive">
                        <table id="datatable-barang" class="table align-middle table-nowrap">
                            <thead class="table-light">
                                <tr>
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
            </div>
        </div>
    </div>

@endsection
