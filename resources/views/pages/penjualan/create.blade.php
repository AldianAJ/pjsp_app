@extends('layouts.app')

@section('title')
Tambah Surat Jalan
@endsection

@push('after-app-style')
<link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container .select2-selection--single {
        padding: 0.30rem 0.45rem;
        height: 38.2px;
    }
</style>
@endpush

@push('after-app-script')
<script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        $('#no_po').change(function() {
            var no_po = $(this).val();
            if (no_po) {
                $.ajax({
                    url: '{{ route('penjualan.create') }}',
                    method: 'GET',
                    data: { no_po: no_po },
                    success: function(data) {
                        $('#datatable-spek tbody').empty();
                        $.each(data.data, function(index, spek) {
                            $('#datatable-spek tbody').append(
                                `<tr>
                                    <td>${index + 1}</td>
                                    <td>${spek.spek}</td>
                                    <td>${Math.floor(spek.qty_po)}</td>
                                    <td>${spek.satuan1}</td>
                                    <td>
                                        <button class="btn btn-primary select-spek"
                                                onclick="selectSpek('${spek.spek}', ${Math.floor(spek.qty_po)}, '${spek.satuan1}', '${spek.satuan1}', ${spek.konversi1}, '${spek.satuan2}', '${spek.spek_id}')">Pilih</button>
                                    </td>
                                </tr>`
                            );
                        });
                        $('#spekModal').modal('show');
                    }
                });
            }
        });

        window.selectSpek = function(spek, qty, satuan1Param, satuan1, konversi, satuan2, spekId) {
            $('#spek').val(spek);
            $('#qty_po').val(Math.floor(qty));
            $('#konversi1').val(konversi);
            $('#satuan').text(satuan1);
            $('#qty_po').data('satuan1', satuan1Param);
            $('#qty_po').data('konversi', konversi);
            $('#qty_po').data('satuan2', satuan2);
            $('#keterangan').val(`${Math.floor(qty)} ${satuan1Param} @${qty * konversi} ${satuan2}`);
            $('#spek').data('spek-id', spekId);
            $('#qtyModal').modal('show');
        };

        $('#qty_po').on('input', function() {
            var qty = Math.floor($(this).val());
            var satuan1 = $('#qty_po').data('satuan1');
            var konversi = parseFloat($('#qty_po').data('konversi')) || 0;
            var satuan2 = $('#qty_po').data('satuan2');

            var qtySatuan2 = qty * konversi;

            if (!isNaN(qtySatuan2)) {
                $('#keterangan').val(`${qty} ${satuan1} @${qtySatuan2} ${satuan2}`);
            } else {
                $('#keterangan').val(`${qty} ${satuan1} @0 ${satuan2}`);
            }
        });

        let itemCount = 0;

        $('#addItem').click(function() {
            itemCount++;
            var spek = $('#spek').val();
            var qty_po = parseFloat($('#qty_po').val());
            var no_batch = $('#no_batch').val();
            var keterangan = $('#keterangan').val();
            var spekId = $('#spek').data('spek-id');
            var konversi = parseFloat($('#qty_po').data('konversi')) || 0;

            var qtySatuan2 = qty_po * konversi;

            $('#selected-items tbody').append(
                `<tr data-spek-id="${spekId}">
                    <td>${itemCount}</td>
                    <td>${spek}</td>
                    <td>${no_batch}</td>
                    <td>${keterangan}</td>
                    <td><button class="btn btn-danger remove-item"><i class="bx bxs-trash align-middle font-size-14"></i></button></td>
                </tr>`
            );

            $('#selected-items tbody tr:last-child').data('qty-pack', qtySatuan2);

            $('#qty_po').val('');
            $('#no_batch').val('');
            $('#keterangan').val('');
            $('#qtyModal').modal('hide');
        });

        $(document).on('click', '.remove-item', function() {
            $(this).closest('tr').remove();
            itemCount--;
            $('#selected-items tbody tr').each(function(index) {
                $(this).find('td:first').text(index + 1);
            });
        });

        $('form').submit(function(e) {
            e.preventDefault();

            let items = [];
            $('#selected-items tbody tr').each(function() {
                const row = $(this);
                const spek_id = row.data('spek-id');
                const no_batch = row.find('td').eq(2).text();
                const qty_po = parseFloat(row.find('td').eq(3).text().split(' ')[0]);
                const qty_total = row.data('qty-pack');

                items.push({
                    spek_id: spek_id,
                    no_batch: no_batch,
                    qty_po: qty_po,
                    qty_total: qty_total,
                    ket: row.find('td').eq(3).text()
                });
            });

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

        $('#no_po, #no_pol').select2({
            width: 'resolve'
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
                        <select name="no_po" id="no_po" style="width: 100%"
                            class="form-control @error('no_po') is-invalid @enderror" required>
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
                        <select name="no_pol" id="no_pol" style="width: 100%"
                            class="form-control @error('no_pol') is-invalid @enderror" required>
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

<div class="modal fade" id="spekModal" tabindex="-1" role="dialog" aria-labelledby="dataModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title fw-bolder">Pilih Barang</h3>
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
                                        <th>Qty PO</th>
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
                <h3 class="modal-title fw-bolder">Input Qty</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="spek" class="form-label">Nama Barang :</label>
                    <input type="text" id="spek" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label for="qty_po" class="form-label">Qty PO :</label>
                    <div class="d-flex align-items-center">
                        <input type="text" inputmode="numeric" id="qty_po" class="form-control me-2" required>
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

<div class="row">
    <div class="col-lg-12 mt-2">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title fw-bolder">Detail Pengiriman</h4>
                <div class="table-responsive">
                    <table class="table table-striped" id="selected-items">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Spek</th>
                                <th>No. Batch</th>
                                <th>Ket</th>
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
