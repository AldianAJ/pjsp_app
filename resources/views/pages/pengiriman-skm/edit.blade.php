@extends('layouts.app')

@section('title')
Edit Pengiriman Batangan
@endsection

@push('after-app-style')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('after-app-script')
<script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/datatables.init.js') }}"></script>
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/autonumeric/4.10.5/autoNumeric.min.js"></script>

<script>
    $(document).ready(function() {
            var mutasi_id = "{{ $mutasi_id }}";
            var dataTableInitialized = false;

            function initializeDataTable() {
                $('#datatable-detail').DataTable({
                    ajax: {
                        url: "{{ url('pengiriman-skm/edit') }}/" + mutasi_id,
                    },
                    lengthMenu: [5],
                    columns: [{
                            data: null,
                            render: (data, type, row, meta) => meta.row + 1
                        },
                        {
                            data: "nm_brg",
                        },
                        {
                            data: "qty",
                            render: function(data, type, row) {
                                return `
                                <span class="qty-value" style="width: 5.5rem;">${data}</span>
                                <input type="text" inputmode="numeric" class="form-control qty-input d-none" style="width: 5.5rem;" value="${data}">
                            `;
                            }
                        },
                        {
                            data: "satuan",
                        },
                        {
                            data: "qty_std",
                            render: function(data, type, row) {
                                return `
                                <span class="qty-value-konversi" style="width: 5.5rem;">${data}</span>
                                <input type="text" inputmode="numeric" class="form-control qty-input-konversi d-none" style="width: 5.5rem;" value="${data}">
                            `;
                            }
                        },
                        {
                            data: "satuan_std"
                        },
                        {
                            data: null,
                            render: function(data, type, row) {
                                return `
                                <button class="btn btn-success waves-effect waves-light edit-btn"><i class="bx bx-edit align-middle font-size-14"></i> Edit</button>
                                <button class="btn btn-danger waves-effect waves-light cancel-btn d-none"><i class="bx bx-x-circle align-middle font-size-14"></i> Batal</button>
                                <button class="btn btn-primary waves-effect waves-light save-btn d-none"><i class="bx bx-save align-middle font-size-14"></i> Simpan</button>
                            `;
                            }
                        }
                    ],
                    rowCallback: function(row, data) {
                        $(row).attr('data-spek-id', data.spek_id);
                        $(row).data('konversi1', data.konversi1);
                    }
                });
            }

            $('#showDataBarangButton').on('click', function() {
                $('#editDataBarangModal').modal('show');

                if (!dataTableInitialized) {
                    initializeDataTable();
                    dataTableInitialized = true;
                }
            });

            $('#editDataBarangModal').on('click', '.edit-btn', function() {
                var $row = $(this).closest('tr');
                var konversi = parseFloat($row.data('konversi1'));

                $row.find('.qty-value').addClass('d-none');
                $row.find('.qty-value-konversi').addClass('d-none');
                $row.find('.qty-input').removeClass('d-none');
                $row.find('.qty-input-konversi').removeClass('d-none');
                $(this).addClass('d-none');
                $row.find('.save-btn').removeClass('d-none');
                $row.find('.cancel-btn').removeClass('d-none');

                $row.find('.qty-input').on('keyup', function() {
                    var qtyBeli = parseFloat($(this).val()) || 0;
                    var qtyStd = qtyBeli * konversi;
                    $row.find('.qty-input-konversi').val(qtyStd);
                });

                $row.find('.qty-input-konversi').on('keyup', function() {
                    var qtyStd = parseFloat($(this).val()) || 0;
                    var qtyBeli = qtyStd / konversi;
                    $row.find('.qty-input').val(qtyBeli);
                });
            });

            $('#editDataBarangModal').on('click', '.save-btn', function() {
                var $row = $(this).closest('tr');
                var qtyInput = $row.find('.qty-input');
                var qtyInputKonversi = $row.find('.qty-input-konversi');

                var qty = parseFloat(qtyInput.val());
                var qty_std = parseFloat(qtyInputKonversi.val());
                var spek_id = $row.data('spek-id');

                $.ajax({
                    url: "{{ route('pengiriman-skm.update', ['no_krmmsn' => $mutasi_id]) }}",
                    method: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        mutasi_id: "{{ $mutasi_id }}",
                        items: [{
                            spek_id: spek_id,
                            qty: qty,
                            qty_std: qty_std,
                        }]
                    },
                    success: function(response) {
                        $row.find('.qty-value').text(qty).removeClass('d-none');
                        $row.find('.qty-value-konversi').text(qty_std).removeClass('d-none');
                        qtyInput.addClass('d-none');
                        qtyInputKonversi.addClass('d-none');
                        $row.find('.save-btn').addClass('d-none');
                        $row.find('.edit-btn').removeClass('d-none');
                        $row.find('.cancel-btn').addClass('d-none');
                        Swal.fire({
                            toast: true,
                            position: 'top-right',
                            icon: response.success ? 'success' : 'error',
                            title: response.message,
                            showConfirmButton: false,
                            timer: 5000
                        });
                    },
                });
            });

            $('#editDataBarangModal').on('click', '.cancel-btn', function() {
                var $row = $(this).closest('tr');
                var QtyBeli = $row.find('.qty-value').text();
                var QtyStd = $row.find('.qty-value-konversi').text();
                $row.find('.qty-value').text(QtyBeli).removeClass('d-none');
                $row.find('.qty-value-konversi').text(QtyStd).removeClass('d-none');
                $row.find('.qty-input').addClass('d-none');
                $row.find('.qty-input-konversi').addClass('d-none');
                $(this).addClass('d-none');
                $row.find('.save-btn').addClass('d-none');
                $row.find('.edit-btn').removeClass('d-none');
            });

            function checkFormChanges() {
                let isChanged = false;

                $('#updateForm input, #updateForm select').each(function() {
                    if ($(this).is(':visible') && $(this).data('original-value') !== $(this).val()) {
                        isChanged = true;
                    }
                });
            }

            $('#updateForm input, #updateForm select').each(function() {
                $(this).data('original-value', $(this).val());
            });

            $('#updateForm input, #updateForm select').on('input change', checkFormChanges);
        });

        document.getElementById('updateForm').addEventListener('submit', function(e) {
            e.preventDefault();

            var form = this;
            var formData = new FormData(form);

            fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-right'
                        }).then(() => {
                            window.location.href = "{{ route('pengiriman-skm') }}";
                        });
                    }
                });
        });
        // Fungsi untuk memuat opsi dari server
        async function loadOptions() {
            try {
                // Mengambil data dari server menggunakan fetch API
                const tgl = document.getElementById('tgl').value;
                const response = await fetch('{{ route('pengiriman-skm.create') }}?tgl=' + tgl);
                const optionsData = await response.json();

                // Mendapatkan elemen select
                const select = document.getElementById('gdg_tujuan');

                // Mengosongkan opsi sebelumnya (jika ada)
                select.innerHTML = '<option value="">-- Pilih Mesin Tujuan --</option>';

                // Looping data dari server dan menambahkan opsi ke select
                optionsData.forEach(option => {
                    const newOption = document.createElement('option');
                    // document.getElementById('msn_trgt_id').value = option.msn_trgt_id;
                    newOption.value = option.mesin_id; // Nilai untuk setiap option (misalnya, ID)
                    newOption.text = '(Shift ' + option.shift + ') ' + option
                    .nama; // Teks yang akan ditampilkan
                    newOption.setAttribute('data-msn_trgt_id', option.msn_trgt_id);

                    // Jika opsi ini adalah yang terpilih, tambahkan atribut 'selected'
                    if (option.mesin_id == '{{ $gdg_tujuan }}') {
                        newOption.selected = true;
                        document.getElementById('msn_trgt_id').value = option.msn_trgt_id;
                    }

                    select.add(newOption);
                });
            } catch (error) {
                console.error('Error fetching data:', error);
            }
        }

        function getMsnTrgtId() {
            const selectedOption = document.getElementById('gdg_tujuan');
            const msnTrgtId = selectedOption.options[selectedOption.selectedIndex].dataset.msn_trgt_id;
            document.getElementById('msn_trgt_id').value = msnTrgtId;
        }

        // Memanggil loadOptions saat halaman pertama kali dimuat
        document.addEventListener('DOMContentLoaded', loadOptions);
