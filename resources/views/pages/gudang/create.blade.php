@extends('layouts.app')

@section('title')
    Tambah Gudang
@endsection

@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Tambah Gudang</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('gudang.store') }}" method="post" enctype="multipart/form-data">
                        @csrf

                        <div class="mt-4 mb-3 row">
                            <label for="jenis" class="col-md-2 col-form-label font-size-14">Penyimpanan</label>
                            <div class="col-md">
                                <select name="jenis" id="jenis" class="form-control">
                                    <option value="0">-- Pilih Penyimpanan --</option>
                                    <option value="1">Gudang</option>
                                    <option value="2">Mesin</option>
                                </select>
                                @error('jenis')
                                    <p class="text-danger font-size-12 font-weight-bold">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div id="gudangIdContainer" class="mt-4 mb-3 row d-none">
                            <label for="gudang_id" class="col-md-2 col-form-label font-size-14">ID Gudang</label>
                            <div class="col-md">
                                <input type="text" name="gudang_id" id="gudang_id" class="form-control"
                                    value="{{ $gudang_id }}" readonly>
                                @error('gudang_id')
                                    <p class="text-danger font-size-12 font-weight-bold">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div id="mesinIdContainer" class="mt-4 mb-3 row d-none">
                            <label for="mesin_id" class="col-md-2 col-form-label font-size-14">Nama Mesin</label>
                            <div class="col-md">
                                <select name="mesin_id" id="mesin_id" class="form-control">
                                    <option value="">-- Pilih Mesin --</option>
                                    @foreach ($Mesins as $mesin)
                                        <option value="{{ $mesin->mesin_id }}">{{ $mesin->nama }}</option>
                                    @endforeach
                                </select>
                                @error('mesin_id')
                                    <p class="text-danger font-size-12 font-weight-bold">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4 mb-3 row">
                            <label for="address" class="col-md-2 col-form-label font-size-14">Alamat</label>
                            <div class="col-md">
                                <input type="text" name="address" id="address" class="form-control"
                                    value="{{ old('address') }}">
                                @error('address')
                                    <p class="text-danger font-size-12 font-weight-bold">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4 mb-3 d-flex justify-content-end">
                            <a href="{{ route('gudang') }}" class="btn btn-secondary font-size-14 mx-1"> <i
                                    class="bx bx-caret-left align-middle me-2 font-size-18"></i>Kembali</a>
                            <button type="submit" class="btn btn-success font-size-14 mx-1"><i
                                    class="bx bx-save align-middle me-2 font-size-18"></i>Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const jenisSelect = document.getElementById('jenis');
            const gudangIdContainer = document.getElementById('gudangIdContainer');
            const mesinIdContainer = document.getElementById('mesinIdContainer');
            const addressInput = document.getElementById('address');

            function toggleFields() {
                const selectedValue = jenisSelect.value;

                gudangIdContainer.classList.toggle('d-none', selectedValue !== '1');
                mesinIdContainer.classList.toggle('d-none', selectedValue !== '2');
                addressInput.disabled = selectedValue === '0';
            }

            jenisSelect.addEventListener('change', toggleFields);
            toggleFields();
        });
    </script>
@endsection
