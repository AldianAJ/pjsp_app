@extends('layouts.app')

@section('title')
Stok
@endsection

@push('after-app-style')
<link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" />
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
        const $gudangIdInput = $('#gudang_id');
        const $brgIdInput = $('#brg_id');
        $('#stockDataRow').hide();

        $('#showDataGudangButton').on('click', function() {
            $('#datatable-gudang').DataTable({
                ajax: {
                    url: "{{ route('stok') }}",
                    data: {
                        type: 'gudangs'
                    }
                },
                lengthMenu: [5],
                columns: [
                    { data: null, render: (data, type, row, meta) => meta.row + 1 },
                    { data: "nama" },
                    {
                        data: null,
                        render: (data, type, row) => `
                            <button type="button" class="btn btn-primary" onclick="selectGudang('${row.gudang_id}')">Pilih</button>`
                    }
                ]
            });
            $('#dataGudang').modal('show');
        });

        window.selectGudang = function(gudangId) {
            $gudangIdInput.val(gudangId);
            $('#dataGudang').modal('hide');
            showBarangModal();
        }

        function showBarangModal() {
            if ($.fn.dataTable.isDataTable('#datatable-barang')) {
                $('#datatable-barang').DataTable().clear().destroy();
            }

            $('#datatable-barang').DataTable({
                ajax: {
                    url: "{{ route('stok') }}",
                    data: {
                        type: 'barangs'
                    }
                },
                lengthMenu: [5],
                columns: [
                    { data: null, render: (data, type, row, meta) => meta.row + 1 },
                    { data: "nm_brg" },
                    {
                        data: null,
                        render: (data, type, row) => `
                            <button type="button" class="btn btn-primary" onclick="selectBarang('${row.brg_id}')">Pilih</button>`
                    }
                ]
            });
            $('#dataBarang').modal('show');
        }

        window.selectBarang = function(barangId) {
            $brgIdInput.val(barangId);
            $('#dataBarang').modal('hide');
            loadStockData();
        }

        function loadStockData() {
            let gudangId = $gudangIdInput.val();
            let brgId = $brgIdInput.val();

            if ($.fn.dataTable.isDataTable('#datatable')) {
                $('#datatable').DataTable().clear().destroy();
            }

            $('#datatable').DataTable({
                ajax: {
                    url: "{{ route('stok') }}",
                    data: {
                        type: 'data_stoks',
                        gudang_id: gudangId,
                        brg_id: brgId
                    }
                },
                columns: [
                    { data: null, render: (data, type, row, meta) => meta.row + 1 },
                    { data: "tgl", render: (data) => new Date(data).toLocaleDateString('id-ID') },
                    { data: "nm_brg" },
                    { data: "doc_id" },
                    { data: "ket" },
                    { data: "akhir" },
                ],
                initComplete: function() {
                    $('#stockDataRow').show();
                }
            });
        }
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

<div class="mb-4">
    <button type="button" class="btn btn-dark waves-effect waves-light" id="showDataGudangButton">
        <i class="bx bx-plus-circle align-middle me-2 font-size-18"></i>Pilih Gudang
    </button>
</div>

<!-- Hidden Inputs -->
<input type="hidden" id="gudang_id" value="">
<input type="hidden" id="brg_id" value="">

<!-- Gudang Modal -->
<div class="modal fade" id="dataGudang" tabindex="-1" role="dialog" aria-labelledby="dataModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="dataModalLabel">Data Gudang</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="datatable-gudang" class="table align-middle table-nowrap">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Nama Gudang</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Barang Modal -->
<div class="modal fade" id="dataBarang" tabindex="-1" role="dialog" aria-labelledby="dataModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="dataModalLabel">Data Barang</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="datatable-barang" class="table align-middle table-nowrap">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Nama Barang</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row" id="stockDataRow" style="display: none;">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
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