@extends('layouts.app')

@section('title')
    Detail History Pengiriman Barang
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
        let pengiriman BarangDatatable = $('#pengiriman-datatable').DataTable({
            ajax: {
                url: "{{ route('pengiriman-barang.history') }}",
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


        $("#pengiriman Barang-datatable").on("click", ".btn-delete", function() {
            let selectedData = '';
            let brg_id = '';
            let indexRow = pengiriman BarangDatatable.rows().nodes().to$().index($(this).closest('tr'));
            selectedData = pengiriman BarangDatatable.row(indexRow).data();
            brg_id = selectedData.brg_id;
            Swal.fire({
                title: 'Apakah anda yakin?',
                text: "Menghapus pengiriman Barang" + selectedData.nm_brg,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yakin, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '/admin/pengiriman Barang/destroy/' + brg_id;
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
                <h4 class="mb-sm-0 font-size-18">Detail Pengiriman Barang</h4>
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
                    <table id="pengiriman-datatable" class="table table-bordered dt-responsive nowrap w-100">
                        <thead class="table-light">
                            <tr>
                                <th>No.</th>
                                <th>No. Dokumen Kirim</th>
                                <th>Dikirim dari</th>
                                <th>Dikirim ke</th>
                                <th>Nama Barang</th>
                                <th>Qty</th>
                                <th>Satuan</th>
                                <th>Persetujuan</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>SJ/GU001/24/08/001</td>
                                <td>Gudang</td>
                                <td>SKM</td>
                                <td>TBK Surabaya</td>
                                <td>100</td>
                                <td>kg</td>
                                <td>Disetujui</td>
                                <td>-</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
