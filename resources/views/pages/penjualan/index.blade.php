@extends('layouts.app')

@section('title')
Surat Jalan
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

    mainTable = $('#datatable').DataTable({
            ajax: "{{ route('penjualan') }}",
            ordering: false,
            columns: [{
                    data: "no_sj"
                },
                {
                    data: "tgl",
                    render: function(data) {
                        return new Date(data).toLocaleDateString('id-ID', {
                            day: 'numeric',
                            month: 'long',
                            year: 'numeric'
                        });
                    }
                },
                {
                    data: "no_po"
                },
                {
                    data: "no_segel"
                },
                {
                    data: "no_pol"
                },
                {
                    data: "driver"
                },
                {
                    data: "action"
                }
            ],
        });

    $('#datatable').on('click', '.btn-detail', function() {
        let selectedData = mainTable.row($(this).closest('tr')).data();
        $("#id-terima").text(selectedData.no_sj);
        $("#id-supplier").text(selectedData.supplier.nama);

        if ($.fn.DataTable.isDataTable('#detail-datatable')) {
            $('#detail-datatable').DataTable().destroy();
        }
        $('#detail-datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                type: "GET",
                url: "{{ route('penjualan.detail') }}",
                data: {
                    no_sj: selectedData.no_sj
                }
            },
            lengthMenu: [5],
            columns: [
                { data: 'nm_brg' },
                { data: 'qty' },
                { data: 'satuan' },
            ],
        });

        $('#detailModal').modal('show');
    });
</script>
@endpush

@section('content')
<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Surat Jalan</h4>
        </div>
    </div>
</div>
<!-- end page title -->

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-end mb-2">
                    <a href="{{ route('penjualan.create') }}" class="btn btn-primary my-2">
                        <i class="bx bx-plus-circle align-middle me-2 font-size-18"></i> Tambah
                    </a>
                </div>

                <!-- Table -->
                <div class="table-responsive">
                    <table id="datatable" class="table align-middle table-nowrap">
                        <thead class="table-light">
                            <tr>
                                <th>No. SJ</th>
                                <th>Tanggal</th>
                                <th>No. PO</th>
                                <th>No. Segel</th>
                                <th>Armada</th>
                                <th>Driver</th>
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