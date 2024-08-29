@extends('layouts.app')

@section('title')
    Tambah Persediaan Masuk
@endsection

@section('content')
    <!-- Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Tambah Persediaan Masuk</h4>
            </div>
        </div>
    </div>

    <!-- Main Form -->
    <div class="row">
        <div class="col-md-6">
            <!-- Data Barang Table -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Data Barang</h5>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID Barang</th>
                                <th>Nama Barang</th>
                                <th>Satuan</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($barangs as $barang)
                                <tr>
                                    <td>{{ $barang->brg_id }}</td>
                                    <td>{{ $barang->nm_brg }}</td>
                                    <td>{{ $barang->satuan_besar }}</td>
                                    <td>
                                        <button type="button" class="btn btn-primary btn-sm font-size-14"
                                            onclick="showModal('{{ $barang->brg_id }}', '{{ $barang->nm_brg }}', '{{ $barang->satuan_besar }}')">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <!-- List Persediaan Masuk -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">List Persediaan Masuk</h5>
                    <table class="table table-striped" id="selected-items-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Barang</th>
                                <th>Jumlah</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="selected-items">
                            <!-- Selected items will be appended here dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Input Quantity -->
    <div class="modal fade" id="qtyModal" tabindex="-1" role="dialog" aria-labelledby="qtyModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="qtyModalLabel">Input Quantity</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="qtyForm">
                        <input type="hidden" id="modal-brg-id">
                        <div class="form-group">
                            <label for="modal-nm-brg">Nama Barang</label>
                            <input type="text" class="form-control" id="modal-nm-brg" readonly>
                        </div>
                        <div class="form-group mt-3">
                            <label for="modal-qty">Jumlah</label>
                            <input type="number" class="form-control" id="modal-qty">
                        </div>
                        <div class="form-group mt-3">
                            <label for="modal-satuan-besar">Satuan</label>
                            <input type="text" class="form-control" id="modal-satuan-besar" readonly>
                        </div>
                        <div class="form-group mt-3">
                            <label for="modal-ket">Keterangan</label>
                            <input type="text" class="form-control" id="modal-ket">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="addItem()">Tambah</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Form for Document Details -->
    <div class="row mt-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Data Transaksi</h5>
                    <form action="{{ route('stok-masuk.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group mt-3">
                            <label for="no_trm">No. Dokumen</label>
                            <input type="text" class="form-control" name="no_trm" value="{{ $NoTrms }}" required>
                        </div>
                        <div class="form-group mt-3">
                            <label for="no_sj">No. SJ</label>
                            <input type="text" class="form-control" name="no_sj" value="{{ old('no_sj') }}" required>
                        </div>
                        <div class="form-group mt-3">
                            <label for="supplier_id">Nama Supplier</label>
                            <select name="supplier_id" class="form-control" required>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->supplier_id }}">{{ $supplier->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mt-3">
                            <label for="tgl">Tanggal</label>
                            <input type="text" class="form-control" name="tgl"
                                value="{{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM YYYY') }}" readonly>
                        </div>
                        <input type="hidden" name="items" id="items-input">
                        <div class="d-flex justify-content-end mt-3">
                            <button type="submit" class="btn btn-success"
                                onclick="prepareFormSubmission()">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let selectedItems = [];

        function showModal(brg_id, nm_brg, satuan_besar) {
            document.getElementById('modal-brg-id').value = brg_id;
            document.getElementById('modal-nm-brg').value = nm_brg;
            document.getElementById('modal-satuan-besar').value = satuan_besar;
            document.getElementById('modal-qty').value = '';
            document.getElementById('modal-ket').value = '';
            new bootstrap.Modal(document.getElementById('qtyModal')).show();
        }

        function addItem() {
            const brg_id = document.getElementById('modal-brg-id').value;
            const nm_brg = document.getElementById('modal-nm-brg').value;
            const qty = document.getElementById('modal-qty').value;
            const satuan_besar = document.getElementById('modal-satuan-besar').value;
            const ket = document.getElementById('modal-ket').value;

            selectedItems.push({
                brg_id,
                nm_brg,
                qty,
                satuan_besar,
                ket
            });

            renderItems();
            bootstrap.Modal.getInstance(document.getElementById('qtyModal')).hide();
        }

        function removeItem(index) {
            selectedItems.splice(index, 1);
            renderItems();
        }

        function renderItems() {
            document.getElementById('selected-items').innerHTML = '';
            selectedItems.forEach((item, index) => {
                const itemHTML = `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${item.nm_brg}</td>
                        <td>${item.qty} ${item.satuan_besar}</td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm" onclick="removeItem(${index})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                document.getElementById('selected-items').insertAdjacentHTML('beforeend', itemHTML);
            });
        }

        function prepareFormSubmission() {
            document.getElementById('items-input').value = JSON.stringify(selectedItems);
        }
    </script>
@endsection
