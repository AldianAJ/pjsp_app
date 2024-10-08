@extends('layouts.app')

@section('title')
Penerimaan Barang
@endsection

@push('after-style')
<!-- Sweet Alert-->
<link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@push('after-app-script')
<script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<!-- Responsive examples -->
<script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/datatables.init.js') }}"></script>
<script>
    let mainTable;

        mainTable = $('#datatable').DataTable({
            ajax: "{{ route('penerimaan-barang') }}",
            ordering: false,
            columns: [{
                    data: "no_krmskm"
                },
                {
                    data: "tgl_krm",
                    render: function(data) {
                        return new Date(data).toLocaleDateString('id-ID', {
                            day: 'numeric',
                            month: 'long',
                            year: 'numeric'
                        });
                    }
                },
                {
                    data: "tgl_trm",
                    render: function(data) {
                        if (!data) {
                            return '-';
                        }
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
            $("#id-krm").text(selectedData.no_krmskm);

            if ($.fn.DataTable.isDataTable('#detail-datatable')) {
                $('#detail-datatable').DataTable().destroy();
            }
            $('#detail-datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    type: "GET",
                    url: "{{ route('penerimaan-barang.terimaDetail') }}",
                    data: {
                        no_krmskm: selectedData.no_krmskm
                    }
                },
                lengthMenu: [5],
                columns: [{
                        data: null,
                        render: (data, type, row, meta) => meta.row + 1
                    },
                    {
                        data: 'nm_brg',
                        // render: function(data, type, row) {
                        //     return row.tr_trmsup_detail[0].barang.nm_brg;
                        // }
                    },
                    {
                        data: 'spek'
                    },
                    {
                        data: 'qty_beli',
                        // render: function(data, type, row) {
                        //     return row.tr_trmsup_detail[0].qty;
                        // }
                    },
                    {
                        data: 'satuan_beli',
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
            <h4 class="mb-sm-0 font-size-18">Penerimaan Barang</h4>
        </div>
    </div>
</div>
<!-- end page title -->

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="datatable" class="table align-middle table-nowrap">
                        <thead class="table-light">
                            <tr>
                                <th>No. Dokumen</th>
                                <th>Tanggal Pengiriman</th>
                                <th>Tanggal Penerimaan</th>
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

<div class="modal modal-lg fade" id="detailModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title fw-bolder" id="exampleModalLabel">Detail Barang - <span id="id-krm"></span>
                </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table align-middle table-nowrap" id="detail-datatable">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Nama Barang</th>
                                <th>Spek</th>
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