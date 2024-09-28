@extends('layouts.app')

@section('title')
    Stok
@endsection

@push('after-app-style')
    <!-- Sweet Alert-->
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css"
        rel="stylesheet" />
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="{{ asset('assets/js/pages/datatables.init.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('#filterBarang').select2({
                width: 'resolve'
            });

            $('#datepicker2 input').datepicker({
                format: 'dd-mm-yyyy',
                autoclose: true,
                todayHighlight: true,
                clearBtn: true,
                todayBtn: 'linked'
            });

            $('#filterBarang, #filterDate').on('change', function() {
                var filterValue = $('#filterBarang').val();
                var dateValue = $('#filterDate').val();
                var table = $('#datatable').DataTable();
                table.ajax.url("{{ route('stok') }}?brg_id=" + filterValue + "&date=" + dateValue).load();
            });

            $(document).ready(function() {
                if ($.fn.dataTable.isDataTable('#datatable')) {
                    $('#datatable').DataTable().destroy();
                }

                $('#datatable').DataTable({
                    ajax: "{{ route('stok') }}",
                    ordering: false,
                    columns: [{
                            data: null,
                            render: (data, type, row, meta) => meta.row + 1
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
                            data: "nm_brg"
                        },
                        {
                            data: "doc_id"
                        },
                        {
                            data: "ket"
                        },
                        {
                            data: "akhir"
                        },
                    ],
                });
            });
        });
    </script>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Stok</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4 mb-3">
                            <label for="filterBarang">Nama Barang :</label>
                            <select id="filterBarang" class="form-select" style="width: 100%">
                                <option value="">-- Pilih Barang --</option>
                                @foreach ($all_barang as $barang)
                                    <option value="{{ $barang->brg_id }}">{{ $barang->nm_brg }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="filterDate">Tanggal :</label>
                            <div class="input-group" id="datepicker2">
                                <input type="text" id="filterDate" class="form-control" placeholder="-- Pilih Tanggal --"
                                    autocomplete="off" />
                                <span class="input-group-text">
                                    <i class="mdi mdi-calendar"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="datatable" class="table align-middle table-nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Nama Barang</th>
                                    <th>No. Dokumen</th>
                                    <th>Keterangan</th>
                                    <th>Saldo</th>
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
