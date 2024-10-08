@extends('layouts.app')

@section('title')
    Target Mingguan
@endsection

@push('after-app-style')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container .select2-selection--single {
            padding: 0.30rem 0.45rem;
            height: 38.2px;
        }
    </style>
@endpush

@push('after-app-script')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>

    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <!-- Responsive examples -->
    <script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/datatables.init.js') }}"></script>
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

        $('#datatable').DataTable({
            ajax: {
                url: "{{ route('kinerja-minggu') }}",
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
                    data: "qty",
                },
                {
                    data: "action"
                }
            ],
            ordering: false,
        });
        // Handle Edit button click
        $('#datatable').on('click', '.btn-editWeek', function() {
            var table = $('#datatable').DataTable();
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
            $(this).text('Save').removeClass('btn-editWeek').addClass('btn-save');

            // Show Save and Cancel buttons, hide Edit button

            $row.find('.btn-cancel').show();
        });

        // Handle Save button click
        $('#datatable').on('click', '.btn-save', function() {
            var table = $('#datatable').DataTable();
            var $row = $(this).closest('tr');
            var row = table.row($row);

            var originalData = $row.data('original');
            // Collect updated qty value
            var updatedQty = $row.find('td').eq(3).find('input').val();
            var week_id = originalData.week_id;

            // Send updated data to server via AJAX
            $.ajax({
                url: "{{ route('kinerja-minggu.update') }}", // Replace with your update route
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    qty: updatedQty,
                    id: row.data().week_id // Assuming each row has a unique ID
                },
                success: function(response) {
                    Swal.fire({
                        toast: true,
                        position: 'bottom-right',
                        icon: 'success',
                        title: response.message,
                        showConfirmButton: false,
                        timer: 5000
                    });
                    // Reload table data
                    table.ajax.reload();
                }
            });

        });

        // Handle Cancel button click
        $('#datatable').on('click', '.btn-cancel', function() {
            var $row = $(this).closest('tr');
            var row = table.row($row);

            // Restore original data
            var originalData = $row.data('original');
            $row.find('td').each(function(index) {
                if (index === 3) { // Only for qty column
                    $(this).text(originalData.qty);
                }
            });

            // Hide Save and Cancel buttons, show Edit button
            $row.find('.btn-save').hide();
            $row.find('.btn-cancel').hide();
            $row.find('.btn-edit').show();
        });

        // Apply filters on change
        $('#filterTahun, #filterWeek').on('change', function() {
            $('#datatable').DataTable().ajax.reload(); // Reload data based on new filters
        });
    </script>
@endpush

@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Target Mingguan</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-end mb-2">
                        <a href="{{ route('kinerja-minggu.create') }}" class="btn btn-primary my-2"><i
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
                                    <option value="{{ $minggu['minggu'] }}"
                                        {{ \Carbon\Carbon::now()->format('W') == $minggu['minggu'] ? 'selected' : '' }}>
                                        {{ $minggu['minggu'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="datatable" class="table align-middle table-nowrap">
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

    @if (session()->has('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    toast: true,
                    position: 'bottom-right',
                    icon: 'success',
                    title: '{{ session('success') }}',
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
            });
        </script>
    @endif
@endsection
