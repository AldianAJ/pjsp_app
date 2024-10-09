@extends('layouts.app')

@section('title')
Edit Pemakaian Spare Part
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
        var no_spart = "{{ $no_spart }}";
        var dataTableInitialized = false;

        function initializeDataTable() {
            $('#datatable-detail').DataTable({
                ajax: {
                    url: "{{ url('pemakaian-sparepart/edit') }}/" + no_spart,
                },
                lengthMenu: [5],
                columns: [{
                        data: null,
                        render: (data, type, row, meta) => meta.row + 1
                    },
                    {
                        data: "nm_brg",
                    },
                    {
                        data: "qty",
                        render: function(data, type, row) {
                            return `
                                <span class="qty-value" style="width: 5.5rem;">${data}</span>
                                <input type="text" inputmode="numeric" class="form-control qty-input d-none" style="width: 5.5rem;" value="${data}">
                            `;
                        }
                    },
                    {
                        data: "satuan",
                    },
                    {
                        data: "qty_std",
                        render: function(data, type, row) {
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
                    $(row).attr('data-spek-id', data.spek_id);
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

            $row.find('.qty-input').on('keyup', function() {
                var qty = parseFloat($(this).val()) || 0;
                var qtyStd = qty * konversi;
                $row.find('.qty-input-konversi').val(qtyStd);
            });

            $row.find('.qty-input-konversi').on('keyup', function() {
                var qtyStd = parseFloat($(this).val()) || 0;
                var qty = qtyStd / konversi;
                $row.find('.qty-input').val(qty);
            });
        });

        $('#editDataBarangModal').on('click', '.save-btn', function() {
            var $row = $(this).closest('tr');
            var qtyInput = $row.find('.qty-input');
            var qtyInputKonversi = $row.find('.qty-input-konversi');

            var qty = parseFloat(qtyInput.val());
            var qty_std = parseFloat(qtyInputKonversi.val());
            var spek_id = $row.data('spek-id');

            $.ajax({
                url: "{{ route('pemakaian-sparepart.update', ['no_spart' => $no_spart]) }}",
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    no_spart: "{{ $no_spart }}",
                    items: [{
                        spek_id: spek_id,
                        qty: qty,
                        qty_std: qty_std
                    }]
                },
                success: function(response) {
                    $row.find('.qty-value').text(qty).removeClass('d-none');
                    $row.find('.qty-value-konversi').text(qty_std).removeClass('d-none');
                    qtyInput.addClass('d-none');
                    qtyInputKonversi.addClass('d-none');
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
            var Qty = $row.find('.qty-value').text();
            var QtyStd = $row.find('.qty-value-konversi').text();
            $row.find('.qty-value').text(Qty).removeClass('d-none');
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
                    window.location.href = "{{ route('pemakaian-sparepart') }}";
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
            <h4 class="mb-sm-0 font-size-18">Edit Pemakaian Spare Part</h4>
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
                <form id="updateForm" action="{{ route('pemakaian-sparepart.update', ['no_spart' => $no_spart]) }}"
                    method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group mt-3">
                        <label for="no_spart">No. Mutasi :</label>
                        <input type="text" name="no_spart" id="no_spart" class="form-control" value="{{ $no_mutasi }}"
                            readonly>
                    </div>
                    <div class="form-group mt-3">
                        <label for="tgl">Tanggal Mutasi :</label>
                        <input type="date" class="form-control" name="tgl" value="{{ $tgl }}" required>
                    </div>
                    <div class="form-group mt-3">
                        <label for="msn_asal">Nama Mesin :</label>
                        <select name="msn_asal" id="msn_asal" class="form-control" style="width: 100%;" required>
                            <option value="">Pilih Mesin</option>
                            @foreach ($mesins as $mesin)
                            <option value="{{ $mesin->msn_asal }}" @if ($mesin->msn_asal == $data_mesin)
                                selected @endif>
                                {{ $mesin->nama }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        <a href="{{ route('pemakaian-sparepart') }}"
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
