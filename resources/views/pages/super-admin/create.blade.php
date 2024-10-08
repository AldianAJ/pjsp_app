@extends('layouts.app')

@section('title')
Tambah User
@endsection

@section('content')
<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Tambah User</h4>
        </div>
    </div>
</div>
<!-- end page title -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('super-admin.store') }}" method="post">
                    @csrf
                    <div class="mt-4 mb-3 row">
                        <label for="user_id" class="col-md-2 col-form-label font-size-14">ID User :</label>
                        <div class="col-md">
                            <input type="text" name="user_id" id="user_id" class="form-control" value="{{ $user_id }}"
                                readonly>
                        </div>
                    </div>

                    <div class="mt-4 mb-3 row">
                        <label for="nama" class="col-md-2 col-form-label font-size-14">Nama User :</label>
                        <div class="col-md">
                            <input type="text" name="nama" id="nama" class="form-control" value="{{ old('nama') }}">
                            @error('nama')
                            <p class="text-danger font-size-12 font-weight-bold">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4 mb-3 row">
                        <label for="username" class="col-md-2 col-form-label font-size-14">Username :</label>
                        <div class="col-md">
                            <input type="text" name="username" id="username" class="form-control"
                                value="{{ old('username') }}">
                            @error('username')
                            <p class="text-danger font-size-12 font-weight-bold">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4 mb-3 row">
                        <label for="password" class="col-md-2 col-form-label font-size-14">Password :</label>
                        <div class="col-md">
                            <input type="password" name="password" id="password" class="form-control">
                            @error('password')
                            <p class="text-danger font-size-12 font-weight-bold">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4 mb-3 row">
                        <label for="role" class="col-md-2 col-form-label font-size-14">Role :</label>
                        <div class="col-md">
                            <input type="text" name="role" id="role" class="form-control" value="{{ old('role') }}">
                            @error('role')
                            <p class="text-danger font-size-12 font-weight-bold">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4 mb-3 row">
                        <label for="gudang_id" class="col-md-2 col-form-label font-size-14">Gudang ID :</label>
                        <div class="col-md">
                            <input type="text" name="gudang_id" id="gudang_id" class="form-control"
                                value="{{ old('gudang_id') }}">
                            @error('gudang_id')
                            <p class="text-danger font-size-12 font-weight-bold">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4 mb-3 d-flex justify-content-end">
                        <a href="{{ route('super-admin') }}" class="btn btn-secondary waves-effect waves-light me-2">
                            <i class="bx bx-caret-left align-middle me-2 font-size-18"></i>Kembali
                        </a>
                        <button type="submit" class="btn btn-primary waves-effect waves-light">
                            <i class="bx bx bxs-save align-middle me-2 font-size-18"></i>Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection