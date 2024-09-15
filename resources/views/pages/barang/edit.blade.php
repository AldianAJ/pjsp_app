@extends('layouts.app')

@section('title')
Edit Barang
@endsection

@push('after-style')
<link rel="stylesheet" href="{{ asset('assets/libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}">
@endpush

@push('after-script')
<script src="{{ asset('assets/libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('assets/libs/tinymce/tinymce.min.js') }}"></script>
@endpush

@section('content')
<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Edit Barang</h4>
        </div>
    </div>
</div>
<!-- end page title -->
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('barang.update', ['brg_id' => $data->brg_id]) }}" method="post"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="mt-4 mb-3 row">
                        <label for="brg_id" class="col-md-2 col-form-label font-size-14">ID Barang</label>
                        <div class="col-md">
                            <input type="text" name="brg_id" id="brg_id" class="form-control"
                                value="{{ $data->brg_id }}" readonly>
                            @error('brg_id')
                            <p class="text-danger font-size-12 font-weight-bold">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4 mb-3 row">
                        <label for="supplier_id" class="col-md-2 col-form-label font-size-14">Supplier</label>
                        <div class="col-md">
                            <select name="supplier_id" id="supplier_id" class="form-control">
                                <option value="">-- Pilih Supplier --</option>
                                @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->supplier_id }}" {{ $supplier->supplier_id ==
                                    $data->supplier_id ? 'selected' : '' }}>
                                    {{ $supplier->nama }}
                                </option>
                                @endforeach
                            </select>
                            @error('supplier_id')
                            <p class="text-danger font-size-12 font-weight-bold">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4 mb-3 row">
                        <label for="nm_brg" class="col-md-2 col-form-label font-size-14">Nama Barang</label>
                        <div class="col-md">
                            <input type="text" name="nm_brg" id="nm_brg" class="form-control"
                                value="{{ $data->nm_brg }}">
                            @error('nm_brg')
                            <p class="text-danger font-size-12 font-weight-bold">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4 mb-3 row">
                        <label for="satuan_beli" class="col-md-2 col-form-label font-size-14">Satuan Beli</label>
                        <div class="col-md">
                            <input type="text" name="satuan_beli" id="satuan_beli" class="form-control"
                                value="{{ $data->satuan_beli }}">
                            @error('satuan_beli')
                            <p class="text-danger font-size-12 font-weight-bold">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4 mb-3 row">
                        <label for="konversi1" class="col-md-2 col-form-label font-size-14">Konversi 1</label>
                        <div class="col-md">
                            <input type="text" name="konversi1" id="konversi1" class="form-control"
                                value="{{ $data->konversi1 }}">
                            @error('konversi1')
                            <p class="text-danger font-size-12 font-weight-bold">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4 mb-3 row">
                        <label for="satuan_besar" class="col-md-2 col-form-label font-size-14">Satuan Besar</label>
                        <div class="col-md">
                            <input type="text" name="satuan_besar" id="satuan_besar" class="form-control"
                                value="{{ $data->satuan_besar }}">
                            @error('satuan_besar')
                            <p class="text-danger font-size-12 font-weight-bold">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4 mb-3 row">
                        <label for="konversi2" class="col-md-2 col-form-label font-size-14">Konversi 2</label>
                        <div class="col-md">
                            <input type="text" name="konversi2" id="konversi2" class="form-control"
                                value="{{ $data->konversi2 }}">
                            @error('konversi2')
                            <p class="text-danger font-size-12 font-weight-bold">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4 mb-3 row">
                        <label for="satuan_kecil" class="col-md-2 col-form-label font-size-14">Satuan Kecil</label>
                        <div class="col-md">
                            <input type="text" name="satuan_kecil" id="satuan_kecil" class="form-control"
                                value="{{ $data->satuan_kecil }}">
                            @error('satuan_kecil')
                            <p class="text-danger font-size-12 font-weight-bold">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4 mb-3 d-flex justify-content-end">
                        <a href="{{ route('barang') }}" class="btn btn-secondary font-size-14 mx-1"> <i
                                class="bx bx-caret-left align-middle me-2 font-size-18"></i>Kembali</a>
                        <button type="submit" class="btn btn-primary font-size-14 mx-1"><i
                                class="bx bx-save align-middle me-2 font-size-18"></i>Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection