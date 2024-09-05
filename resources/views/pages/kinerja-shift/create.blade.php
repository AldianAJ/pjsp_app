@extends('layouts.app')

@section('title')
Tambah Target Shift
@endsection

@section('content')
<!-- Page Title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Tambah Target Shift</h4>
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
                <form action="{{ route('kinerja-shift.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" class="form-control" name="harian_id" value="{{ $harians[0]->harian_id}}"
                        required readonly>
                    <div class="form-group mt-3">
                        <label for="tgl">Shift</label>
                        <select name="shift" class="form-control">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                        </select>
                    </div>
                    <div class="form-group mt-3">
                        <label for="qty">Jumlah</label>
                        <input type="text" class="form-control" name="qty" value="{{ old('qty')}}" required>
                    </div>
                    <div id="items-container"></div> <!-- Container for items input fields -->
                    <div class="d-flex justify-content-end mt-3">
                        <a href="{{ route('kinerja-shift') }}" class="btn btn-info me-1">Kembali</a>
                        <button type="submit" class="btn btn-success">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


@endsection
