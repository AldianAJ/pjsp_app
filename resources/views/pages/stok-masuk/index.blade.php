@extends('layouts.app')

@section('title')
    Stok Masuk
@endsection

@push('after-app-style')
    <!-- Sweet Alert-->
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker.min.css">
@endpush

@push('after-app-script')
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/datatables.init.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js">
    </script>

    <script>
        $(document).ready(function() {
            $('#filterMonthYear').datepicker({
                format: "mm-yyyy",
                autoclose: true,
                minViewMode: 1,
                todayHighlight: true,
                clearBtn: true,
                todayBtn: "linked",
                orientation: "bottom auto",
                templates: {
                    leftArrow: '&laquo;',
                    rightArrow: '&raquo;'
                }
            }).on('changeDate', function(e) {
                $('#filterSupplier').trigger('change');
            });

            var today = new Date();
            var currentMonth = ('0' + (today.getMonth() + 1)).slice(-2);
            var currentYear = today.getFullYear();
            $('#filterMonthYear').datepicker('update', new Date(currentYear, today.getMonth(), 1));

            var table = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('stok-masuk') }}",
                    data: function(d) {
                        d.supplier_id = $('#filterSupplier').val();

                        var selectedMonthYear = $('#filterMonthYear').datepicker('getDate');
                        if (selectedMonthYear) {
                            var month = ('0' + (selectedMonthYear.getMonth() + 1)).slice(-2);
                            var year = selectedMonthYear.getFullYear();
                            d.selected_month = month;
                            d.selected_year = year;
                        } else {
                            d.selected_month = '';
                            d.selected_year = '';
                        }
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
                            var date = new Date(data);
                            return date.toLocaleDateString('id-ID', {
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
            table.draw();
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
                            <label for="filterMonthYear">Tanggal:</label>
                            <div class="input-group" id="datepicker2">
                                <input type="text" id="filterMonthYear" class="form-control"
                                    placeholder="-- Pilih Tanggal --" autocomplete="off" />
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
