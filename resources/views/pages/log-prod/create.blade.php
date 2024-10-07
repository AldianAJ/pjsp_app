@extends('layouts.app')

@section('title')
Tambah Log Produksi
@endsection

@push('after-app-script')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<!-- Responsive examples -->
<script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/datatables.init.js') }}"></script>
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/autonumeric/4.10.5/autoNumeric.min.js"></script>
<script>
    $('#minggu').select2({
            selectOnClose: true,
            width: 'resolve' // need to override the changed default
        });

        $('input[name="tgl"]').on('input blur', function() {
            const selectedDate = $(this).val();

                if ($.fn.DataTable.isDataTable('#datatable-machines')) {
                    $('#datatable-machines').DataTable().clear().destroy();
                }

                $('#datatable-machines').DataTable({
                    ajax: {
                        url: "{{ route('log-produksi.create') }}",
                        data: {
                            type: 'machines',
                            date: selectedDate
                        }
                    },
                    columns: [{
                            data: null,
                            render: (data, type, row, meta) => meta.row + 1
                        },
                        {
                            data: "shift"
                        },
                        {
                            data: "nama"
                        },
                        {
                            data: null,
                            render: (data, type, row) => `
                        <button type="button" class="btn btn-primary font-size-14 waves-effect waves-light" onclick="pilihMesin('${row.msn_trgt_id}')">
                        Pilih
                    </button>`
                        }
                    ]
                });

                $('#dataMesin').modal('show');
            });

            let selectedItems = [];

            function addItem() {
                const msn_trgt_id = document.getElementById('msn_trgt_id').value;
                const trouble = document.getElementById('trouble').value;
                const penanganan = document.getElementById('penanganan').value;
                const waktu_mulai = document.getElementById('waktu_mulai').value;
                const waktu_selesai = document.getElementById('waktu_selesai').value;
                const lost_time = document.getElementById('lost_time').value;
                const lost_time_text = document.getElementById('lost_time_text').value;
                const ket = document.getElementById('ket').value;
                const pic = document.getElementById('pic').value;

                selectedItems.push({
                    msn_trgt_id,
                    trouble,
                    penanganan,
                    waktu_mulai,
                    waktu_selesai,
                    lost_time,
                    lost_time_text,
                    ket,
                    pic,
                });
                updateItems();

            }

            window.removeItem = function(index) {
                selectedItems.splice(index, 1);
                updateItems();
            }

            function updateItems() {
                const itemsTable = document.getElementById('selected-items');
                const itemsContainer = document.getElementById('items-container');
                const saveButton = document.getElementById('saveButton');

                itemsTable.innerHTML = selectedItems.map((item, index) => `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${item.trouble}</td>
                        <td>${item.penanganan}</td>
                        <td>${item.waktu_mulai}</td>
                        <td>${item.waktu_selesai}</td>
                        <td>${item.lost_time_text}</td>
                        <td>${item.ket}</td>
                        <td>
                            <button class="btn btn-danger waves-effect waves-light" onclick="removeItem(${index})">
                                <i class="bx bxs-trash align-middle font-size-14"></i>
                            </button>
                        </td>
                    </tr>
                `).join('');

                        itemsContainer.innerHTML = selectedItems.map((item, index) => `
                    <input type="hidden" name="items[${index}][msn_trgt_id]" value="${item.msn_trgt_id}">
                    <input type="hidden" name="items[${index}][trouble]" value="${item.trouble}">
                    <input type="hidden" name="items[${index}][penanganan]" value="${item.penanganan}">
                    <input type="hidden" name="items[${index}][waktu_mulai]" value="${item.waktu_mulai}">
                    <input type="hidden" name="items[${index}][waktu_selesai]" value="${item.waktu_selesai}">
                    <input type="hidden" name="items[${index}][lost_time]" value="${item.lost_time}">
                    <input type="hidden" name="items[${index}][lost_time_text]" value="${item.lost_time_text}">
                    <input type="hidden" name="items[${index}][ket]" value="${item.ket}">
                    <input type="hidden" name="items[${index}][pic]" value="${item.pic}">
                `).join('');

                saveButton.disabled = selectedItems.length === 0;
            }

        function pilihMesin(msn_trgt_id) {
            document.getElementById('msn_trgt_id').value = msn_trgt_id;
            // $('#troubleModalLabel').html(`Log Produksi`);

            $('#dataMesin').modal('hide');
            $('#troubleModal').modal('show');
        }

        $('#waktu_mulai', '#waktu_selesai').on('change', function() {
            calculateTimeDifference();
        });

        function pilihTgl(){
            const selectedDate = document.getElementById('tgl').value;

                if ($.fn.DataTable.isDataTable('#datatable-machines')) {
                    $('#datatable-machines').DataTable().clear().destroy();
                }

                $('#datatable-machines').DataTable({
                    ajax: {
                        url: "{{ route('log-produksi.create') }}",
                        data: {
                            type: 'machines',
                            date: selectedDate
                        }
                    },
                    columns: [{
                            data: null,
                            render: (data, type, row, meta) => meta.row + 1
                        },
                        {
                            data: "shift"
                        },
                        {
                            data: "nama"
                        },
                        {
                            data: null,
                            render: (data, type, row) => `
                        <button type="button" class="btn btn-primary font-size-14 waves-effect waves-light" onclick="pilihMesin('${row.msn_trgt_id}')">
                        Pilih
                    </button>`
                        }
                    ]
                });

                $('#dataMesin').modal('show');
        }
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
</script>
@endpush

