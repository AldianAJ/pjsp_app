@extends('layouts.app')

@section('title')
Tambah Surat Jalan
@endsection

@push('after-app-style')
<link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@push('after-app-script')
<script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
            // Handling No PO selection
            $('#no_po').change(function() {
                var no_po = $(this).val();
                $.ajax({
                    url: '{{ route('penjualan.create') }}',
                    method: 'GET',
                    data: {
                        no_po: no_po
                    },
                    success: function(data) {
                        $('#datatable-spek tbody').empty();
                        $.each(data.data, function(index, spek) {
                            $('#datatable-spek tbody').append(
                                `<tr>
                            <td>${index + 1}</td>
                            <td>${spek.spek}</td>
                            <td>${spek.qty_krm}</td>
                            <td>${spek.satuan_po}</td>
                            <td><button class="btn btn-primary select-spek" data-spek-id="${spek.spek_id}" data-spek="${spek.spek}" data-qty="${spek.qty_krm}" data-satuan="${spek.satuan_po}">Pilih</button></td>
                        </tr>`
                            );
                        });
                        $('#spekModal').modal('show');
                    }
                });
            });

            // Selecting a spek
            $(document).on('click', '.select-spek', function() {
                var spek = $(this).data('spek');
                var qty = $(this).data('qty');
                var satuan = $(this).data('satuan');
                var spekId = $(this).data('spek-id');

                $('#spek').val(spek);
                $('#qty_kirim').val(qty);
                $('#satuan').text(satuan);
                $('#keterangan').val(`${qty} ${satuan}`); // Ensure proper template literal usage
                $('#spek').data('spek-id', spekId); // Save spek-id in the input for later use
                $('#qtyModal').modal('show');
            });

            // Update keterangan on quantity change
            $('#qty_kirim').on('input', function() {
                var qty = $(this).val();
                var satuan = $('#satuan').text();
                $('#keterangan').val(`${qty} ${satuan}`); // Correct usage of template literal
            });

            let itemCount = 0;

            // Add item to the selected list
            $('#addItem').click(function() {
                itemCount++;
                var spek = $('#spek').val();
                var qty_kirim = parseFloat($('#qty_kirim').val());
                var no_batch = $('#no_batch').val();
                var keterangan = $('#keterangan').val();
                var spekId = $('#spek').data('spek-id');

                $('#selected-items tbody').append(
                    `<tr data-spek-id="${spekId}">
                <td>${itemCount}</td>
                <td>${spek}</td>
                <td>${no_batch}</td>
                <td>${keterangan}</td>
                <td><button class="btn btn-danger remove-item"><i class="bx bxs-trash align-middle font-size-14"></i></button></td>
            </tr>`
                );

                $('#qty_kirim').val('');
                $('#no_batch').val('');
                $('#keterangan').val('');
                $('#qtyModal').modal('hide');
            });

            // Remove item from the list
            $(document).on('click', '.remove-item', function() {
                $(this).closest('tr').remove();
                itemCount--;
                $('#selected-items tbody tr').each(function(index) {
                    $(this).find('td:first').text(index + 1);
                });
            });

            // Form submission handling
            // Penanganan pengiriman form
            $('form').submit(function(e) {
                e.preventDefault();

                let items = [];
                $('#selected-items tbody tr').each(function() {
                    const row = $(this);
                    const spek_id = row.data('spek-id');
                    const no_batch = row.find('td').eq(2).text();
                    const qty_krm = parseFloat(row.find('td').eq(3).text().split(' ')[
                    0]);
                    

                    items.push({
                        spek_id: spek_id,
                        no_batch: no_batch,
                        qty_krm: qty_krm, // Simpan hanya kuantitas tanpa satuan
                        ket: row.find('td').eq(3).text() // Ambil keterangan jika diperlukan
                    });
                });

                // Cek jika array items tidak kosong
                if (items.length === 0) {
                    alert('Silakan tambahkan setidaknya satu item.');
                    return;
                }

                $('<input>').attr({
                    type: 'hidden',
                    name: 'items',
                    value: JSON.stringify(items),
                }).appendTo('form');

                this.submit();
            });

        });
