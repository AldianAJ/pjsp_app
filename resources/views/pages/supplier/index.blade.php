@extends('layouts.app')

@section('title')
    Supplier
@endsection

@push('after-style')
    <!-- Sweet Alert-->
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Supplier</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('supplier.create') }}" class="btn btn-primary my-2"><i
                                class="bx bx-plus-circle align-middle me-2 font-size-18"></i> Tambah</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID Supplier</th>
                                    <th>Nama Supplier</th>
                                    <th>Alamat</th>
                                    <th>Telp</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($suppliers as $supplier)
                                    <tr>
                                        <td>{{ $supplier->supplier_id }}</td>
                                        <td>{{ $supplier->nama }}</td>
                                        <td>{{ $supplier->address }}</td>
                                        <td>{{ $supplier->telp }}</td>
                                        <td>
                                            <a href="{{ route('supplier.edit', ['supplier_id' => $supplier->supplier_id]) }}"
                                                class="btn btn-secondary waves-effect waves-light mx-1">
                                                <i class="bx bx-edit align-middle me-2 font-size-18"></i> Edit
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (session()->has('success'))
        <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    toast: true,
                    position: 'bottom-right',
                    icon: 'success',
                    title: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 5000,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    },
                    customClass: {
                        popup: 'colored-toast'
                    },
                    showCloseButton: true
                });
            });
        </script>
    @endif
@endsection
