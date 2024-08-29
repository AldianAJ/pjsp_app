@extends('layouts.app')

@section('title')
    Edit Mesin
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
                <h4 class="mb-sm-0 font-size-18">Edit Mesin</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('mesin.update', ['mesin_id' => $data->mesin_id]) }}" method="post"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="mt-4 mb-3 row">
                            <label for="mesin_id" class="col-md-2 col-form-label font-size-14">ID Mesin</label>
                            <div class="col-md">
                                <input type="text" name="mesin_id" id="mesin_id" class="form-control"
                                    value="{{ $data->mesin_id }}" readonly>
                                @error('mesin_id')
                                    <p class="text-danger font-size-12 font-weight-bold">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4 mb-3 row">
                            <label for="jenis_id" class="col-md-2 col-form-label font-size-14">Jenis Mesin</label>
                            <div class="col-md">
                                <select name="jenis_id" id="jenis_id" class="form-control">
                                    <option value="">-- Pilih Mesin --</option>
                                    @foreach ($JenisMesins as $jenis)
                                        <option value="{{ $jenis->jenis_id }}"
                                            {{ $jenis->jenis_id == $data->jenis_id ? 'selected' : '' }}>
                                            {{ $jenis->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('jenis_id')
                                    <p class="text-danger font-size-12 font-weight-bold">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4 mb-3 row">
                            <label for="nama" class="col-md-2 col-form-label font-size-14">Nama Mesin</label>
                            <div class="col-md">
                                <input type="text" name="nama" id="nama" class="form-control"
                                    value="{{ $data->nama }}">
                                @error('nama')
                                    <p class="text-danger font-size-12 font-weight-bold">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4 mb-3 d-flex justify-content-end">
                            <a href="{{ route('mesin') }}" class="btn btn-secondary font-size-14 mx-1"> <i
                                    class="bx bx-caret-left align-mmesin_iddle me-2 font-size-18"></i>Kembali</a>
                            <button type="submit" class="btn btn-success font-size-14 mx-1"><i
                                    class="bx bx-save align-mmesin_iddle me-2 font-size-18"></i>Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