</script>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Tambah Surat Jalan</h4>
        </div>
    </div>
</div>

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
                <h5 class="card-title">Data Transaksi</h5>
                <form action="{{ route('penjualan.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group mt-3">
                        <label for="tgl">Tanggal :</label>
                        <input type="date" class="form-control" name="tgl"
                            value="{{ old('tgl', \Carbon\Carbon::now()->format('Y-m-d')) }}" required>
                    </div>
                    <div class="form-group mt-3">
                        <label for="no_po">No. PO :</label>
                        <select name="no_po" id="no_po" class="form-control @error('no_po') is-invalid @enderror"
                            required>
                            <option value="">-- Pilih No. PO --</option>
                            @foreach ($hms_poo as $hms_po)
                            <option value="{{ $hms_po->no_po }}">{{ $hms_po->no_po }}</option>
                            @endforeach
                        </select>
                        @error('no_po')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group mt-3">
                        <label for="no_segel">No. Segel:</label>
                        <input type="text" class="form-control @error('no_segel') is-invalid @enderror" name="no_segel"
                            value="{{ old('no_segel') }}" required>
                        @error('no_segel')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group mt-3">
                        <label for="no_pol">Armada :</label>
                        <select name="no_pol" id="no_pol" class="form-control @error('no_pol') is-invalid @enderror"
                            required>
                            <option value="">-- Pilih Armada --</option>
                            @foreach ($armadas as $armada)
                            <option value="{{ $armada->no_pol }}">{{ $armada->no_pol }}</option>
                            @endforeach
                        </select>
                        @error('no_pol')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group mt-3">
                        <label for="driver">Driver :</label>
                        <input type="text" class="form-control @error('driver') is-invalid @enderror" name="driver"
                            value="{{ old('driver') }}" required>
                        @error('driver')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <input type="hidden" name="cust_id" id="cust_id" value="{{ old('cust_id', $cust_id ?? '') }}">
                    <div class="d-flex justify-content-end mt-3">
                        <a href="{{ route('penjualan') }}" class="btn btn-secondary waves-effect waves-light me-2">
                            <i class="bx bx-caret-left align-middle me-2 font-size-18"></i>Kembali
                        </a>
                        <button type="submit" class="btn btn-primary waves-effect waves-light" id="saveButton">
                            <i class="bx bx bxs-save align-middle me-2 font-size-18"></i>Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk memilih spek -->
<div class="modal fade" id="spekModal" tabindex="-1" role="dialog" aria-labelledby="dataModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pilih Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="table-responsive">
                            <table id="datatable-spek" class="table align-middle table-nowrap">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Spek</th>
                                        <th>Qty Kirim</th>
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

<!-- Modal untuk input qty -->
<div class="modal fade" id="qtyModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Input Qty</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="spek" class="form-label">Nama Barang :</label>
                    <input type="text" id="spek" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label for="qty_kirim" class="form-label">Qty Kirim :</label>
                    <div class="d-flex align-items-center">
                        <input type="text" inputmode="numeric" id="qty_kirim" class="form-control me-2" required>
                        <label for="satuan" class="form-label fw-bolder" id="satuan"></label>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="no_batch" class="form-label">No. Batch :</label>
                    <input type="text" id="no_batch" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="keterangan" class="form-label">Keterangan :</label>
                    <input type="text" id="keterangan" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="addItem">Tambah</button>
            </div>
        </div>
    </div>
</div>

<!-- Tabel untuk menampilkan detail pengiriman -->
<div class="row">
    <div class="col-lg-12 mt-2">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Detail Pengiriman</h5>
                <div class="table-responsive">
                    <table class="table table-striped" id="selected-items">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Spek</th>
                                <th>No Batch</th>
                                <th>Keterangan</th>
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