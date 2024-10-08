@extends('layouts.app')

@section('title')
Penerimaan Barang
@endsection

@push('after-app-style')
<style>
    .check-barang {
        width: 18px;
        height: 18px;
    }
</style>
@endpush

@push('after-app-script')
<script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/datatables.init.js') }}"></script>
<script>
    var no_krmskm = "{{ $no_krmskm }}";
    let selectedBarang = [];

    const saveButton = document.querySelector('button[type="submit"]');
    saveButton.disabled = true;

    $('#showDataCheckButton').on('click', function() {
        if ($.fn.DataTable.isDataTable('#datatable-check')) {
            selectedBarang = [];
            $('#datatable-check input.check-barang:checked').each(function() {
                selectedBarang.push($(this).val());
            });
            $('#datatable-check').DataTable().clear().destroy();
        }

        $('#datatable-check').DataTable({
            ajax: {
                url: "{{ url('penerimaan-barang/create') }}/" + no_krmskm,
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
                    data: "qty_minta"
                },
                {
                    data: "qty_kirim"
                },
                {
                    data: "satuan_beli"
                },
                {
                    data: null,
                    render: (data, type, row) => {
                        let checked = selectedBarang.includes(row.brg_id) ? 'checked' : '';
                        return `<input type="checkbox" class="check-barang" value="${row.brg_id}" ${checked}>`;
                    }
                }
            ],
        });
        $('#dataCheck').modal('show');
    });

    $('#datatable-check').on('click', '.check-barang', function() {
        let barangId = $(this).val();
        if ($(this).prop('checked')) {
            selectedBarang.push(barangId);
        } else {
            selectedBarang = selectedBarang.filter(item => item !== barangId);
        }
        toggleSaveButton();
    });

    function toggleSaveButton() {
        saveButton.disabled = selectedBarang.length === 0;
    }

    $('form').on('submit', function(e) {
        $('#items-container').empty();
        selectedBarang.forEach(function(barangId) {
            $('#items-container').append('<input type="hidden" name="brg_id[]" value="' + barangId + '">');
        });
    });
</script>
@endpush

@section('content')
<!-- Page Title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Penerimaan Barang</h4>
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
                <form action="{{ route('penerimaan-barang.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group mt-3">
                        <label for="no_krmskm">No. Dokumen Pengiriman :</label>
                        <input type="text" name="no_krmskm" id="no_krmskm" class="form-control" value="{{ $no_krm }}"
                            readonly>
                    </div>
                    <div class="form-group mt-3">
                        <label for="no_reqskm">No. Dokumen Permintaan :</label>
                        <input type="text" name="no_reqskm" id="no_reqskm" class="form-control" value="{{ $no_req }}"
                            readonly>
                    </div>
                    <div class="form-group mt-3">
                        <label for="tgl_trm">Tanggal Penerimaan :</label>
                        <input type="date" class="form-control" name="tgl_trm"
                            value="{{ old('tgl_trm', \Carbon\Carbon::now()->format('Y-m-d')) }}" required>
                    </div>
                    <input type="hidden" name="user_id" id="user_id" value="{{ old('user_id', $user_id ?? '') }}">
                    <div id="items-container"></div>
                    <div class="d-flex justify-content-end mt-3">
                        <a href="{{ route('penerimaan-barang') }}"
                            class="btn btn-secondary waves-effect waves-light me-2">
                            <i class="bx bx-caret-left align-middle me-2 font-size-18"></i>Kembali
                        </a>
                        <button type="submit" class="btn btn-primary waves-effect waves-light"><i
                                class="bx bx bxs-save align-middle me-2 font-size-18"></i>Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <button type="button" class="btn btn-dark waves-effect waves-light" id="showDataCheckButton">
                <i class="bx bx-check-circle align-middle me-2 font-size-18"></i>Terima Data Barang
            </button>
        </div>
    </div>
</div>

<div class="modal fade" id="dataCheck" tabindex="-1" role="dialog" aria-labelledby="dataModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title fw-bolder" id="dataModalLabel">Data Penerimaan</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="table-responsive">
                            <table id="datatable-check" class="table align-middle table-nowrap">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Barang</th>
                                        <th>Qty Minta</th>
                                        <th>Qty Kirim</th>
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
@endsection