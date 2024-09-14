@extends('layouts.app')

@section('title')
    Edit Permintaan
@endsection

@push('after-app-script')
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/datatables.init.js') }}"></script>

    <script>
        var no_reqskm = "{{ $no_reqskm }}";

        $('#datatable').DataTable({
            ajax: "{{ url('permintaan-skm/edit') }}/" + no_reqskm,
            lengthMenu: [5],
            columns: [{
                    data: "barang.nm_brg"
                },
                {
                    data: "qty",
                    render: function(data, type, row) {
                        return `
                            <span class="qty-value">${data}</span>
                            <input type="number" class="form-control qty-input d-none" value="${data}">
                        `;
                    }
                },
                {
                    data: "satuan_besar"
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        return `
                            <button class="btn btn-success waves-effect waves-light edit-btn"><i class="bx bx-edit align-middle font-size-18"></i> Edit</button>
                            <button class="btn btn-primary waves-effect waves-light save-btn d-none"><i class="bx bx-save align-middle font-size-18"></i> Simpan</button>
                        `;
                    }
                }
            ],
            rowCallback: function(row, data) {
                $(row).attr('data-brg-id', data.brg_id);
            }
        });

        $('#datatable').on('click', '.edit-btn', function() {
            var $row = $(this).closest('tr');
            $row.find('.qty-value').addClass('d-none');
            $row.find('.qty-input').removeClass('d-none');
            $(this).addClass('d-none');
            $row.find('.save-btn').removeClass('d-none');
        });

        $('#datatable').on('click', '.save-btn', function() {
            var $row = $(this).closest('tr');
            var qty = $row.find('.qty-input').val();
            var brg_id = $row.data('brg-id');

            $.ajax({
                url: "{{ route('permintaan-skm.update', ['no_reqskm' => $no_reqskm]) }}",
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    no_reqskm: "{{ $no_reqskm }}",
                    items: [{
                        brg_id: brg_id,
                        qty: qty
                    }]
                },
                success: function(response) {
                    $row.find('.qty-value').text(qty).removeClass('d-none');
                    $row.find('.qty-input').addClass('d-none');
                    $row.find('.save-btn').addClass('d-none');
                    $row.find('.edit-btn').removeClass('d-none');
                    alert('Qty updated successfully');
                },
                error: function() {
                    alert('Failed to update qty');
                }
            });
        });
    </script>
@endpush

@section('content')
    <!-- Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Edit Permintaan</h4>
            </div>
        </div>
    </div>

    <!-- Main Form -->
    <div class="row">
        <div class="col-md-12">
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
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Data Transaksi</h5>
                    <form>
                        @csrf
                        <div class="form-group mt-3">
                            <label for="no_reqskm">No. Dokumen Permintaan SKM :</label>
                            <input type="text" name="no_reqskm" id="no_reqskm" class="form-control"
                                value="{{ $no_req }}" readonly>
                        </div>
                        <div class="form-group mt-3">
                            <label for="tgl">Tanggal Permintaan :</label>
                            <input type="text" name="tgl" id="tgl" class="form-control"
                                value="{{ \Carbon\Carbon::parse($datas['tgl'])->format('d-m-Y') }}" readonly>
                        </div>
                        <div id="items-container"></div> <!-- Container for items input fields -->
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <!-- Data Barang Table -->
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">Data Barang</h4>
                    <div class="table-responsive">
                        <table id="datatable" class="table align-middle table-nowrap">
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
