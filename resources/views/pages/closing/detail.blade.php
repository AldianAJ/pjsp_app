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
    <!-- jquery step -->
    <script src="{{ asset('assets/libs/jquery-steps/build/jquery.steps.min.js') }}"></script>

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
            const jenis = data.mesin.jenis_id;
            if ($(this).hasClass('btn-process') || $(this).hasClass('btn-detailHari')) {
                $('#week_id').val(weekId);
                window.currentWeekId = weekId;
                if (jenis.substring(0, 3) == 'HLP') {
                    $('#makerModal').modal('show');
                } else if (jenis.substring(0, 2) == 'MK') {
                    $('#hlpModal').modal('show');
                }
            }
        });

        var settings = {
            labels: {
                current: "current step:",
                pagination: "Pagination",
                finish: "Finish",
                next: "Selanjutnya",
                previous: "Sebelumnya",
                loading: "Loading ..."
            }
        };
        // form wizard init
        $(function() {
            $("#form-wizard").steps({
                headerTag: "h3",
                bodyTag: "section",
                transitionEffect: "slide",
                labels: {
                    finish: "Finish",
                    next: "Selanjutnya",
                    previous: "Sebelumnya",
                }
            })
            $("#form-hlp").steps({
                headerTag: "h3",
                bodyTag: "section",
                transitionEffect: "slide",
                labels: {
                    finish: "Finish",
                    next: "Selanjutnya",
                    previous: "Sebelumnya",
                }
            })
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

    <!-- Modal Maker-->
    <div class="modal fade" id="makerModal" tabindex="-1" aria-hidden="true">
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
                                    <h4 class="card-title mb-4">Closing MAKER</h4>

                                    <div id="form-wizard">
                                        <!-- Seller Details -->
                                        <h3>Sisa Hasil</h3>
                                        <section>
                                            <form>
                                                <div class="row">
                                                    <div class="col-lg-4">
                                                        <div class="mb-3">
                                                            <label for="basicpill-firstname-input">TRAY</label>
                                                            <input type="text" class="form-control"
                                                                id="basicpill-firstname-input"
                                                                placeholder="Enter Your First Name">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <div class="mb-3">
                                                            <label for="basicpill-lastname-input">Batangan</label>
                                                            <input type="text" class="form-control"
                                                                id="basicpill-lastname-input"
                                                                placeholder="Enter Your Last Name">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <div class="mb-3">
                                                            <label for="basicpill-lastname-input">Batangan Reject</label>
                                                            <input type="text" class="form-control"
                                                                id="basicpill-lastname-input"
                                                                placeholder="Enter Your Last Name">
                                                        </div>
                                                    </div>
                                                </div>

                                            </form>
                                        </section>

                                        <!-- Company Document -->
                                        <h3>Reject</h3>
                                        <section>
                                            <form>
                                                <div class="row">
                                                    <div class="col-lg-6">
                                                        <div class="mb-3">
                                                            <label for="basicpill-pancard-input">Debu</label>
                                                            <input type="text" class="form-control"
                                                                id="basicpill-pancard-input"
                                                                placeholder="Enter Your PAN No.">
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6">
                                                        <div class="mb-3">
                                                            <label for="basicpill-vatno-input">Sapon</label>
                                                            <input type="text" class="form-control"
                                                                id="basicpill-vatno-input"
                                                                placeholder="Enter Your VAT/TIN No.">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-6">
                                                        <div class="mb-3">
                                                            <label for="basicpill-cstno-input">CP Reject</label>
                                                            <input type="text" class="form-control"
                                                                id="basicpill-cstno-input" placeholder="Enter Your CST No.">
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6">
                                                        <div class="mb-3">
                                                            <label for="basicpill-servicetax-input">Filter Reject</label>
                                                            <input type="text" class="form-control"
                                                                id="basicpill-servicetax-input"
                                                                placeholder="Enter Your Service Tax No.">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-6">
                                                        <div class="mb-3">
                                                            <label for="basicpill-companyuin-input">CTP Reject</label>
                                                            <input type="text" class="form-control"
                                                                id="basicpill-companyuin-input"
                                                                placeholder="Enter Your Company UIN">
                                                        </div>
                                                    </div>

                                                </div>
                                            </form>
                                        </section>

                                        <!-- Bank Details -->
                                        <h3>Bahan</h3>
                                        <section>
                                            <div>
                                                <form>
                                                    <div class="row">
                                                        <div class="col-lg-6">
                                                            <div class="mb-3">
                                                                <label for="basicpill-namecard-input">TSG</label>
                                                                <input type="text" class="form-control"
                                                                    id="basicpill-namecard-input"
                                                                    placeholder="Enter Your Name on Card">
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-6">
                                                            <div class="mb-3">
                                                                <label for="basicpill-namecard-input">CP</label>
                                                                <input type="text" class="form-control"
                                                                    id="basicpill-namecard-input"
                                                                    placeholder="Enter Your Name on Card">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-6">
                                                            <div class="mb-3">
                                                                <label for="basicpill-cardno-input">Filter</label>
                                                                <input type="text" class="form-control"
                                                                    id="basicpill-cardno-input"
                                                                    placeholder="Credit Card Number">
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-6">
                                                            <div class="mb-3">
                                                                <label for="basicpill-card-verification-input">CTP</label>
                                                                <input type="text" class="form-control"
                                                                    id="basicpill-card-verification-input"
                                                                    placeholder="Credit Verification Number">
                                                            </div>
                                                        </div>
                                                    </div>

                                                </form>
                                            </div>
                                        </section>

                                    </div>

                                </div>
                                <!-- end card body -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>
    <!-- Modal HLP-->
    <div class="modal fade" id="hlpModal" tabindex="-1" aria-hidden="true">
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
                                    <h4 class="card-title mb-4">Closing HLP</h4>

                                    <div id="form-hlp">
                                        <!-- Seller Details -->
                                        <h3>Sisa Hasil</h3>
                                        <section>
                                            <form>
                                                <div class="row">
                                                    <div class="col-lg-4">
                                                        <div class="mb-3">
                                                            <label for="basicpill-firstname-input">Karton</label>
                                                            <input type="text" class="form-control"
                                                                id="basicpill-firstname-input"
                                                                placeholder="Enter Your First Name">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <div class="mb-3">
                                                            <label for="basicpill-lastname-input">Ball</label>
                                                            <input type="text" class="form-control"
                                                                id="basicpill-lastname-input"
                                                                placeholder="Enter Your Last Name">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <div class="mb-3">
                                                            <label for="basicpill-lastname-input">Slop</label>
                                                            <input type="text" class="form-control"
                                                                id="basicpill-lastname-input"
                                                                placeholder="Enter Your Last Name">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-lg-4">
                                                        <div class="mb-3">
                                                            <label for="basicpill-firstname-input">Pack OPP</label>
                                                            <input type="text" class="form-control"
                                                                id="basicpill-firstname-input"
                                                                placeholder="Enter Your First Name">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <div class="mb-3">
                                                            <label for="basicpill-lastname-input">NPC</label>
                                                            <input type="text" class="form-control"
                                                                id="basicpill-lastname-input"
                                                                placeholder="Enter Your Last Name">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <div class="mb-3">
                                                            <label for="basicpill-lastname-input">Pack Reject</label>
                                                            <input type="text" class="form-control"
                                                                id="basicpill-lastname-input"
                                                                placeholder="Enter Your Last Name">
                                                        </div>
                                                    </div>
                                                </div>

                                            </form>
                                        </section>

                                        <!-- Company Document -->
                                        <h3>Reject</h3>
                                        <section>
                                            <form>
                                                <div class="row">
                                                    <div class="col-lg-4">
                                                        <div class="mb-3">
                                                            <label for="basicpill-firstname-input">Foil</label>
                                                            <input type="text" class="form-control"
                                                                id="basicpill-firstname-input"
                                                                placeholder="Enter Your First Name">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <div class="mb-3">
                                                            <label for="basicpill-lastname-input">Inner</label>
                                                            <input type="text" class="form-control"
                                                                id="basicpill-lastname-input"
                                                                placeholder="Enter Your Last Name">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <div class="mb-3">
                                                            <label for="basicpill-lastname-input">Etiket</label>
                                                            <input type="text" class="form-control"
                                                                id="basicpill-lastname-input"
                                                                placeholder="Enter Your Last Name">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-4">
                                                        <div class="mb-3">
                                                            <label for="basicpill-firstname-input">Pita Cukai</label>
                                                            <input type="text" class="form-control"
                                                                id="basicpill-firstname-input"
                                                                placeholder="Enter Your First Name">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <div class="mb-3">
                                                            <label for="basicpill-lastname-input">OPP Pack</label>
                                                            <input type="text" class="form-control"
                                                                id="basicpill-lastname-input"
                                                                placeholder="Enter Your Last Name">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <div class="mb-3">
                                                            <label for="basicpill-lastname-input">Teartape</label>
                                                            <input type="text" class="form-control"
                                                                id="basicpill-lastname-input"
                                                                placeholder="Enter Your Last Name">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-4">
                                                        <div class="mb-3">
                                                            <label for="basicpill-firstname-input">OPP Slop</label>
                                                            <input type="text" class="form-control"
                                                                id="basicpill-firstname-input"
                                                                placeholder="Enter Your First Name">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <div class="mb-3">
                                                            <label for="basicpill-lastname-input">Segel Slop</label>
                                                            <input type="text" class="form-control"
                                                                id="basicpill-lastname-input"
                                                                placeholder="Enter Your Last Name">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <div class="mb-3">
                                                            <label for="basicpill-lastname-input">Pack Reject</label>
                                                            <input type="text" class="form-control"
                                                                id="basicpill-lastname-input"
                                                                placeholder="Enter Your Last Name">
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </section>

                                        <!-- Bank Details -->
                                        <h3>Bahan</h3>
                                        <section>
                                            <div>
                                                <form>
                                                    <div class="row">
                                                        <div class="col-lg-6">
                                                            <div class="mb-3">
                                                                <label for="basicpill-namecard-input">TSG</label>
                                                                <input type="text" class="form-control"
                                                                    id="basicpill-namecard-input"
                                                                    placeholder="Enter Your Name on Card">
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-6">
                                                            <div class="mb-3">
                                                                <label for="basicpill-namecard-input">CP</label>
                                                                <input type="text" class="form-control"
                                                                    id="basicpill-namecard-input"
                                                                    placeholder="Enter Your Name on Card">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-6">
                                                            <div class="mb-3">
                                                                <label for="basicpill-cardno-input">Filter</label>
                                                                <input type="text" class="form-control"
                                                                    id="basicpill-cardno-input"
                                                                    placeholder="Credit Card Number">
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-6">
                                                            <div class="mb-3">
                                                                <label for="basicpill-card-verification-input">CTP</label>
                                                                <input type="text" class="form-control"
                                                                    id="basicpill-card-verification-input"
                                                                    placeholder="Credit Verification Number">
                                                            </div>
                                                        </div>
                                                    </div>

                                                </form>
                                            </div>
                                        </section>

                                    </div>

                                </div>
                                <!-- end card body -->
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
