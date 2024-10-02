@extends('layouts.app')

@section('title')
Edit Pengiriman
@endsection

@push('after-app-style')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('after-app-script')
<script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/datatables.init.js') }}"></script>
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/autonumeric/4.10.5/autoNumeric.min.js"></script>

<script>
    $(document).ready(function() {
        var no_krmskm = "{{ $no_krmskm }}";
        var dataTableInitialized = false;

        function initializeDataTable() {
            $('#datatable-detail').DataTable({
                ajax: {
                    url: "{{ url('pengiriman-gudang-utama/edit') }}/" + no_krmskm,
                    data: {
                        type: 'details'
                    },
                },
                lengthMenu: [5],
                columns: [{
                        data: null,
                        render: (data, type, row, meta) => meta.row + 1
                    },
                    {
                        data: "nama",
                    },
                    {
                        data: "qty_beli",
                        render: function(data) {
                            return `
                                <span class="qty-value" style="width: 5.5rem;">${data}</span>
                                <input type="text" inputmode="numeric" class="form-control qty-input d-none" style="width: 5.5rem;" value="${data}">
                            `;
                        }
                    },
                    {
                        data: "satuan_beli",
                    },
                    {
                        data: "qty_std",
                        render: function(data) {
                            return `
                                <span class="qty-value-konversi" style="width: 5.5rem;">${data}</span>
                                <input type="text" inputmode="numeric" class="form-control qty-input-konversi d-none" style="width: 5.5rem;" value="${data}">
                            `;
                        }
                    },
                    {
                        data: "satuan_std"
                    },
                    {
                        data: null,
                        render: function() {
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
                    $(row).data('konversi1', data.konversi1);
                }
            });
        }

        $('#showDataBarangButton').on('click', function() {
            $('#editDataBarangModal').modal('show');

            if (!dataTableInitialized) {
                initializeDataTable();
                dataTableInitialized = true;
            }
        });

        $('#editDataBarangModal').on('click', '.edit-btn', function() {
            var $row = $(this).closest('tr');
            var konversi = parseFloat($row.data('konversi1'));

            $row.find('.qty-value').addClass('d-none');
            $row.find('.qty-value-konversi').addClass('d-none');
            $row.find('.qty-input').removeClass('d-none');
            $row.find('.qty-input-konversi').removeClass('d-none');
            $(this).addClass('d-none');
            $row.find('.save-btn').removeClass('d-none');
            $row.find('.cancel-btn').removeClass('d-none');

            $row.find('.qty-input').on('input', function() {
                var qtyBeli = parseFloat($(this).val()) || 0;
                var qtyStd = qtyBeli * konversi;
                $row.find('.qty-input-konversi').val(qtyStd);
            });

            $row.find('.qty-input-konversi').on('input', function() {
                var qtyStd = parseFloat($(this).val()) || 0;
                var qtyBeli = qtyStd / konversi;
                $row.find('.qty-input').val(qtyBeli);
            });
        });

        $('#editDataBarangModal').on('click', '.save-btn', function() {
            var $row = $(this).closest('tr');
            var qtyInput = $row.find('.qty-input');
            var qtyInputKonversi = $row.find('.qty-input-konversi');

            var qty_beli = parseFloat(qtyInput.val()) || 0;
            var qty_std = parseFloat(qtyInputKonversi.val()) || 0;
            var brg_id = $row.data('brg-id');

            $.ajax({
                url: "{{ route('pengiriman-gudang-utama.update', ['no_krmskm' => $no_krmskm]) }}",
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    no_krmskm: "{{ $no_krmskm }}",
                    items: [{
                        brg_id: brg_id,
                        qty_beli: qty_beli,
                        qty_std: qty_std
                    }]
                },
                success: function(response) {
                    $row.find('.qty-value').text(qty_beli).removeClass('d-none');
                    $row.find('.qty-value-konversi').text(qty_std).removeClass('d-none');
                    $row.find('.qty-input').addClass('d-none');
                    $row.find('.qty-input-konversi').addClass('d-none');
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
            var QtyBeli = $row.find('.qty-value').text();
            var QtyStd = $row.find('.qty-value-konversi').text();
            $row.find('.qty-value').text(QtyBeli).removeClass('d-none');
            $row.find('.qty-value-konversi').text(QtyStd).removeClass('d-none');
            $row.find('.qty-input').addClass('d-none');
            $row.find('.qty-input-konversi').addClass('d-none');
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
                        window.location.href = "{{ route('pengiriman-gudang-utama') }}";
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
            <h4 class="mb-sm-0 font-size-18">Edit Proses Permintaan</h4>
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
                <h3 class="card-title fw-bolder">Data Transaksi</h3>
                <form id="updateForm"
                    action="{{ route('pengiriman-gudang-utama.update', ['no_krmskm' => $no_krmskm]) }}" method="post"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="form-group mt-3">
                        <label for="no_krmskm">No. Pengiriman :</label>
                        <input type="text" name="no_krmskm" id="no_krmskm" class="form-control" value="{{ $no_krm }}"
                            readonly>
                    </div>
                    <div class="form-group mt-3">
                        <label for="no_reqskm">No. Permintaan :</label>
                        <input type="text" name="no_reqskm" id="no_reqskm" class="form-control" value="{{ $no_req }}"
                            readonly>
                    </div>
                    <div class="form-group mt-3">
                        <label for="tgl_krm">Tanggal Pengiriman :</label>
                        <input type="date" class="form-control" name="tgl_krm" value="{{ $tgl_krm }}" required>
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        <a href="{{ route('pengiriman-gudang-utama') }}"
                            class="btn btn-secondary waves-effect waves-light me-2">
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
    <div class="col-md-6">
        <button type="button" class="btn btn-dark waves-effect waves-light" id="showDataBarangButton"
            data-toggle="modal" data-target="#editDataBarangModal">
            <i class="bx bx-edit align-middle me-2 font-size-18"></i>Edit Data Barang
        </button>
    </div>
</div>

<div class="modal fade modal-lg" id="editDataBarangModal" tabindex="-1" role="dialog"
    aria-labelledby="editDataBarangModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title fw-bolder" id="editDataBarangModalLabel">Data Barang</h3>
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
                                <th>Qty Konversi</th>
                                <th>Satuan Konversi</th>
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