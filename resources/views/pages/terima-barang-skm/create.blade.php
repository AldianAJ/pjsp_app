@extends('layouts.app')

@section('title')
    Penerimaan Barang
@endsection

@push('after-app-script')
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <!-- Responsive examples -->
    <script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/datatables.init.js') }}"></script>
    <script>
        var no_krmskm = "{{ $no_krmskm }}";

        $('#showDataCheckButton').on('click', function() {
            if ($.fn.DataTable.isDataTable('#datatable-check')) {
                $('#datatable-check').DataTable().clear().destroy();
            }
            $('#datatable-check').DataTable({
                ajax: {
                    url: "{{ url('penerimaan-barang/create') }}/" + no_krmskm,
                    data: {
                        type: 'barang_krms'
                    }
                },
                lengthMenu: [5],
                columns: [{
                        data: null,
                        render: (data, type, row, meta) => meta.row + 1
                    },
                    {
                        data: "barang.nm_brg"
                    },
                    {
                        data: "qty_beli"
                    },
                    {
                        data: "satuan_beli"
                    },
                    {
                        data: "action"
                    }
                ],
            });
            $('#dataCheck').modal('show');
        });

        $('#showDataPermintaanButton').on('click', function() {
            if ($.fn.DataTable.isDataTable('#datatable-minta')) {
                $('#datatable-kirim').DataTable().clear().destroy();
            }
            $('#datatable-minta').DataTable({
                ajax: {
                    url: "{{ url('penerimaan-barang/create') }}/" + no_krmskm,
                    data: {
                        type: 'data_reqs'
                    }
                },
                lengthMenu: [5],
                columns: [{
                        data: null,
                        render: (data, type, row, meta) => meta.row + 1
                    },
                    {
                        data: "nm_brg"
                    },
                    {
                        data: "qty_beli"
                    },
                    {
                        data: "satuan_beli"
                    }
                ],
            });
            $('#dataMinta').modal('show');
        });

        $('#showDataPengirimanButton').on('click', function() {
            if ($.fn.DataTable.isDataTable('#datatable-kirim')) {
                $('#datatable-kirim').DataTable().clear().destroy();
            }
            $('#datatable-kirim').DataTable({
                ajax: {
                    url: "{{ url('penerimaan-barang/create') }}/" + no_krmskm,
                    data: {
                        type: 'data_krms'
                    }
                },
                lengthMenu: [5],
                columns: [{
                        data: null,
                        render: (data, type, row, meta) => meta.row + 1
                    },
                    {
                        data: "barang.nm_brg"
                    },
                    {
                        data: "qty_beli"
                    },
                    {
                        data: "satuan_beli"
                    }
                ],
            });
            $('#dataKirim').modal('show');
        });

        let selectedBarang = [];

        $('#datatable-check').on('click', '.check-barang', function(e) {
            let barangId = $(this).val();

            if ($(this).prop('checked')) {
                selectedBarang.push(barangId);
            } else {
                selectedBarang = selectedBarang.filter(item => item !==
                    barangId);
            }

            console.log(selectedBarang);
        });

        $('form').on('submit', function(e) {
            $('#items-container').empty();

            selectedBarang.forEach(function(barangId) {
                $('#items-container').append(
                    '<input type="hidden" name="brg_id[]" value="' + barangId +
                    '">'
                );
            });
        });
    </script>
@endpush

@section('content')
    <!-- Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Penerimaan Barang</h4>
            </div>
        </div>
    </div>

    <!-- Main Form -->
    <div class="row">
        <div class="col-md-12">
            <!-- Display validation errors -->
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Data Transaksi</h5>
                    <form action="{{ route('penerimaan-barang.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        {{-- <div class="form-group mt-3">
                        <label for="no_trm">No. Dokumen</label>
                        <input type="text" class="form-control" name="no_trm" value="{{ old('no_trm', $no_trm) }}"
                            required>
                    </div> --}}
                        <div class="form-group mt-3">
                            <label for="no_krmskm">No. Dokumen Pengiriman Gudang Besar</label>
                            <input type="text" name="no_krmskm" id="no_krmskm" class="form-control"
                                value="{{ $no_krm }}" readonly>
                        </div>
                        <div class="form-group mt-3">
                            <label for="no_reqskm">No. Dokumen Permintaan SKM</label>
                            <input type="text" name="no_reqskm" id="no_reqskm" class="form-control"
                                value="{{ $no_req }}" readonly>
                        </div>
                        <div class="form-group mt-3">
                            <label for="tgl_trm">Tanggal Penerimaan</label>
                            <input type="date" class="form-control" name="tgl_trm"
                                value="{{ old('tgl_trm', \Carbon\Carbon::now()->format('Y-m-d')) }}" required>
                        </div>
                        <input type="hidden" name="user_id" id="user_id" value="{{ old('user_id', $user_id ?? '') }}">
                        <div id="items-container"></div>
                        <div class="d-flex justify-content-end mt-3">
                            <a href="{{ route('penerimaan-barang') }}"
                                class="btn btn-secondary waves-effect waves-light me-2">
                                <i class="bx bx-caret-left align-middle me-2 font-size-18"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-primary waves-effect waves-light"><i
                                    class="bx bx bxs-save align-middle me-2 font-size-18"></i>Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <button type="button" class="btn btn-dark waves-effect waves-light me-2" id="showDataPermintaanButton">
                    <i class="bx bx-show-alt align-middle me-2 font-size-18"></i> Lihat Data Permintaan
                </button>
            </div>
            <div class="mb-3">
                <button type="button" class="btn btn-dark waves-effect waves-light me-2" id="showDataPengirimanButton">
                    <i class="bx bx-show-alt align-middle me-2 font-size-18"></i> Lihat Data Pengiriman
                </button>
            </div>
            <div class="mb-3">
                <button type="button" class="btn btn-dark waves-effect waves-light" id="showDataCheckButton">
                    <i class="bx bx-check-circle align-middle me-2 font-size-18"></i>Terima Data Barang
                </button>
            </div>
        </div>
    </div>

    <div class="modal fade" id="dataMinta" tabindex="-1" role="dialog" aria-labelledby="dataModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="dataModalLabel">Data Permintaan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <table id="datatable-minta" class="table align-middle table-nowrap">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
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
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="dataKirim" tabindex="-1" role="dialog" aria-labelledby="dataModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="dataModalLabel">Data Pengiriman</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <table id="datatable-kirim" class="table align-middle table-nowrap">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
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
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="dataCheck" tabindex="-1" role="dialog" aria-labelledby="dataModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="dataModalLabel">Data Penerimaan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <table id="datatable-check" class="table align-middle table-nowrap">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Barang</th>
                                        <th>Qty</th>
                                        <th>Satuan</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Selesai</button>
                </div>
            </div>
        </div>
    </div>
@endsection
