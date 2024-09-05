@extends('layouts.app')

@section('title')
Tambah Target Mesin
@endsection

@section('content')
<!-- Page Title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Tambah Target Mesin</h4>
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
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Data Transaksi</h5>
                <form action="{{ route('kinerja-mesin.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" class="form-control" name="shift_id" value="{{ $shifts[0]->shift_id}}" required
                        readonly>
                    <div class="form-group mt-3">
                        <label for="tgl">Mesin</label>
                        <select name="mesin_id" class="form-control">
                            @foreach ($mesins as $mesin)
                            <option value="{{ $mesin->mesin_id}}">{{ $mesin->nama}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mt-3">
                        <label for="qty">Jumlah</label>
                        <input type="text" class="form-control" name="qty" value="{{ old('qty')}}" required>
                    </div>
                    <div id="items-container"></div> <!-- Container for items input fields -->
                    <div class="d-flex justify-content-end mt-3">
                        <a href="{{ route('kinerja-mesin') }}" class="btn btn-info me-1">Kembali</a>
                        <button type="submit" class="btn btn-success">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


@endsection