</script>
@endpush

@section('content')
<!-- Page Title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Edit Pengiriman SKM ke Mesin</h4>
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
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h3 class="card-title fw-bolder">Data Transaksi</h3>
                <form id="updateForm" action="{{ route('pengiriman-skm.update', ['no_krmmsn' => $mutasi_id]) }}"
                    method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group mt-3">
                        <label for="mutasi_id">No. Mutasi :</label>
                        <input type="text" name="mutasi_id" id="mutasi_id" class="form-control" value="{{ $no_mutasi }}"
                            readonly>
                    </div>
                    <div class="form-group mt-3">
                        <label for="tgl">Tanggal :</label>
                        <input type="date" class="form-control" id="tgl" name="tgl" value="{{ $tgl }}" required
                            readonly>
                    </div>
                    <input type="hidden" name="msn_trgt_id" id="msn_trgt_id" value="">
                    <div class="form-group mt-3">
                        <label for="gdg_asal">Gudang Asal :</label>
                        <input type="text" class="form-control" name="gudang" id="gudang"
                            value="{{ $gudangs[0]->nama }}" readonly>
                        <input type="hidden" class="form-control" name="gdg_asal" id="gdg_asal"
                            value="{{ $gudang_id }}">
                        @error('gdg_asal')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mt-3">
                        <label for="gdg_tujuan">Mesin Tujuan :</label>
                        <select name="gdg_tujuan" id="gdg_tujuan" style="width: 100%"
                            class="form-control @error('gdg_tujuan') is-invalid @enderror" style="width: 100%;" required
                            onchange="getMsnTrgtId()">
                            <option value="">-- Memuat Mesin --</option>
                            {{-- @foreach ($mesins as $mesin)
                            <option value="{{ $mesin->mesin_id }}" {{ $gdg_tujuan==$mesin->mesin_id ? 'selected' : ''
                                }}>
                                {{ $mesin->nama }}
                            </option>
                            @endforeach --}}
                        </select>
                        @error('gdg_tujuan')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        <a href="{{ route('pengiriman-skm') }}" class="btn btn-secondary waves-effect waves-light me-2">
                            <i class="bx bx-caret-left align-middle me-2 font-size-18"></i>Kembali
                        </a>
                        <button type="submit" id="submitButton" class="btn btn-primary waves-effect waves-light">
                            <i class="bx bx bxs-save align-middle me-2 font-size-18"></i>Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <button type="button" class="btn btn-dark waves-effect waves-light" id="showDataBarangButton"
            data-toggle="modal" data-target="#editDataBarangModal">
            <i class="bx bx-edit align-middle me-2 font-size-18"></i>Edit Data Barang
        </button>
    </div>
</div>

<div class="modal fade modal-lg" id="editDataBarangModal" tabindex="-1" role="dialog"
    aria-labelledby="editDataBarangModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title fw-bolder" id="editDataBarangModalLabel">Data Barang</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="datatable-detail" class="table align-middle table-nowrap">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Nama Barang</th>
                                <th>Qty</th>
                                <th>Satuan</th>
                                <th>Qty Konversi</th>
                                <th>Satuan Konversi</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection