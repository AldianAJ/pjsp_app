@extends('layouts.app')

@section('title')
    Closing Mesin
@endsection

@push('after-style')
    <!-- Sweet Alert-->
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@push('after-app-script')
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <!-- Responsive examples -->
    <script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/datatables.init.js') }}"></script>
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/form-wizard.init.js') }}"></script>
    <script>
        $('#datatableDetail').DataTable({
            ajax: {
                url: "{{ route('closing-mesin') }}",
                type: "GET",
            },
            columns: [{
                    data: "target_shift.target_hari.target_week.barang.nm_brg"
                },
                {
                    data: "target_shift.shift"
                },
                {
                    data: "mesin.nama"
                },
                {
                    data: "action"
                }

            ],

        });

        $('#datatableDetail').on('click', '.btn-process, .btn-detailHari', function() {
            const weekId = $(this).data('msn-trgt-id');
            const row = $(this).closest('tr');
            const data = $('#datatableDetail').DataTable().row(row).data();
            if ($(this).hasClass('btn-process') || $(this).hasClass('btn-detailHari')) {
                $('#week_id').val(weekId);
                window.currentWeekId = weekId;
                $('#createModal').modal('show');
            }
        });
    </script>
@endpush

@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Closing Mesin</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- Filter Toolbar -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="filterTahun">Tanggal:</label>
                            <input type="text" name="tgl" id="tgl" class="form-control mb-3"
                                value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                        </div>

                    </div>
                    <div class="table-responsive">
                        <table id="datatableDetail" class="table align-middle table-nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th>Barang</th>
                                    <th>Shift</th>
                                    <th>Mesin</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal create-->
    <div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="modalTitle" class="modal-title">Hasil dan Reject</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title mb-4">Basic Wizard</h4>

                                    <div id="basic-example" role="application" class="wizard clearfix">
                                        <div class="steps clearfix">
                                            <ul role="tablist">
                                                <li role="tab" class="first current" aria-disabled="false"
                                                    aria-selected="true"><a id="basic-example-t-0" href="#basic-example-h-0"
                                                        aria-controls="basic-example-p-0"><span
                                                            class="current-info audible">current step: </span><span
                                                            class="number">1.</span> Hasil</a></li>
                                                <li role="tab" class="disabled" aria-disabled="true"><a
                                                        id="basic-example-t-1" href="#basic-example-h-1"
                                                        aria-controls="basic-example-p-1"><span class="number">2.</span>
                                                        Reject</a></li>
                                            </ul>
                                        </div>
                                        <div class="content clearfix">
                                            <!-- Hasil -->
                                            <h3 id="basic-example-h-0" tabindex="-1" class="title current">Hasil
                                            </h3>
                                            <section id="basic-example-p-0" role="tabpanel"
                                                aria-labelledby="basic-example-h-0" class="body current"
                                                aria-hidden="false">
                                                <form>
                                                    <div class="row">
                                                        <div class="col-lg-6">
                                                            <div class="mb-3">
                                                                <label for="basicpill-firstname-input">Tray</label>
                                                                <input type="text" class="form-control"
                                                                    id="basicpill-firstname-input"
                                                                    placeholder="Enter Your First Name">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <div class="mb-3">
                                                                <label for="basicpill-lastname-input">Batangan</label>
                                                                <input type="text" class="form-control"
                                                                    id="basicpill-lastname-input"
                                                                    placeholder="Enter Your Last Name">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </section>

                                            <!-- Company Document -->
                                            <h3 id="basic-example-h-1" tabindex="-1" class="title">Company Document
                                            </h3>
                                            <section id="basic-example-p-1" role="tabpanel"
                                                aria-labelledby="basic-example-h-1" class="body" aria-hidden="true"
                                                style="display: none;">
                                                <form>
                                                    <div class="row">
                                                        <div class="col-lg-6">
                                                            <div class="mb-3">
                                                                <label for="basicpill-pancard-input">PAN Card</label>
                                                                <input type="text" class="form-control"
                                                                    id="basicpill-pancard-input"
                                                                    placeholder="Enter Your PAN No.">
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-6">
                                                            <div class="mb-3">
                                                                <label for="basicpill-vatno-input">VAT/TIN No.</label>
                                                                <input type="text" class="form-control"
                                                                    id="basicpill-vatno-input"
                                                                    placeholder="Enter Your VAT/TIN No.">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-6">
                                                            <div class="mb-3">
                                                                <label for="basicpill-cstno-input">CST No.</label>
                                                                <input type="text" class="form-control"
                                                                    id="basicpill-cstno-input"
                                                                    placeholder="Enter Your CST No.">
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-6">
                                                            <div class="mb-3">
                                                                <label for="basicpill-servicetax-input">Service Tax
                                                                    No.</label>
                                                                <input type="text" class="form-control"
                                                                    id="basicpill-servicetax-input"
                                                                    placeholder="Enter Your Service Tax No.">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-6">
                                                            <div class="mb-3">
                                                                <label for="basicpill-companyuin-input">Company UIN</label>
                                                                <input type="text" class="form-control"
                                                                    id="basicpill-companyuin-input"
                                                                    placeholder="Enter Your Company UIN">
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-6">
                                                            <div class="mb-3">
                                                                <label
                                                                    for="basicpill-declaration-input">Declaration</label>
                                                                <input type="text" class="form-control"
                                                                    id="basicpill-Declaration-input"
                                                                    placeholder="Declaration Details">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </section>
                                        </div>
                                        <div class="actions clearfix">
                                            <ul role="menu" aria-label="Pagination">
                                                <li class="disabled" aria-disabled="true"><a href="#previous"
                                                        role="menuitem">Previous</a></li>
                                                <li aria-hidden="false" aria-disabled="false"><a href="#next"
                                                        role="menuitem">Next</a></li>
                                                <li aria-hidden="true" style="display: none;"><a href="#finish"
                                                        role="menuitem">Finish</a></li>
                                            </ul>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>
@endsection
