@extends('layouts.app')

@section('title')
History Permintaan ke Gudang Besar
@endsection

@push('after-style')
<!-- Sweet Alert-->
<link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />

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
                ajax: "{{ route('permintaan-skm.history') }}",
                columns: [{
                        data: "no_reqskm"
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
                $("#id-req").text(selectedData.no_reqskm);

                if ($.fn.DataTable.isDataTable('#detail-datatable')) {
                    $('#detail-datatable').DataTable().destroy();
                }
                $('#detail-datatable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        type: "GET",
                        url: "{{ route('permintaan-skm.showDetailHistory') }}",
                        data: {
                            no_reqskm: selectedData.no_reqskm
                        }
                    },
                    lengthMenu: [5],
                    columns: [{
                            data: 'nm_brg',
                            // render: function(data, type, row) {
                            //     return row.tr_trmsup_detail[0].barang.nm_brg;
                            // }
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
            <h4 class="mb-sm-0 font-size-18">History Permintaan ke Gudang Besar</h4>
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
                                <th>Tanggal Permintaan</th>
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
                <h5 class="modal-title" id="exampleModalLabel">Detail Barang - <span id="id-req"></span></h5>
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
@endsection