@section('content')
<!-- Page Title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Tambah Log Produksi</h4>
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
                <form action="{{ route('log-produksi.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="form-group mt-3">
                            <label for="tgl">Tanggal</label>
                            <div class="input-group">
                                <div class="col-xl-10 me-2">
                                    <input type="date" class="form-control" name="tgl" id="tgl"
                                        value="{{ old('tgl', \Carbon\Carbon::now()->format('Y-m-d')) }}" required>
                                </div>
                                <button type="button" class="btn btn-primary btn-sm me-2"
                                    onclick="pilihTgl()">Pilih</button>
                            </div>
                        </div>

                        <input type="hidden" name="msn_trgt_id" id="msn_trgt_id" value="">
                    </div>
                    <div id="items-container"></div> <!-- Container for items input fields -->
                    <div class="d-flex justify-content-end mt-3">
                        <a href="{{ route('log-produksi') }}" class="btn btn-secondary me-1">Kembali</a>
                        <button type="submit" class="btn btn-primary waves-effect waves-light" id="saveButton" disabled>
                            <i class="bx bx bxs-save align-middle me-2 font-size-18"></i>Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="dataMesin" tabindex="-1" role="dialog" aria-labelledby="machineModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="machineModalLabel">Pilih Mesin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="table-responsive">
                            <table id="datatable-machines" class="table align-middle table-nowrap">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Shift</th>
                                        <th>Mesin</th>
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
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="troubleModal" tabindex="-1" role="dialog" aria-labelledby="troubleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="troubleModalLabel">Log Produksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="modal-brg-id">
                <div class="mb-3">
                    <label for="trouble" class="form-label">Trouble</label>
                    <textarea class="form-control" name="trouble" id="trouble" rows="6"></textarea>
                </div>
                <input type="hidden" id="msn_trgt_id">
                <div class="mb-3">
                    <label for="penanganan" class="form-label">Penanganan</label>
                    <textarea class="form-control" name="penanganan" id="penanganan" rows="6"></textarea>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="waktu_mulai" class="form-label">Waktu Mulai</label>
                        <input type="time" class="form-control" name="waktu_mulai" id="waktu_mulai"
                            onblur="calculateTimeDifference()">
                    </div>

                    <div class="col-md-6">
                        <label for="waktu_selesai" class="form-label">Waktu Selesai</label>
                        <input type="time" class="form-control" name="waktu_selesai" id="waktu_selesai"
                            onblur="calculateTimeDifference()">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="lost_time_text" class="form-label">Lost Time</label>
                    <input type="text" class="form-control" name="lost_time_text" id="lost_time_text">
                </div>
                <input type="hidden" class="form-control" name="lost_time" id="lost_time">

                <div class="mb-3">
                    <label for="ket" class="form-label">Keterangan</label>
                    <textarea class="form-control" name="ket" id="ket" rows="6"></textarea>
                </div>
                <div class="mb-3">
                    <label for="pic" class="form-label">PIC</label>
                    <input type="text" class="form-control" name="pic" id="pic">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="addItem()"
                    data-bs-dismiss="modal">Tambah</button>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 mt-2">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">List Log Produksi</h5>
                <div class="table-responsive">
                    <table class="table table-striped" id="selected-items-table">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Trouble</th>
                                <th>Penanganan</th>
                                <th>Waktu Mulai</th>
                                <th>waktu Selesai</th>
                                <th>Lost Time</th>
                                <th>Ket</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="selected-items">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection