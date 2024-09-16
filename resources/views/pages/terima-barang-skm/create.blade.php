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

        $('#datatable-check').DataTable({
            ajax: {
                url: "{{ url('penerimaan-barang/create') }}/" + no_krmskm,
                data: {
                    type: 'barang_krms'
                }
            },
            lengthMenu: [5],
            columns: [{
                    data: "barang.nm_brg"
                },
                {
                    data: "qty"
                },
                {
                    data: "satuan_besar"
                },
                {
                    data: "action"
                }
            ],
        });

        $('#datatable-minta').DataTable({
            ajax: {
                url: "{{ url('penerimaan-barang/create') }}/" + no_krmskm,
                data: {
                    type: 'data_reqs'
                }
            },
            lengthMenu: [5],
            columns: [{
                    data: "nm_brg"
                },
                {
                    data: "qty"
                },
                {
                    data: "satuan_besar"
                }
            ],
        });

        $('#datatable-kirim').DataTable({
            ajax: {
                url: "{{ url('penerimaan-barang/create') }}/" + no_krmskm,
                data: {
                    type: 'data_krms'
                }
            },
            lengthMenu: [5],
            columns: [{
                    data: "barang.nm_brg"
                },
                {
                    data: "qty"
                },
                {
                    data: "satuan_besar"
                }
            ],
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
    <div class="col-12">
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
                        <input type="text" name="no_krmskm" id="no_krmskm" class="form-control" value="{{ $no_krm }}"
                            readonly>
                    </div>
                    <div class="form-group mt-3">
                        <label for="no_reqskm">No. Dokumen Permintaan SKM</label>
                        <input type="text" name="no_reqskm" id="no_reqskm" class="form-control" value="{{ $no_req }}"
                            readonly>
                    </div>
                    <div class="form-group mt-3">
                        <label for="tgl_trm">Tanggal Penerimaan</label>
                        <input type="date" class="form-control" name="tgl_trm"
                            value="{{ old('tgl_trm', \Carbon\Carbon::now()->format('Y-m-d')) }}" required readonly>
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
</div>
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-3">Data Permintaan</h4>
                <div class="table-responsive">
                    <table id="datatable-minta" class="table align-middle table-nowrap">
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
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-3">Data Pengiriman</h4>
                <div class="table-responsive">
                    <table id="datatable-kirim" class="table align-middle table-nowrap">
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
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-3">Data Penerimaan</h4>
                <div class="table-responsive">
                    <table id="datatable-check" class="table align-middle table-nowrap">
                        <thead class="table-light">
                            <tr>
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
    </div>
</div>
@endsection