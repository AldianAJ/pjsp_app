@extends('layouts.app')

@section('title')
Target Mesin
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
    $('#datatableDetail').DataTable({
            ajax:{
                    url: "{{ route('kinerja-mesin.detail') }}",
                    type: "GET",
                    data: {shift_id: '{{ $_GET['shift_id'] }}'}
                },
            columns: [{
                    data: "msn_trgt_id"
                },
                {
                    data: "target_shift.target_hari.target_week.barang.nm_brg"
                },
                {
                    data: "target_shift.shift"
                },
                {
                    data: "mesin.nama"
                },
                {
                    data: "qty"
                }
            ],
            "dom": '<"wrapper"l<"toolbar">>ftp',
            initComplete: function () {
                this.api().columns([0]).every( function () {  // columns[0] sets the filter dropdown to only the rider type
                    var column = this;
                    var select = $('<select id="col3" class="form-control input-sm"><option value="">ID TARGET</option></select>').appendTo( ".toolbar" )
                    .on( 'change', function () {
                        var val = $.fn.dataTable.util.escapeRegex($(this).val());
                        column.search( val ? '^'+val+'$' : '', true, false ).draw();
                    } );
                    column.data().unique().sort().each( function ( d, j ) {
                        select.append( '<option value="'+d+'">'+d+'</option>' );
                    } );
                } );
            },

        });
</script>
@endpush

@section('content')
<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Target Mingguan</h4>
        </div>
    </div>
</div>
<!-- end page title -->

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-end mb-2">
                    <a href="{{ route('kinerja-mesin') }}" class="btn btn-info my-2">Kembali</a>
                </div>
                <div class="table-responsive">
                    <table id="datatableDetail" class="table align-middle table-nowrap">
                        <thead class="table-light">
                            <tr>
                                <th>Id</th>
                                <th>Barang</th>
                                <th>Shift</th>
                                <th>Mesin</th>
                                <th>Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" style="text-align:right">Total:</th>
                                <th></th>
                            </tr>
                        </tfoot>
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
