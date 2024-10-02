@extends('layouts.app')

@section('title')
Kirim BJSK
@endsection

@push('after-app-style')
<!-- Sweet Alert-->
<link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
<link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker.min.css">
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js">
</script>

<script>
    let mainTable;

        mainTable = $('#datatable').DataTable({
            ajax: "{{ route('pengiriman-bjsk') }}",
            ordering: false,
            columns: [{
                    data: "mutasi_id"
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
                    data: "action"
                }
            ],
        });


        $('#datatable').on('click', '.btn-detail', function() {
            let selectedData = mainTable.row($(this).closest('tr')).data();
            $("#id-kirim-btg").text(selectedData.mutasi_id);

            if ($.fn.DataTable.isDataTable('#detail-datatable')) {
                $('#detail-datatable').DataTable().destroy();
            }
            $('#detail-datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    type: "GET",
                    url: "{{ route('pengiriman-bjsk.detail') }}",
                    data: {
                        mutasi_id: selectedData.mutasi_id
                    }
                },
                lengthMenu: [5],
                columns: [{
                        data: 'spek',
                        // render: function(data, type, row) {
                        //     return row.tr_trmsup_detail[0].barang.nm_brg;
                        // }
                    },
                    {
                        data: 'qty',
                        // render: function(data, type, row) {
                        //     return row.tr_trmsup_detail[0].qty;
                        // }
                    },
                    {
                        data: 'satuan',
                        // render: function(data, type, row) {
                        //     return row.tr_trmsup_detail[0].barang.satuan_beli;
                        // }
                    },
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
            <h4 class="mb-sm-0 font-size-18">Kirim BJSK</h4>
        </div>
    </div>
</div>
<!-- end page title -->

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-end mb-2">
                    <a href="{{ route('pengiriman-bjsk.create') }}" class="btn btn-primary my-2">
                        <i class="bx bx-plus-circle align-middle me-2 font-size-18"></i> Tambah
                    </a>
                </div>
                <div class="table-responsive">
                    <table id="datatable" class="table align-middle table-nowrap">
                        <thead class="table-light">
                            <tr>
                                <th>No. Pengiriman</th>
                                <th>Tanggal Kirim</th>
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
                <h3 class="modal-title fw-bolder" id="exampleModalLabel">Detail Barang - <span id="id-kirim-btg"></span>
                </h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
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