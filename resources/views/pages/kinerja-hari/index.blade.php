@extends('layouts.app')

@section('title')
Target Harian
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
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script>
    $('#datatable').DataTable({
            ajax: "{{ route('kinerja-hari') }}",
            columns: [
                {
                    data: "tahun"
                },
                {
                    data: "WEEK"
                },
                {
                    data: "barang.nm_brg"
                },
                {
                    data: "qty"
                },
                {
                    data: "action"
                }
            ],
            autoWidth: false
        });

        $('#datatableDetail').DataTable({
            ajax:{
                    url: "{{ route('kinerja-hari.detail') }}",
                    type: "GET",
                    data: function(d) {
                        d.week_id = window.currentWeekId; // Use the current week_id
                    }
                },
            columns: [
                {
                    data: "harian_id"
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
                    data: "qty"
                },
                {
                    data: "action"
                }
            ],
            footerCallback: function (row, data, start, end, display) {
                var api = this.api();
                var totalQty = api.column(2).data().reduce((a, b) => a + b, 0);
                $(api.column(1).footer()).html(totalQty);
            },
            autoWidth: false
        });

    $('#datatable').on('click', '.btn-edit, .btn-detail', function() {
        const weekId = $(this).data('week-id');
        if ($(this).hasClass('btn-edit')) {
            $('#week_id').val(weekId);
            $('#editModal').modal('show');
            window.currentWeekId = weekId;
            $('#datatableDetail').DataTable().ajax.reload();
        } else {
            window.currentWeekId = weekId;
            $('#datatableDetail').DataTable().ajax.reload();
            $('#detailModal').modal('show');
        }
    });

    // Handle form submission
    $('#formAction').on('submit', async function(event) {
        event.preventDefault();

        const formData = $(this).serialize();
        const url = $(this).attr('action');

        try {
            const response = await $.post(url, formData);

            if (response.success) {
                Swal.fire({
                    toast: true,
                    position: 'bottom-right',
                    icon: 'success',
                    title: response.message,
                    showConfirmButton: false,
                    timer: 3000
                });
                $('#formAction')[0].reset();
                $('#editModal').modal('hide');
                $('#datatable').DataTable().ajax.reload();
            } else {
                Swal.fire({
                    toast: true,
                    position: 'bottom-right',
                    icon: 'error',
                    title: response.message,
                    showConfirmButton: false,
                    timer: 3000
                });
                $('#formAction')[0].reset();
                $('#editModal').modal('hide');
                $('#datatable').DataTable().ajax.reload();
            }
        } catch (error) {
            Swal.fire({
                toast: true,
                position: 'bottom-right',
                icon: 'error',
                title: 'An error occurred',
                showConfirmButton: false,
                timer: 3000
            });

            $('#editModal').modal('hide');
            $('#datatable').DataTable().ajax.reload();
        }
    });
</script>
@endpush

@section('content')
<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Target Harian</h4>
        </div>
    </div>
</div>
<!-- end page title -->

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                {{-- <div class="d-flex justify-content-end mb-2">
                    <a href="{{ route('kinerja-hari.create') }}" class="btn btn-primary my-2"><i
                            class="bx bx-plus-circle align-middle me-2 font-size-18"></i> Tambah</a>
                </div> --}}
                <div class="table-responsive">
                    <table id="datatable" class="table align-middle table-nowrap" style="width: 100%">
                        <thead class="table-light">
                            <tr>
                                <th>Tahun</th>
                                <th>Minggu</th>
                                <th>Barang</th>
                                <th>Jumlah</th>
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

<!-- Modal create-->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <form id="formAction" action="{{ route('kinerja-hari.store') }}" method="post"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" id="week_id" name="week_id" value="{{ old('week_id') }}"
                                        readonly>
                                    <div class="form-group">
                                        <label for="tgl">Tanggal</label>
                                        <input type="date" class="form-control" name="tgl"
                                            value="{{ old('tgl', now()->format('Y-m-d')) }}" required readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="qty">Jumlah</label>
                                        <input type="text" class="form-control" name="qty" value="{{ old('qty') }}"
                                            required>
                                    </div>
                                    <div id="items-container"></div>
                                    <div class="d-flex justify-content-end mt-3">
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#nestedModal">Buka Modal Nested</button>
                                        <button type="submit" class="btn btn-success">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="datatableDetail" class="table align-middle table-nowrap">
                                        <thead class="table-light">
                                            <tr>
                                                <th>ID</th>
                                                <th>Tanggal</th>
                                                <th>Jumlah</th>
                                                <th>Action</th>
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
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="nestedModal" tabindex="-1" aria-labelledby="nestedModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="nestedModalLabel">Modal Nested</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Ini adalah modal nested di dalam modal utama.</p>
            </div>
        </div>
    </div>
</div>
<!-- Modal create-->
<div class="modal fade" id="shiftModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <form id="formAction" action="{{ route('kinerja-hari.store') }}" method="post"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" id="week_id" name="week_id" value="{{ old('week_id') }}"
                                        readonly>
                                    <div class="form-group">
                                        <label for="tgl">Tanggal</label>
                                        <input type="date" class="form-control" name="tgl"
                                            value="{{ old('tgl', now()->format('Y-m-d')) }}" required readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="qty">Jumlah</label>
                                        <input type="text" class="form-control" name="qty" value="{{ old('qty') }}"
                                            required>
                                    </div>
                                    <div id="items-container"></div>
                                    <div class="d-flex justify-content-end mt-3">
                                        <button type="submit" class="btn btn-success">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="datatableDetail" class="table align-middle table-nowrap">
                                        <thead class="table-light">
                                            <tr>
                                                <th>ID</th>
                                                <th>Tanggal</th>
                                                <th>Jumlah</th>
                                                <th>Action</th>
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
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>

@endsection