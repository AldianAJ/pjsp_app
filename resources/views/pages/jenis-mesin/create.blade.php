@extends('layouts.app')

@section('title')
    Tambah Jenis Mesin
@endsection

@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Tambah Jenis Mesin</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('jenis-mesin.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="mt-4 mb-3 row">
                            <label for="jenis_id" class="col-md-2 col-form-label font-size-14">ID Mesin</label>
                            <div class="col-md">
                                <input type="text" name="jenis_id" id="jenis_id" class="form-control">
                                @error('jenis_id')
                                    <p class="text-danger font-size-12 font-weight-bold">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4 mb-3 row">
                            <label for="nama" class="col-md-2 col-form-label font-size-14">Nama Mesin</label>
                            <div class="col-md">
                                <input type="text" name="nama" id="nama" class="form-control"
                                    value="{{ old('nama') }}">
                                @error('nama')
                                    <p class="text-danger font-size-12 font-weight-bold">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4 mb-3 d-flex justify-content-end">
                            <a href="{{ route('jenis-mesin') }}" class="btn btn-secondary font-size-14 mx-1"> <i
                                    class="bx bx-caret-left align-middle me-2 font-size-18"></i>Kembali</a>
                            <button type="submit" class="btn btn-success font-size-14 mx-1"><i
                                    class="bx bx-save align-middle me-2 font-size-18"></i>Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
