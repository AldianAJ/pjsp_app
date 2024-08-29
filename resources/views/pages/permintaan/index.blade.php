@extends('layouts.app')

@section('title')
    Detail Permintaan Barang
@endsection

@push('after-style')
    <!-- DataTables -->
    <link href="{{ asset('backend/assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('backend/assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}" rel="stylesheet"
        type="text/css" />

    <!-- Responsive datatable examples -->
    <link href="{{ asset('backend/assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}"
        rel="stylesheet" type="text/css" />
    <!-- Sweet Alert-->
    <link href="{{ asset('backend/assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@push('after-script')
    <!-- Required datatable js -->
    <script src="{{ asset('backend/assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('backend/assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <!-- Buttons examples -->
    <script src="{{ asset('backend/assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('backend/assets/libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('backend/assets/libs/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('backend/assets/libs/pdfmake/build/pdfmake.min.js') }}"></script>
    <script src="{{ asset('backend/assets/libs/pdfmake/build/vfs_fonts.js') }}"></script>
    <script src="{{ asset('backend/assets/libs/datatables.net-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('backend/assets/libs/datatables.net-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('backend/assets/libs/datatables.net-buttons/js/buttons.colVis.min.js') }}"></script>

    <!-- Responsive examples -->
    <script src="{{ asset('backend/assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('backend/assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}">
    </script>
    <!-- Datatable init js -->

    <!-- Sweet Alerts js -->
    <script src="{{ asset('backend/assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>

    <!-- Sweet alert init js-->
    <script src="{{ asset('backend/assets/js/pages/sweet-alerts.init.js') }}"></script>

    <script>
        let permintaan BarangDatatable = $('#permintaan-datatable').DataTable({
            ajax: {
                url: "{{ route('permintaan-barang') }}",
            },
            columns: [{
                    data: "brg_id",
                    name: "brg_id"
                },
                {
                    data: "nm_brg",
                    name: "nm_brg"
                },
                {
                    data: "satuan_beli",
                    name: "satuan_beli"
                },
                {
                    data: "satuan_besar",
                    name: "satuan_besar"
                },
                {
                    data: "satuan_kecil",
                    name: "satuan_kecil"
                },
                {
                    data: "konver_besar",
                    name: "konver_besar"
                },
                {
                    data: "konver_kecil",
                    name: "konver_kecil"
                },
                {
                    data: "action",
                    name: "action"
                }
            ],
        });


        $("#permintaan Barang-datatable").on("click", ".btn-delete", function() {
            let selectedData = '';
            let brg_id = '';
            let indexRow = permintaan BarangDatatable.rows().nodes().to$().index($(this).closest('tr'));
            selectedData = permintaan BarangDatatable.row(indexRow).data();
            brg_id = selectedData.brg_id;
            Swal.fire({
                title: 'Apakah anda yakin?',
                text: "Menghapus permintaan Barang" + selectedData.nm_brg,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yakin, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '/admin/permintaan Barang/destroy/' + brg_id;
                }
            })
        });
    </script>
@endpush

@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Detail Permintaan Barang</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if (session()->has('message'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('message') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    <table id="permintaan-datatable" class="table table-bordered dt-responsive nowrap w-100">
                        <thead class="table-light">
                            <tr>
                                <th>No.</th>
                                <th>No. Dokumen</th>
                                <th>Nama Barang</th>
                                <th>Qty</th>
                                <th>Satuan</th>
                                <th>Keterangan</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>FPB/SKM/24/08/001</td>
                                <td>TBK Surabaya</td>
                                <td>100</td>
                                <td>kg</td>
                                <td>Barang di SKM sudah habis</td>
                                <td>
                                    <a href="#" class="btn btn-primary waves-effect waves-light mx-1">
                                        Persetujuan</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
