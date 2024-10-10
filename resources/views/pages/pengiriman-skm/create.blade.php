@extends('layouts.app')

@section('title')
Tambah Pengiriman ke Mesin
@endsection

@push('after-app-style')
<link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@push('after-app-script')
<script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<!-- Responsive examples -->
<script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/datatables.init.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/autonumeric/4.10.5/autoNumeric.min.js"></script>
<script>
    $(document).ready(function() {
            $('#showDataBarangButton').on('click', function() {
                if ($.fn.DataTable.isDataTable('#datatable-spek')) {
                    $('#datatable-spek').DataTable().clear().destroy();
                }

                $('#datatable-spek').DataTable({
                    ajax: {
                        url: "{{ route('pengiriman-skm.create') }}",
                        data: {
                            type: 'speks',
                        }
                    },
                    processing: true,
                    ordering: false,
                    lengthMenu: [5],
                    columns: [{
                            data: null,
                            render: (data, type, row, meta) => meta.row + 1
                        },
                        {
                            data: "nm_brg"
                        },
                        {
                            data: "spek"
                        },
                        {
                            data: "satuan1"
                        },
                        {
                            data: null,
                            render: (data, type, row) => `<button type="button" class="btn btn-primary font-size-14 waves-effect waves-light" onclick="showQtyModal('${row.brg_id}', '${row.nm_brg}', '${row.satuan1}', '${row.satuan2}', '${row.konversi1}', '${row.spek_id}', '${row.spek}')">
                                Pilih
                                </button>`
                        }
                    ],
                });

                $('#dataBarang').modal('hide');
                $('#dataSpek').modal('show');
            });

            $('input[name="tgl"]').on('change', function() {
                const selectedDate = $(this).val();

                if ($.fn.DataTable.isDataTable('#datatable-machines')) {
                    $('#datatable-machines').DataTable().clear().destroy();
                }

                $('#datatable-machines').DataTable({
                    ajax: {
                        url: "{{ route('pengiriman-skm.create') }}",
                        data: {
                            type: 'machines',
                            date: selectedDate,
                        }
                    },
                    orderprocessing: true,
                    ordering: false,
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
                        <button type="button" class="btn btn-primary font-size-14 waves-effect waves-light" onclick="select('${row.msn_trgt_id}','${row.mesin_id}')">
                        Pilih
                    </button>`
                        }
                    ]
                });

                $('#dataMesin').modal('show');

            });

            window.showQtyModal = function(brg_id, nm_brg, satuan1, satuan2, konversi1, spek_id, spek) {
                const modal = document.getElementById('qtyModal');
                const msn_trgt_id = document.getElementById('msn_trgt_id').value
                document.getElementById('modal-brg-id').value = brg_id;
                document.getElementById('modal-nm-brg').value = nm_brg;
                document.getElementById('modal-qty-beli').value = '';
                document.getElementById('modal-satuan1').innerText = satuan1;
                document.getElementById('modal-qty-std').value = '';
                document.getElementById('modal-konversi1').value = konversi1;
                document.getElementById('modal-satuan2').innerText = satuan2;
                document.getElementById('modal-ket').value = msn_trgt_id;
                // document.getElementById('modal-ket').value = spek;
                document.getElementById('modal-spek-id').value = spek_id;

                $('#dataSpek').modal('hide');
                $('#dataBarang').modal('hide');
                new bootstrap.Modal(modal).show();
            };

            $('#qtyModal .btn-primary').on('click', function() {
                addItem();
                $('#qtyModal').modal('hide');
                $('#dataSpek').modal('show');
            });

            new AutoNumeric('#modal-qty-beli', {
                decimalCharacter: ',',
                digitGroupSeparator: '.'
            });
            new AutoNumeric('#modal-qty-std', {
                decimalCharacter: ',',
                digitGroupSeparator: '.'
            });

            $('#modal-qty-beli').on('keyup', function() {
                const qtyBeli = parseFloat(AutoNumeric.getNumericString(this)) || 0;
                const konversi1 = parseFloat($('#modal-konversi1').val());
                const qtyStd = qtyBeli * konversi1;

                AutoNumeric.set('#modal-qty-std', qtyStd);
            });

            $('#modal-qty-std').on('keyup', function() {
                const qtyStd = parseFloat(AutoNumeric.getNumericString(this)) || 0;
                const konversi1 = ($('#modal-konversi1').val());
                const qtyBeli = qtyStd / konversi1;

                AutoNumeric.set('#modal-qty-beli', qtyBeli);
            });

            let selectedItems = [];

            function addItem() {
                const brg_id = document.getElementById('modal-brg-id').value;
                const nm_brg = document.getElementById('modal-nm-brg').value;
                const qty_beli = parseFloat(AutoNumeric.getNumericString(document.getElementById(
                    'modal-qty-beli'))) || 0;
                const satuan1 = document.getElementById('modal-satuan1').innerText;
                const konversi1 = parseFloat(document.getElementById('modal-konversi1').value) || 0;
                const qty_std = parseFloat(AutoNumeric.getNumericString(document.getElementById(
                    'modal-qty-std'))) || 0;
                const satuan2 = document.getElementById('modal-satuan2').innerText;
                const ket = document.getElementById('modal-ket').value;
                const spek_id = document.getElementById('modal-spek-id').value;

                if (qty_beli <= 0 || qty_std <= 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Tidak Bisa',
                        text: 'Qty harus lebih dari 0',
                    });
                    return;
                }

                selectedItems.push({
                    brg_id,
                    nm_brg,
                    qty_beli,
                    satuan1,
                    qty_std,
                    satuan2,
                    ket,
                    spek_id,
                });
                updateItems();

                AutoNumeric.set('#modal-qty-beli', 0);
                AutoNumeric.set('#modal-qty-std', 0);
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
                <td>${item.nm_brg}</td>
                <td>${item.ket}</td>
                <td>${item.qty_beli}</td>
                <td>${item.satuan1}</td>
                <td>
                    <button class="btn btn-danger waves-effect waves-light" onclick="removeItem(${index})">
                        <i class="bx bxs-trash align-middle font-size-14"></i>
                    </button>
                </td>
            </tr>
        `).join('');

                itemsContainer.innerHTML = selectedItems.map((item, index) => `
            <input type="hidden" name="items[${index}][brg_id]" value="${item.brg_id}">
            <input type="hidden" name="items[${index}][qty_beli]" value="${item.qty_beli}">
            <input type="hidden" name="items[${index}][satuan_beli]" value="${item.satuan1}">
            <input type="hidden" name="items[${index}][qty_std]" value="${item.qty_std}">
            <input type="hidden" name="items[${index}][satuan_std]" value="${item.satuan2}">
            <input type="hidden" name="items[${index}][ket]" value="${item.ket}">
            <input type="hidden" name="items[${index}][spek_id]" value="${item.spek_id}">
        `).join('');

                saveButton.disabled = selectedItems.length === 0;
            }
        });

        function select(msn_trgt_id, gdg_tujuan) {
            document.getElementById('msn_trgt_id').value = msn_trgt_id;
            document.getElementById('gdg_tujuan').value = gdg_tujuan;

            $('#dataMesin').modal('hide');
        }

        function pilihTgl() {
            const selectedDate = document.getElementById('tgl').value;

            if ($.fn.DataTable.isDataTable('#datatable-machines')) {
                $('#datatable-machines').DataTable().clear().destroy();
            }

            $('#datatable-machines').DataTable({
                ajax: {
                    url: "{{ route('pengiriman-skm.create') }}",
                    data: {
                        type: 'machines',
                        date: selectedDate,
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
                        <button type="button" class="btn btn-primary font-size-14 waves-effect waves-light" onclick="select('${row.msn_trgt_id}', '${row.mesin_id}')">
                        Pilih
                    </button>`
                    }
                ]
            });

            $('#dataMesin').modal('show');
        }

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
                    newOption.value =  option.mesin_id; // Nilai untuk setiap option (misalnya, ID)
                    newOption.text = '(Shift ' + option.shift + ') ' + option.nama; // Teks yang akan ditampilkan
                    newOption.setAttribute('data-msn_trgt_id', option.msn_trgt_id);
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
            <h4 class="mb-sm-0 font-size-18">Tambah Pengiriman ke Mesin</h4>
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
                <form action="{{ route('pengiriman-skm.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    {{-- <div class="form-group mt-3">
                        <label for="no_trm">No. Dokumen</label>
                        <input type="text" class="form-control" name="no_trm" value="{{ old('no_trm', $no_trm) }}"
                            required>
                    </div> --}}
                    <div class="form-group mt-3">
                        <label for="tgl">Tanggal</label>
                        <input type="date" class="form-control" name="tgl" id="tgl"
                            value="{{ old('tgl', \Carbon\Carbon::now()->format('Y-m-d')) }}" required>
                    </div>
                    <input type="hidden" name="msn_trgt_id" id="msn_trgt_id" value="">
                    <div class="form-group mt-3">
                        <label for="gdg_asal">Gudang Asal :</label>
                        <input type="text" class="form-control" name="gudang" id="gudang"
                            value="{{ $gudangs[0]->nama }}" readonly>
                        <input type="hidden" class="form-control" name="gdg_asal" id="gdg_asal" value="{{ $gudang_id }}"
                            readonly>
                        {{-- <select name="gdg_asal" id="gdg_asal" style="width: 100%"
                            class="form-control @error('gdg_asal') is-invalid @enderror" style="width: 100%;" required>
                            <option value="{{ $gudang_id }}" selected>{{ $gudangs[0]->nama }}</option>
                            @foreach ($mesins as $mesin)
                            <option value="{{ $mesin->mesin_id }}">
                                {{ $mesin->nama }}
                            </option>
                            @endforeach
                        </select> --}}
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
                            <option value="{{ $mesin->mesin_id }}">
                                {{ $mesin->nama }}
                            </option>
                            @endforeach --}}
                        </select>
                        @error('gdg_tujuan')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div id="items-container"></div> <!-- Container for items input fields -->
                    <div class="d-flex justify-content-end mt-3">
                        <a href="{{ route('pengiriman-skm') }}" class="btn btn-secondary waves-effect waves-light me-2">
                            <i class="bx bx-caret-left align-middle me-2 font-size-18"></i>Kembali
                        </a>
                        <button type="submit" class="btn btn-primary waves-effect waves-light" id="saveButton" disabled>
                            <i class="bx bx bxs-save align-middle me-2 font-size-18"></i>Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <button type="button" class="btn btn-dark waves-effect waves-light" id="showDataBarangButton">
            <i class="bx bx-plus-circle align-middle me-2 font-size-18"></i>Tambah Data Barang
        </button>
    </div>
</div>

<div class="modal fade" id="dataShift" tabindex="-1" role="dialog" aria-labelledby="shiftModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="shiftModalLabel">Pilih Shift</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="table-responsive">
                            <table id="datatable-shifts" class="table align-middle table-nowrap">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Shift</th>
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

<div class="modal fade" id="dataBarang" tabindex="-1" role="dialog" aria-labelledby="dataModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dataModalLabel">Data Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="table-responsive">
                            <table id="datatable-barang" class="table align-middle table-nowrap">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Barang</th>
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
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Selesai</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Input Quantity -->
<div class="modal fade" id="dataSpek" tabindex="-1" role="dialog" aria-labelledby="dataSpekLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dataSpekLabel">Data Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table id="datatable-spek" class="table align-middle table-wrap">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Barang</th>
                                        <th>Spesifikasi</th>
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
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Selesai</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="qtyModal" tabindex="-1" role="dialog" aria-labelledby="qtyModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="qtyModalLabel">Input Qty</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="modal-brg-id">
                <div class="mb-3">
                    <label for="modal-nm-brg" class="form-label">Nama Barang</label>
                    <input type="text" class="form-control" id="modal-nm-brg" readonly>
                </div>
                <input type="hidden" id="modal-spek-id">
                <div class="mb-3">
                    <label for="modal-qty" class="form-label">Qty</label>
                    <div class="d-flex align-items-center">
                        <input type="text" inputmode="numeric" class="form-control me-2" id="modal-qty-beli" min="1"
                            required>
                        <label for="modal-satuan1" class="form-label fw-bolder" id="modal-satuan1"></label>
                    </div>
                </div>
                <input type="hidden" id="modal-konversi1">
                <div class="mb-3">
                    <label for="modal-qty-std" class="form-label">Qty Konversi</label>
                    <div class="d-flex align-items-center">
                        <input type="text" inputmode="numeric" class="form-control me-2" id="modal-qty-std" min="1"
                            required>
                        <label for="modal-satuan2" class="form-label fw-bolder" id="modal-satuan2"></label>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="modal-ket" class="form-label">Keterangan</label>
                    <textarea class="form-control" id="modal-ket"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="addItem()">Tambah</button>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 mt-2">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">List Kirim Barang</h5>
                <div class="table-responsive">
                    <table class="table table-striped" id="selected-items-table">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Ket</th>
                                <th>Qty</th>
                                <th>Satuan</th>
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