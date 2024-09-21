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
            const trgtId = $(this).data('msn-trgt-id');
            const row = $(this).closest('tr');
            const data = $('#datatableDetail').DataTable().row(row).data();
            const jenis = data.mesin.jenis_id;
            const id = data.msn_trgt_id;
            if ($(this).hasClass('btn-process') || $(this).hasClass('btn-detailHari')) {
                window.currentWeekId = trgtId;
                if (jenis.substring(0, 3) == 'HLP') {
                    $('#trgt_id').val(trgtId);
                    $('#hlpModal').modal('show');
                } else if (jenis.substring(0, 2) == 'MK') {
                    $('#trgt_id').val(trgtId);
                    $('#makerModal').modal('show');
                }
            }
        });

        $('#hlpModal .btn-primary').on('click', function() {
            $('#hlpModal').modal('hide');
            $('#form-hlp').submit();
        });
        $('#makerModal .btn-primary').on('click', function() {
            $('#makerModal').modal('hide');
            $('#form-wizard').submit();
        })

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
                },
                autoFocus: true,
                onStepChanging: function(event, currentIndex, newIndex) {
                    return validateForm(currentIndex);
                },
                onFinished: function(event, currentIndex) {
                    if (validateForm(currentIndex)) {
                        alert("Form submitted!");
                        var sisaHasilData = $("#sisaHasilForm").serializeArray();
                        var rejectData = $("#rejectForm").serializeArray();
                        var bahanData = $("#bahanForm").serializeArray();

                        // Combine all data into one object
                        var combinedData = {
                            sisaHasil: sisaHasilData,
                            reject: rejectData,
                            bahan: bahanData,
                            _token: "{{ csrf_token() }}"
                        };

                        console.log(combinedData); // Log the data for testing purposes
                        // Send updated data to server via AJAX
                        $.ajax({
                            url: "{{ route('closing-mesin.store') }}", // Replace with your update route
                            type: "POST",
                            data: combinedData,
                            success: function(response) {
                                Swal.fire({
                                    toast: true,
                                    position: 'bottom-right',
                                    icon: response.success ? 'success' : 'error',
                                    title: response.message,
                                    showConfirmButton: false,
                                    timer: 5000
                                });
                                $('#makerModal').modal('hide');
                            }
                        });
                    }

                    // Implement form submission logic here
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

            function validateForm(currentIndex) {
                // Validate current step before moving to the next
                var isValid = true;

                switch (currentIndex) {
                    case 0:
                        isValid = $("#sisaHasilForm")[0].checkValidity();
                        break;
                    case 1:
                        isValid = $("#rejectForm")[0].checkValidity();
                        break;
                    case 2:
                        isValid = $("#bahanForm")[0].checkValidity();
                        break;
                }

                if (!isValid) {
                    // Highlight invalid fields
                    $("#sisaHasilForm, #rejectForm, #bahanForm").each(function() {
                        $(this).find('input').each(function() {
                            if (!this.validity.valid) {
                                $(this).addClass('is-invalid');
                            } else {
                                $(this).removeClass('is-invalid');
                            }
                        });
                    });
                } else {
                    // Clear invalid classes if valid
                    $(".form-control").removeClass('is-invalid');
                }

                return isValid; // Allow step change if valid
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
                                    <h3>Sisa Hasil</h3>
                                    <section>
                                        <form id="sisaHasilForm">
                                            <div class="row">
                                                <?php
                                                $fields = [
                                                    ["label" => "TRAY", "placeholder" => "Enter TRAY"],
                                                    ["label" => "Batangan", "placeholder" => "Enter Batangan"],
                                                    ["label" => "Batangan Reject", "placeholder" => "Enter Batangan Reject"],
                                                ];

                                                foreach ($fields as $index => $field) {
                                                    echo '<div class="col-lg-4">
                                                            <div class="mb-3">
                                                                <label for="input-' . $index . '">' . $field['label'] . '</label>
                                                                <input type="text" class="form-control" name="bahan[' . $index .']" id="input-' . $index . '" placeholder="' . $field['placeholder'] . '" pattern="\d*" inputmode="numeric" required>
                                                            </div>
                                                          </div>';
                                                }
                                                ?>
                                            </div>
                                        </form>
                                    </section>

                                    <h3>Reject</h3>
                                    <section>
                                        <form id="rejectForm">
                                            <div class="row">
                                                <?php
                                                $fields = [
                                                    ["label" => "Debu", "placeholder" => "Enter Debu"],
                                                    ["label" => "Sapon", "placeholder" => "Enter Sapon"],
                                                    ["label" => "CP Reject", "placeholder" => "Enter CP Reject"],
                                                    ["label" => "Filter Reject", "placeholder" => "Enter Filter Reject"],
                                                    ["label" => "CTP Reject", "placeholder" => "Enter CTP Reject"],
                                                ];

                                                foreach ($fields as $index => $field) {
                                                    echo '<div class="col-lg-4">
                                                            <div class="mb-3">
                                                                <label for="rejectInput-' . $index . '">' . $field['label'] . '</label>
                                                                <input type="text" class="form-control" name="bahan[' . $index .']" id="rejectInput-' . $index . '" placeholder="' . $field['placeholder'] . '" pattern="\d*" inputmode="numeric" required>
                                                            </div>
                                                          </div>';
                                                }
                                                ?>
                                            </div>
                                        </form>
                                    </section>

                                    <h3>Bahan</h3>
                                    <section>
                                        <form id="bahanForm">
                                            <div class="row">
                                                <?php
                                                $fields = [
                                                    ["label" => "TSG", "placeholder" => "Enter TSG"],
                                                    ["label" => "CP", "placeholder" => "Enter CP"],
                                                    ["label" => "Filter", "placeholder" => "Enter Filter"],
                                                    ["label" => "CTP", "placeholder" => "Enter CTP"],
                                                ];

                                                foreach ($fields as $index => $field) {
                                                    echo '<div class="col-lg-4">
                                                            <div class="mb-3">
                                                                <label for="bahanInput-' . $index . '">' . $field['label'] . '</label>
                                                                <input type="text" class="form-control" name="bahan[' . $index .']" id="bahanInput-' . $index . '" placeholder="' . $field['placeholder'] . '" pattern="\d*" inputmode="numeric" required>
                                                            </div>
                                                          </div>';
                                                }
                                                ?>
                                            </div>
                                        </form>
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
                                            <?php
                                                $fields = [
                                                    ["label" => "Karton", "placeholder" => "Enter Karton"],
                                                    ["label" => "Ball", "placeholder" => "Enter Ball"],
                                                    ["label" => "Slop", "placeholder" => "Enter Slop"],
                                                    ["label" => "Pack OPP", "placeholder" => "Enter Pack OPP"],
                                                    ["label" => "NPC", "placeholder" => "Enter NPC"],
                                                    ["label" => "Pack Reject", "placeholder" => "Enter Pack Reject"],
                                                ];

                                                echo '<div class="row">';
                                                foreach ($fields as $index => $field) {
                                                    // Start a new row after every 3 fields
                                                    if ($index % 3 === 0 && $index !== 0) {
                                                        echo '</div><div class="row">';
                                                    }

                                                    echo '<div class="col-lg-4">
                                                            <div class="mb-3">
                                                                <label for="input-' . $index . '">' . $field['label'] . '</label>
                                                                <input type="text" class="form-control" id="input-' . $index . '" placeholder="' . $field['placeholder'] . '">
                                                            </div>
                                                          </div>';
                                                }
                                                echo '</div>';
                                                ?>

                                        </form>
                                    </section>

                                    <!-- Company Document -->
                                    <h3>Reject</h3>
                                    <section>
                                        <form>
                                            <?php
                                                $fields = [
                                                    ["label" => "Foil", "placeholder" => "Enter Foil"],
                                                    ["label" => "Inner", "placeholder" => "Enter Inner"],
                                                    ["label" => "Etiket", "placeholder" => "Enter Etiket"],
                                                    ["label" => "Pita Cukai", "placeholder" => "Enter Pita Cukai"],
                                                    ["label" => "OPP Pack", "placeholder" => "Enter OPP Pack"],
                                                    ["label" => "Teartape", "placeholder" => "Enter Teartape"],
                                                    ["label" => "OPP Slop", "placeholder" => "Enter OPP Slop"],
                                                    ["label" => "Segel Slop", "placeholder" => "Enter Segel Slop"],
                                                    ["label" => "Kertas Ball", "placeholder" => "Enter Kertas Ball"],
                                                    ["label" => "Segel Ball", "placeholder" => "Enter Segel Ball"],
                                                    ["label" => "Karton", "placeholder" => "Enter Karton"],
                                                ];

                                                echo '<div class="row">';
                                                foreach ($fields as $index => $field) {
                                                    // Start a new row after every 3 fields
                                                    if ($index % 3 === 0 && $index !== 0) {
                                                        echo '</div><div class="row">';
                                                    }

                                                    echo '<div class="col-lg-4">
                                                            <div class="mb-3">
                                                                <label for="input-' . $index . '">' . $field['label'] . ' Reject</label>
                                                                <input type="text" class="form-control" id="input-' . $index . '" placeholder="' . $field['placeholder'] . '">
                                                            </div>
                                                          </div>';
                                                }
                                                echo '</div>';
                                                ?>
                                        </form>
                                    </section>

                                    <!-- Bank Details -->
                                    <h3>Bahan</h3>
                                    <section>
                                        <div>
                                            <form>
                                                <?php
                                                $fields = [
                                                    ["label" => "Foil", "placeholder" => "Enter Foil"],
                                                    ["label" => "Inner", "placeholder" => "Enter Inner"],
                                                    ["label" => "Etiket", "placeholder" => "Enter Etiket"],
                                                    ["label" => "Pita Cukai", "placeholder" => "Enter Pita Cukai"],
                                                    ["label" => "OPP Pack", "placeholder" => "Enter OPP Pack"],
                                                    ["label" => "Teartape", "placeholder" => "Enter Teartape"],
                                                    ["label" => "OPP Slop", "placeholder" => "Enter OPP Slop"],
                                                    ["label" => "Segel Slop", "placeholder" => "Enter Segel Slop"],
                                                    ["label" => "Kertas Ball", "placeholder" => "Enter Kertas Ball"],
                                                    ["label" => "Segel Ball", "placeholder" => "Enter Segel Ball"],
                                                    ["label" => "Karton", "placeholder" => "Enter Karton"],
                                                    ["label" => "Batangan", "placeholder" => "Enter Batangan"],
                                                ];

                                                echo '<div class="row">';
                                                foreach ($fields as $index => $field) {
                                                    // Start a new row after every 3 fields
                                                    if ($index % 3 === 0 && $index !== 0) {
                                                        echo '</div><div class="row">';
                                                    }

                                                    echo '<div class="col-lg-4">
                                                            <div class="mb-3">
                                                                <label for="input-' . $index . '">' . $field['label'] . '</label>
                                                                <input type="text" class="form-control" id="input-' . $index . '" placeholder="' . $field['placeholder'] . '">
                                                            </div>
                                                          </div>';
                                                }
                                                echo '</div>';
                                                ?>

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