@extends('layouts.app')

@section('title')
Penerimaan Supplier
@endsection

@push('after-app-style')
<!-- Sweet Alert-->
<link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
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

<script>
    let mainTable;

    $(document).ready(function() {
        mainTable = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('stok-masuk') }}",
                data: function(d) {
                    d.supplier_id = $('#filterSupplier').val();

                    let selectedMonthYear = $('#filterMonthYear').val();
                    if (selectedMonthYear) {
                        const [year, month] = selectedMonthYear.split('-');
                        d.selected_month = month;
                        d.selected_year = year;
                    } else {
                        d.selected_month = '';
                        d.selected_year = '';
                    }

                    console.log('Sending:', d); // Debugging
                }
            },
            ordering: false,
            columns: [
                { data: 'no_trm' },
                { data: 'no_sj' },
                { data: 'supplier.nama' },
                {
                    data: 'tgl',
                    render: function(data) {
                        let date = new Date(data);
                        return date.toLocaleDateString('id-ID', {
                            day: 'numeric',
                            month: 'long',
                            year: 'numeric'
                        });
                    }
                },
                { data: 'action' }
            ],
            destroy: true
        });

        $('#filterSupplier').on('change', function() {
            mainTable.draw();
        });

        $('#filterMonthYear').on('change', function() {
            mainTable.draw();
        });

        mainTable.draw();
    });

    $('#datatable').on('click', '.btn-detail', function() {
        let selectedData = mainTable.row($(this).closest('tr')).data();
        $("#id-terima").text(selectedData.no_trm);
        $("#id-supplier").text(selectedData.supplier.nama);

        if ($.fn.DataTable.isDataTable('#detail-datatable')) {
            $('#detail-datatable').DataTable().destroy();
        }
        $('#detail-datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                type: "GET",
                url: "{{ route('stok-masuk.detail') }}",
                data: {
                    no_trm: selectedData.no_trm
                }
            },
            lengthMenu: [5],
            columns: [
                { data: 'nm_brg' },
                { data: 'qty_beli' },
                { data: 'satuan_beli' },
            ],
        });

        $('#detailModal').modal('show');
    });

    $(document).ready(function() {
        $('#filterSupplier').select2({
            width: 'resolve'
        });
    });
</script>
@endpush

@section('content')
<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Penerimaan Supplier</h4>
        </div>
    </div>
</div>
<!-- end page title -->

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-end mb-2">
                    <a href="{{ route('stok-masuk.create') }}" class="btn btn-primary my-2">
                        <i class="bx bx-plus-circle align-middle me-2 font-size-18"></i> Tambah
                    </a>
                </div>

                <!-- Filters -->
                <div class="row mb-3">
                    <div class="col-md-4 mb-3">
                        <label for="filterSupplier">Nama Supplier :</label>
                        <select id="filterSupplier" class="form-control" style="width: 100%">
                            <option value="">-- Pilih Supplier --</option>
                            @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->supplier_id }}">{{ $supplier->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="filterMonthYear">Tanggal :</label>
                        <div class="input-group" id="datepicker2">
                            <input type="month" id="filterMonthYear" class="form-control"
                                placeholder="-- Pilih Tanggal --" autocomplete="off" />
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="table-responsive">
                    <table id="datatable" class="table align-middle table-nowrap">
                        <thead class="table-light">
                            <tr>
                                <th>No. Penerima</th>
                                <th>No. SJ Supplier</th>
                                <th>Nama Supplier</th>
                                <th>Tanggal Terima</th>
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

<div class="modal modal-md fade" id="detailModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Detail Barang - <span id="id-terima"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="font-size-14 mb-2">
                    <strong>Nama Supplier :</strong> <span id="id-supplier"></span>
                </div>
                <table class="table table-bordered dt-responsive nowrap w-100" id="detail-datatable">
                    <thead class="table-light">
                        <tr>
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
    </div>
</div>

@if (session()->has('success'))
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            toast: true,
            position: 'top-right',
            icon: 'success',
            title: '{{ session('success') }}',
            showConfirmButton: false,
            timer: 5000,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            },
            customClass: {
                popup: 'colored-toast'
            },
            showCloseButton: true
        });
    });
</script>
@endif
@endsection