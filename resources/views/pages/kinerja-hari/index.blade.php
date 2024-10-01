@extends('layouts.app')

@section('title')
Target Harian
@endsection

@push('after-app-style')
<!-- Sweet Alert-->
<link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
<style>
    .select2-container .select2-selection--single {
        padding: 0.30rem 0.45rem;
        height: 38.2px;
    }
</style>
@endpush

@push('after-app-script')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<!-- Responsive examples -->
<script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/datatables.init.js') }}"></script>
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $('#filterTahun2').select2({
            selectOnClose: true,
            width: 'resolve' // need to override the changed default
        });
        $('#filterWeek').select2({
            selectOnClose: true,
            width: 'resolve' // need to override the changed default
        });
    });
    // Apply filters on change
    $('#filterTahun, #filterWeek').on('change', function() {
        $('#datatable').DataTable().ajax.reload(); // Reload data based on new filters
    });
    $('#datatable').DataTable({
            ajax: {
                url: "{{ route('kinerja-hari') }}",
                type: "GET",
                data: function(d) {
                    d.tahun = $('#filterTahun').val(); // Pass selected tahun
                    d.week = $('#filterWeek').val(); // Pass selected week
                }
            },
            columns: [{
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
            autoWidth: false,
            ordering: false
        });

        $('#datatableDetail').DataTable({
            ajax: {
                url: "{{ route('kinerja-hari.detail') }}",
                type: "GET",
                data: function(d) {
                    d.week_id = window.currentWeekId; // Use the current week_id
                }
            },
            lengthMenu: [5],
            columns: [{
                    data: null, // Use null because we will provide custom rendering
                    render: function(data, type, row, meta) {
                        return meta.row + 1; // +1 to start numbering from 1
                    }
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
            footerCallback: function(row, data, start, end, display) {
                var api = this.api();
                var totalQty = api.column(2).data().reduce((a, b) => a + b, 0);
                $(api.column(3).footer()).html(totalQty);
            },
            autoWidth: false,
            ordering: false
        });

        if (window.currentShiftId !== null) {

            $('#datatableShiftDetail').DataTable({
                ajax: {
                    url: "{{ route('kinerja-shift.detail') }}",
                    type: "GET",
                    data: function(d) {
                        d.harian_id = window.currentHarianId; // Use the current week_id
                    }
                },
                lengthMenu: [5],
                columns: [{
                        data: null, // Use null because we will provide custom rendering
                        render: function(data, type, row, meta) {
                            return meta.row + 1; // +1 to start numbering from 1
                        }
                    },
                    {
                        data: "target_hari.target_week.barang.nm_brg"
                    },
                    {
                        data: "shift"
                    },
                    {
                        data: "qty"
                    },
                    {
                        data: "action"
                    }
                ],
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();
                    var totalQty = api.column(3).data().reduce((a, b) => a + b, 0);
                    $(api.column(3).footer()).html(totalQty);
                },
                autoWidth: false,
                ordering: false
            });
        }

        $('#datatableMesinDetail').DataTable({
            ajax: {
                url: "{{ route('kinerja-mesin.detail') }}",
                type: "GET",
                data: function(d) {
                    d.shift_id = window.currentShiftId; // Use the current week_id
                }
            },
            lengthMenu: [5],
            columns: [{
                    data: null, // Use null because we will provide custom rendering
                    render: function(data, type, row, meta) {
                        return meta.row + 1; // +1 to start numbering from 1
                    }
                },
                {
                    data: "target_shift.target_hari.target_week.barang.nm_brg"
                },
                {
                    data: "mesin.nama"
                },
                {
                    data: "qty"
                },
                {
                    data: "action"
                }
            ],
            footerCallback: function(row, data, start, end, display) {
                var api = this.api();
                var totalQty = api.column(3).data().reduce((a, b) => a + b, 0);
                $(api.column(3).footer()).html(totalQty);
            },
            autoWidth: false,
            ordering: false
        });

        // Apply filters on change
        $('#filterTahun, #filterWeek').on('change', function() {
            $('#datatable').DataTable().ajax.reload(); // Reload data based on new filters
        });

        $('#datatable').on('click', '.btn-edit, .btn-detailHari', function() {
            const weekId = $(this).data('week-id');
            const row = $(this).closest('tr');
            const data = $('#datatable').DataTable().row(row).data();

            if (data && data.barang) {
                const name = data.barang.nm_brg;
                const details = data.qty;

                $('#week_id').val(weekId);
                window.currentWeekId = weekId;
                $('#modalTitle').html(`Target Harian (${name}, Jumlah: ${details})`);
                $('#datatableDetail').DataTable().ajax.reload();
                $('#hariModal').modal('show');
            } else {
                console.error("Invalid data structure for week details.");
            }
        });

        $('#datatableDetail').on('click', '.btn-edit, .btn-shift', function() {
            const harianId = $(this).data('harian-id');
            const row = $(this).closest('tr');
            const data = $('#datatableDetail').DataTable().row(row).data();

            if (data && data.target_week && data.target_week.barang) {
                const name = data.target_week.barang.nm_brg;
                const tgl = data.tgl;
                const details = data.qty;

                $('#harian_id').val(harianId);
                window.currentHarianId = harianId;
                $('#modalTitleShift').html(`Target Shift (${tgl}) (${name}, Jumlah: ${details})`);
                $('#hariModal').modal('hide');
                $('#datatableShiftDetail').DataTable().ajax.reload();
                $('#shiftModal').modal('show');
            } else {
                console.error("Invalid data structure for daily target details.");
            }
        });

        $('#datatableShiftDetail').on('click', '.btn-edit, .btn-mesin', function() {
            const shiftId = $(this).data('shift-id');
            const row = $(this).closest('tr');
            const data = $('#datatableShiftDetail').DataTable().row(row).data();

            if (data && data.target_hari && data.target_hari.target_week && data.target_hari.target_week.barang) {
                const name = data.target_hari.target_week.barang.nm_brg;
                const tgl = data.target_hari.tgl;
                const details = data.qty;
                const shift = data.shift;

                $('#shift_id').val(shiftId);
                window.currentShiftId = shiftId;
                $('#modalTitleMesin').html(`Target Mesin (${tgl}) (Shift ${shift}) (${name}, Jumlah: ${details})`);
                $('#shiftModal').modal('hide');
                $('#datatableMesinDetail').DataTable().ajax.reload();
                $('#mesinModal').modal('show');
            } else {
                console.error("Invalid data structure for machine target details.");
            }
        });
        // Handle Edit button click
        $('#datatableDetail').on('click', '.btn-editHari', function() {
            var table = $('#datatableDetail').DataTable();
            var $row = $(this).closest('tr');
            var row = table.row($row);

            // Store original data
            var originalData = row.data();
            $row.data('original', originalData);

            // Convert qty cell to an input field
            var qtyCell = $row.find('td').eq(2); // Assuming qty is the 4th column
            var qtyText = qtyCell.text();
            qtyCell.html('<input type="text" value="' + qtyText + '" class="form-control">');

            // Switch to inline edit mode
            $(this).text('Save').removeClass('btn-editHari').addClass('btn-save');

            // Show Save and Cancel buttons, hide Edit button

            $row.find('.btn-shift').hide();
            $row.find('.btn-cancel').show();
        });

        // Handle Save button click
        $('#datatableDetail').on('click', '.btn-save', function() {
            var table = $('#datatableDetail').DataTable();
            var $row = $(this).closest('tr');
            var row = table.row($row);

            var originalData = $row.data('original');
            // Collect updated qty value
            var updatedQty = $row.find('td').eq(2).find('input').val();
            var harian_id = originalData.harian_id;
            var week_id = originalData.week_id;
            var qtyOri = originalData.qty;
            // Send updated data to server via AJAX
            $.ajax({
                url: "{{ route('kinerja-hari.update') }}", // Replace with your update route
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    week_id: week_id,
                    qtyOri: qtyOri,
                    qty: updatedQty,
                    id: row.data().harian_id // Assuming each row has a unique ID
                },
                success: function(response) {
                    Swal.fire({
                        toast: true,
                        position: 'top-right',
                        icon: response.success ? 'success' : 'error',
                        title: response.message,
                        showConfirmButton: false,
                        timer: 5000,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                        },
                        customClass: {
                            popup: 'colored-toast'
                        },
                        showCloseButton: true
                    });
                    // Reload table data
                    table.ajax.reload();
                }
            });

        });

        // Handle Edit button click
        $('#datatableShiftDetail').on('click', '.btn-editShift', function() {
            var table = $('#datatableShiftDetail').DataTable();
            var $row = $(this).closest('tr');
            var row = table.row($row);

            // Store original data
            var originalData = row.data();
            $row.data('original', originalData);

            // Convert qty cell to an input field
            var qtyCell = $row.find('td').eq(3); // Assuming qty is the 4th column
            var qtyText = qtyCell.text();
            qtyCell.html('<input type="text" value="' + qtyText + '" class="form-control">');

            // Switch to inline edit mode
            $(this).text('Save').removeClass('btn-editShift').addClass('btn-save');

            // Show Save and Cancel buttons, hide Edit button

            $row.find('.btn-cancel').show();
        });

        // Handle Save button click
        $('#datatableShiftDetail').on('click', '.btn-save', function() {
            var table = $('#datatableShiftDetail').DataTable();
            var $row = $(this).closest('tr');
            var row = table.row($row);

            var originalData = $row.data('original');
            // Collect updated qty value
            var updatedQty = $row.find('td').eq(3).find('input').val();
            var shift_id = originalData.shift_id;
            var harian_id = originalData.harian_id;
            var qtyOri = originalData.qty;
            // Send updated data to server via AJAX
            $.ajax({
                url: "{{ route('kinerja-shift.update') }}", // Replace with your update route
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    harian_id: harian_id,
                    qtyOri: qtyOri,
                    qty: updatedQty,
                    id: shift_id
                },
                success: function(response) {
                    Swal.fire({
                        toast: true,
                        position: 'top-right',
                        icon: response.success ? 'success' : 'error',
                        title: response.message,
                        showConfirmButton: false,
                        timer: 5000
                    });
                    // Reload table data
                    table.ajax.reload();
                }
            });

        });

        // Handle form submission
        $('form').on('submit', async function(event) {
            event.preventDefault();
            const form = $(this);
            const formData = form.serialize();
            const url = form.attr('action');
            const formId = form.attr('id');

            console.log(url, formData, formId);
            try {
                const response = await $.post(url, formData);

                if (response) {
                    Swal.fire({
                        toast: true,
                        position: 'top-right',
                        icon: response.success ? 'success' : 'error',
                        title: response.message,
                        showConfirmButton: false,
                        timer: 5000
                    });
                    form[0].reset();
                    $('#datatable').DataTable().ajax.reload();
                    $('#datatableDetail').DataTable().ajax.reload();
                    $('#datatableShiftDetail').DataTable().ajax.reload();
                    $('#datatableMesinDetail').DataTable().ajax.reload();
                }
            } catch (error) {
                Swal.fire({
                    toast: true,
                    position: 'top-right',
                    icon: 'error',
                    title: 'An error occurred',
                    showConfirmButton: false,
                    timer: 5000
                });

                $('#datatable').DataTable().ajax.reload();
                $('#datatableDetail').DataTable().ajax.reload();
                $('#datatableShiftDetail').DataTable().ajax.reload();
                $('#datatableMesinDetail').DataTable().ajax.reload();
            }
        });

        // Menangani penutupan modal setelah ditutup
        $('#backHarian').on('click', function() {
            var mainModal = new bootstrap.Modal(document.getElementById('hariModal'));
            mainModal.show();
        });
        $('#mesinModal').on('hidden.bs.modal', function() {
            var mainModal = new bootstrap.Modal(document.getElementById('shiftModal'));
            mainModal.show(); // Tampilkan kembali modal utama jika belum ditampilkan
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
                <div class="d-flex justify-content-end mb-2">
                    <a href="{{ route('kinerja-hari.create') }}" class="btn btn-primary my-2"><i
                            class="bx bx-plus-circle align-middle me-2 font-size-18"></i> Tambah</a>
                </div>
                <!-- Filter Toolbar -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="filterTahun">Tahun:</label>
                        <select id="filterTahun" class="form-control" style="width: 100%">
                            <option value="">Semua</option>
                            <option value="{{ \Carbon\Carbon::now()->format('Y') }}" selected>
                                {{ \Carbon\Carbon::now()->format('Y') }}</option>
                            <!-- Populate options dynamically via JavaScript -->
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="filterWeek">Minggu:</label>
                        <select id="filterWeek" class="form-control" style="width: 100%">
                            <option value="">Semua</option>
                            @foreach ($mingguList as $minggu)
                            <option value="{{ $minggu['minggu'] }}" {{ \Carbon\Carbon::now()->format('W') ==
                                $minggu['minggu'] ? 'selected' : '' }}>
                                {{ $minggu['minggu'] }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
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

<!-- Modal Target Harian-->
<div class="modal fade" id="hariModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="modalTitle" class="modal-title">Target Harian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Data Transaksi</h5>

                                <form id="formAction" action="{{ route('kinerja-hari.store') }}" method="post"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="tgl">Tanggal</label>
                                                <input type="date" class="form-control" name="tgl"
                                                    value="{{ old('tgl', now()->format('Y-m-d')) }}" required readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="qty">Jumlah</label>
                                                <input type="text" class="form-control" name="qty"
                                                    value="{{ old('qty') }}" pattern="\d*" inputmode="numeric" required>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" id="week_id" name="week_id" value="{{ old('week_id') }}"
                                        readonly>
                                    <div id="items-container"></div>
                                    <div class="d-flex justify-content-end my-3">
                                        <button type="button" class="btn btn-secondary me-1"
                                            data-bs-dismiss="modal">Kembali</button>
                                        <button type="submit" class="btn btn-success">Simpan</button>
                                    </div>
                                </form>

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

<!-- Modal Target Shift-->
<div class="modal fade" id="shiftModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="modalTitleShift" class="modal-title">Target Shift</h5>
                <button type="button" id="backHarian" class="btn-close" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Data Transaksi</h5>
                                <form id="formShift" action="{{ route('kinerja-shift.store') }}" method="post"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mt-3">
                                                <label for="tgl">Shift</label>
                                                <select name="shift" class="form-control">
                                                    <option value="1">1</option>
                                                    <option value="2">2</option>
                                                    <option value="3">3</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mt-3">
                                                <label for="qty">Jumlah</label>
                                                <input type="text" class="form-control" name="qty"
                                                    value="{{ old('qty') }}" pattern="\d*" inputmode="numeric" required>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" id="harian_id" class="form-control" name="harian_id"
                                        value="{{ old('harian_id') }}" required readonly>
                                    <div id="items-container"></div> <!-- Container for items input fields -->
                                    <div class="d-flex justify-content-end my-3">
                                        <button type="button" id="backHarian" class="btn btn-secondary me-1"
                                            data-bs-dismiss="modal">Kembali</button>
                                        <button type="submit" class="btn btn-success">Simpan</button>
                                    </div>
                                </form>

                                <div class="table-responsive">
                                    <table id="datatableShiftDetail" class="table align-middle table-nowrap">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Id</th>
                                                <th>Barang</th>
                                                <th>Shift</th>
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

<!-- Modal Target Mesin-->
<div class="modal fade" id="mesinModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="modalTitleMesin" class="modal-title">Target Mesin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Data Transaksi</h5>

                                <form id="formMesin" action="{{ route('kinerja-mesin.store') }}" method="post"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group mt-3">
                                        <label for="tgl">Mesin</label>
                                        <select name="mesin_id" class="form-control">
                                            @foreach ($mesins as $mesin)
                                            <option value="{{ $mesin->mesin_id }}">{{ $mesin->nama }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group mt-3">
                                        <label for="qty">Jumlah</label>
                                        <input type="text" class="form-control" name="qty" value="{{ old('qty') }}"
                                            required>
                                    </div>
                                    <input type="hidden" id="shift_id" class="form-control" name="shift_id"
                                        value="{{ old('shift_id') }}" required readonly>
                                    <div id="items-container"></div> <!-- Container for items input fields -->
                                    <div class="d-flex justify-content-end my-3">
                                        <button type="button" class="btn btn-secondary me-1"
                                            data-bs-dismiss="modal">Kembali</button>
                                        <button type="submit" class="btn btn-success">Simpan</button>
                                    </div>
                                </form>

                                <div class="table-responsive">
                                    <table id="datatableMesinDetail" class="table align-middle table-nowrap">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Id</th>
                                                <th>Barang</th>
                                                <th>Mesin</th>
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