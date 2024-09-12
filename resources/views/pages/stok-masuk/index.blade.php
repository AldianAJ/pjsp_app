@extends('layouts.app')

@section('title')
    Stok Masuk
@endsection

@push('after-style')
    <!-- Sweet Alert-->
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Datepicker CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-datepicker@1.9.0/dist/css/bootstrap-datepicker.min.css"
        rel="stylesheet" />
@endpush

@push('after-app-script')
    <!-- Scripts for DataTables and Datepicker -->
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/datatables.init.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-datepicker@1.9.0/dist/js/bootstrap-datepicker.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#filterDate').datepicker({
                format: "yyyy-mm-dd",
                autoclose: true,
                todayHighlight: true,
                clearBtn: true,
                todayBtn: "linked",
                orientation: "bottom auto"
            }).on('changeDate', function(e) {
                $('#filterSupplier').trigger('change');
            });

            var table = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('stok-masuk') }}",
                    data: function(d) {
                        d.supplier_id = $('#filterSupplier').val();
                        d.selected_date = $('#filterDate').val();
                    }
                },
                columns: [{
                        data: 'no_trm'
                    },
                    {
                        data: 'no_sj'
                    },
                    {
                        data: 'supplier.nama'
                    },
                    {
                        data: 'tgl',
                        render: function(data) {
                            return new Date(data).toLocaleDateString('id-ID', {
                                day: 'numeric',
                                month: 'long',
                                year: 'numeric'
                            });
                        }
                    },
                    {
                        data: 'action'
                    }
                ],
                destroy: true
            });

            $('#filterSupplier').on('change', function() {
                table.draw();
            });

        });
    </script>
@endpush

@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Stok Masuk</h4>
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
                        <div class="col-md-4">
                            <label for="filterSupplier">Nama Supplier:</label>
                            <select id="filterSupplier" class="form-control" style="width: 100%">
                                <option value="">-- Pilih Supplier --</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->supplier_id }}">{{ $supplier->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="filterDate">Tanggal:</label>
                            <div class="input-group" id="datepicker2">
                                <input type="text" id="filterDate" class="form-control" placeholder="-- Pilih Tanggal --"
                                    autocomplete="off" />
                                <span class="input-group-text">
                                    <i class="mdi mdi-calendar"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table id="datatable" class="table align-middle table-nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th>No. Dokumen</th>
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
@endsection
