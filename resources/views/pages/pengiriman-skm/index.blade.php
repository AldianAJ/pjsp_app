@extends('layouts.app')

@section('title')
Pengiriman ke Mesin
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
    $('#datatable').DataTable({
        ajax: "{{ route('pengiriman-skm') }}",
        processing: true,
        ordering: false,
        bDestroy: true,
        columns: [{
                data: "mutasi_id",
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
            let selectedData = $('#datatable').DataTable().row($(this).closest('tr')).data();
            $("#id-kirim-skm").text(selectedData.mutasi_id);

            if ($.fn.DataTable.isDataTable('#detail-datatable')) {
                    $('#detail-datatable').DataTable().clear().destroy();
                }
            $('#detail-datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    type: "GET",
                    url: "{{ route('pengiriman-skm.Detail') }}",
                    data: {
                        mutasi_id: selectedData.mutasi_id
                    }
                },
                ordering: false,
                processing: true,
                lengthMenu: [5],
                columns: [{
                        data: 'nm_brg',
                },
                    {
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
            <h4 class="mb-sm-0 font-size-18">Pengiriman ke Mesin</h4>
        </div>
    </div>
</div>
<!-- end page title -->

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-end mb-2">
                    <a href="{{ route('pengiriman-skm.create') }}" class="btn btn-primary my-2"><i
                            class="bx bx-plus-circle align-middle me-2 font-size-18"></i> Tambah</a>
                </div>
                <div class="table-responsive">
                    <table id="datatable" class="table align-middle table-nowrap">
                        <thead class="table-light">
                            <tr>
                                <th>No. Dokumen</th>
                                <th>Tanggal Pengiriman</th>
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

<div class="modal modal-md fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="detailModalLabel">Detail Permintaan - <span id="id-kirim-skm"></span></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table align-middle table-nowrap" id="detail-datatable">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Barang</th>
                                <th>Spek Barang</th>
                                <th>Qty</th>
                                <th>Satuan</th>
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


@if (session()->has('success'))
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        toast: true,
                        position: 'bottom-right',
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