@extends('layouts.app')

@section('title')
Permintaan
@endsection

@push('after-app-style')
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
<script src="https://cdn.datatables.net/rowgroup/1.3.0/js/dataTables.rowGroup.min.js"></script>

<script>
    $('#datatable').DataTable({
            ajax: {
                url: "{{ route('log-produksi') }}",
                type: "GET",
                data: function(d) {
                    d.tgl_start = $('#tgl-start').val()
                    d.tgl_end = $('#tgl-end').val()
                }
            },
            processing: true,
            ordering: false,
            columns: [{
                    data: null,
                    render: (data, type, row, meta) => meta.row + 1
                },
                {
                    data: "produksi"
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
                    data: "mesin"
                },
                {
                    data: "waktu"
                },
                {
                    data: "pic"
                },
                {
                    data: null,
                    render: (data, type, row) => `<button class="btn btn-secondary btn-detail font-size-14 waves-effect waves-light" data-logprod-id="${data.logprod_id}">
                        Detail
                    </button> <button class="btn btn-success btn-edit font-size-14 waves-effect waves-light me-2" data-logprod-id="${data.logprod_id}">
                        Edit
                    </button> `
                }
            ],
            rowGroup: {
                dataSrc: 'mesin',
                startRender: function (rows, group) {
                    // Menampilkan grup di awal
                    return `
                        <tr>
                            <td colspan="6">Mesin: ${group}</td>
                            <td></td>
                            <td></td>
                        </tr>
                    `;
                },
                endRender: function (rows, group) {
                    // Menghitung total waktu
                    let totalWaktu = rows.data().reduce((a, b) => a + parseFloat(b.lost_time), 0);
                    var hours = Math.floor(totalWaktu / 60);  // Hasil jam
                    var minutes = totalWaktu % 60;  // Sisa menit

                    // Format hasil menjadi string
                    var result = hours + ' jam ' + minutes + ' menit';
                    return `
                        <tr>
                            <td colspan="4">Loss Time:</td>
                            <td>${result}</td>
                            <td></td>
                            <td></td>
                        </tr>
                    `;
                }
            }
        });
        $('#datatable').on('click', '.btn-detail, .btn-edit', function() {
            const logId = $(this).data('logprod-id');
            const row = $(this).closest('tr');
            const data = $('#datatable').DataTable().row(row).data();
            if ($(this).hasClass('btn-detail')) {
                window.logId = logId;
                $.ajax({
                    url: "{{ route('log-produksi.detail') }}/", // Replace with your update route
                    type: "get",
                    data: {
                        id: logId,
                    },
                    success: function(data) {
                        $(".modal-detail").html(data);
                        $('#troubleModal-detail').modal('show');
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error: ", status, error);
                    }
                });
            } else if ($(this).hasClass('btn-edit')) {
                window.logId = logId;
                $.ajax({
                    url: "{{ route('log-produksi.edit') }}/", // Replace with your update route
                    type: "get",
                    data: {
                        id: logId,
                    },
                    success: function(data) {
                        $(".modal-edit").html(data);
                        $('#troubleModal-edit').modal('show');
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error: ", status, error);
                    }
                });
            }
        });
        $('#waktu_mulai', '#waktu_selesai').on('change', function() {
            calculateTimeDifference();
        });

        // Fungsi untuk menghitung selisih waktu
        function calculateTimeDifference() {
            // Ambil nilai dari input waktu mulai dan selesai
            var startTime = document.getElementById('waktu_mulai').value;
            var endTime = document.getElementById('waktu_selesai').value;

            // Ubah nilai input waktu menjadi objek Date
            var start = new Date('1970-01-01T' + startTime + 'Z');
            var end = new Date('1970-01-01T' + endTime + 'Z');

            // Jika waktu selesai lebih kecil dari waktu mulai (misalnya lewat tengah malam)
            if (end < start) {
                end.setDate(end.getDate() + 1); // Tambah satu hari untuk waktu selesai
            }

            // Hitung selisih dalam milidetik
            var timeDiff = end - start;

            // Hitung total menit
            var totalMinutes = Math.floor(timeDiff / 1000 / 60);  // Total menit

            // Konversi milidetik ke menit dan jam
            var hours = Math.floor(timeDiff / 1000 / 60 / 60);  // Hitung jam
            var minutes = Math.floor((timeDiff / 1000 / 60) % 60); // Hitung menit

            // Format hasil
            var result = '';
            if (hours > 0) {
                result += hours + ' jam ';
            }
            result += minutes + ' menit';

            // Tampilkan hasil selisih waktu
            document.getElementById('lost_time').value = totalMinutes;
            document.getElementById('lost_time_text').value = result;
        }
        $('#tgl-input').on('change', function() {
            $('#datatable').DataTable().ajax.reload(); // Reload data based on new filters
        });
        function filterData() {
            $('#datatable').DataTable().ajax.reload(); // Reload data based on new filters
        }
</script>
@endpush

@section('content')
<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Log Produksi</h4>
        </div>
    </div>
</div>
<!-- end page title -->

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-end mb-2">
                    <a href="{{ route('log-produksi.create') }}" class="btn btn-primary my-2"><i
                            class="bx bx-plus-circle align-middle me-2 font-size-18"></i> Tambah</a>
                </div>
                <div class="d-flex flex-column">
                    <div class="row w-75">
                        <div class="col-sm-3 d-flex me-2">
                            <div class="mb-3 flex-grow-1">
                                <label for="startDate">Tanggal Mulai:</label>
                                <input type="date" class="form-control" name="start_date" id="tgl-start"
                                    value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="col-sm-3 d-flex me-2">
                            <div class="mb-3 flex-grow-1">
                                <label for="endDate">Tanggal Akhir:</label>
                                <input type="date" class="form-control" name="end_date" id="tgl-end"
                                    value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="col-sm-2 d-flex me-2 align-items-center">
                            <button class="btn btn-primary" onclick="filterData()">Cari</button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="datatable" class="table align-middle table-nowrap">
                        <thead class="table-light">
                            <tr>
                                <th>Id</th>
                                <th>Produksi</th>
                                <th>Tanggal</th>
                                <th>Mesin</th>
                                <th>Waktu</th>
                                <th>PIC</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div> <!-- end col -->
</div>
<!-- end row -->

<div class="modal fade" id="troubleModal-detail" tabindex="-1" role="dialog" aria-labelledby="troubleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="troubleModalLabel">Log Produksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body modal-detail">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="troubleModal-edit" tabindex="-1" role="dialog" aria-labelledby="troubleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="troubleModalLabel">Log Produksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body modal-edit">

            </div>
        </div>
    </div>
</div>
@endsection