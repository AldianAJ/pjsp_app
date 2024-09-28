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
    document.addEventListener('DOMContentLoaded', (event) => {
        // Function to format input value
        function formatInput(event) {
            let input = event.target;
            let value = input.value;

            // Replace comma with dot
            value = value.replace(/\,/g, '.');

            // Allow only digits and dot
            value = value.replace(/[^0-9.]/g, '');

            // Update the input value
            input.value = value;
        }

        // Add event listeners to all inputs with the class 'form-control'
        document.querySelectorAll('.number').forEach(input => {
            // Initialize input with default value if empty
            if (input.value === '') {
                input.value = '0';
            }

            // Add input event listener
            input.addEventListener('input', formatInput);

            // Add focus event listener to set default value if empty on focus
            input.addEventListener('focus', () => {
                if (input.value === '0') {
                    input.value = '';
                }
            });

            // Add blur event listener to set default value if empty on blur
            input.addEventListener('blur', () => {
                if (input.value === '') {
                    input.value = '0';
                }
            });
        });
    });
</script>

<script>
    $('#datatableDetail').DataTable({
            ajax: {
                url: "{{ route('closing-mesin') }}",
                type: "GET",
            },
            processing: true,
            serverSide: true,
            lengthMenu: [5],
            columns: [{
                    data: "nm_brg"
                },
                {
                    data: "shift"
                },
                {
                    data: "nama"
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
            const nm_brg = data.nm_brg;
            const nm_mesin = data.nama;
            const shift = data.shift;
            const jenis = data.jenis_id;
            const brg_id = data.brg_id;
            if ($(this).hasClass('btn-process') || $(this).hasClass('btn-detailHari')) {
                window.currentWeekId = trgtId;
                if (jenis.substring(0, 3) == 'HLP') {
                    $('#modalTitleHLP').html(`Closing HLP (Shift ${shift}) (${nm_mesin}) (${nm_brg})`);
                    $('#trgt_id_HLP').val(trgtId);
                    $('#produk_HLP').val(brg_id);
                    // resetJQuerySteps('#form-hlp',3);
                    $('#hlpModal').modal('show');
                } else if (jenis.substring(0, 2) == 'MK') {
                    $('#modalTitleMaker').html(`Closing MAKER (Shift ${shift}) (${nm_mesin}) (${nm_brg})`);
                    $('#trgt_id').val(trgtId);
                    $('#produk').val(brg_id);
                    // resetJQuerySteps('#form-maker',3);
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
            $('#form-maker').submit();
        })

        function resetJQuerySteps(elementTarget, noOfSteps){
            var noOfSteps = noOfSteps - 1;

            var currentIndex = $(elementTarget).steps("getCurrentIndex");
                if(currentIndex >= 1){
                    for(var x = 0; x < currentIndex;x++){
                        $(elementTarget).steps("previous");
                    }
                }

            setTimeout(function resetHeaderCall(){
            var y, steps;
                for(y = 0, steps= 2; y < noOfSteps;y++){
                    //console.log(steps);
                    try{
                        $(`${elementTarget} > .steps > ul > li:nth-child(${steps})`).removeClass("done");
                            $(`${elementTarget} > .steps > ul > li:nth-child(${steps})`).removeClass("current");
                            $(`${elementTarget} > .steps > ul > li:nth-child(${steps})`).addClass("disabled");

                    }
                    catch(err){}
            steps++;
                }
            }, 50);
        }

        // form maker init
        $(function() {
            $("#form-maker").steps({
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
                    if (currentIndex > newIndex) {
                        return true; // Allow back navigation
                    }
                    switch (currentIndex) {
                        case 0:
                            FormId = "#sisaHasilForm";
                            break;
                        case 1:
                            FormId = "#rejectForm";
                            break;
                        case 2:
                            FormId = "#bahanForm";
                            break;
                    }
                    return validateForm(FormId);
                },
                onFinished: function(event, currentIndex) {
                    if (validateForm("#bahanForm")) {
                        var sisaHasilData = $("#sisaHasilForm").serializeArray();
                        var rejectData = $("#rejectForm").serializeArray();
                        var bahanData = $("#bahanForm").serializeArray();
                        var trgtId = $('#trgt_id').val();
                        var produk = $('#produk').val();

                        // Combine all data into one object
                        var combinedData = {
                            trgt_id: trgtId,
                            produk: produk,
                            sisaHasil: sisaHasilData,
                            reject: rejectData,
                            bahan: bahanData,
                            _token: "{{ csrf_token() }}"
                        };

                        // Send updated data to server via AJAX
                        $.ajax({
                            url: "{{ route('closing-mesin.store') }}", // Replace with your update route
                            type: "POST",
                            data: combinedData,
                            success: function(response) {
                                Swal.fire({
                                    toast: true,
                                    position: 'top-right',
                                    icon: response.success ? 'success' : 'error',
                                    title: response.message,
                                    showConfirmButton: false,
                                    timer: 5000
                                });
                                $('#makerModal').modal('hide');
                                $("#sisaHasilForm")[0].reset();
                                $("#rejectForm")[0].reset();
                                $("#bahanForm")[0].reset();
                                $('#trgt_id').val('');
                                $('#produk').val('');
                                $('#datatableDetail').DataTable().ajax.reload();
                                resetJQuerySteps('#form-maker',3);
                            }
                        });
                    }
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
                },
                autoFocus: true,
                onStepChanging: function(event, currentIndex, newIndex) {
                    if (currentIndex > newIndex) {
                        return true; // Allow back navigation
                    }
                    switch (currentIndex) {
                        case 0:
                            FormId = "#sisaHasilHLPForm";
                            break;
                        case 1:
                            FormId = "#rejectHLPForm";
                            break;
                        case 2:
                            FormId = "#bahanHLPForm";
                            break;
                    }
                    return validateForm(FormId);
                },
                onFinished: function(event, currentIndex) {
                    if (validateForm("#bahanHLPForm")) {
                        var sisaHasilData = $("#sisaHasilHLPForm").serializeArray();
                        var rejectData = $("#rejectHLPForm").serializeArray();
                        var bahanData = $("#bahanHLPForm").serializeArray();
                        var trgtId = $('#trgt_id_HLP').val();
                        var produk = $('#produk_HLP').val();

                        // Combine all data into one object
                        var combinedData = {
                            trgt_id: trgtId,
                            produk: produk,
                            sisaHasil: sisaHasilData,
                            reject: rejectData,
                            bahan: bahanData,
                            _token: "{{ csrf_token() }}"
                        };

                        // Send updated data to server via AJAX
                        $.ajax({
                            url: "{{ route('closing-mesin.storeHlp') }}", // Replace with your update route
                            type: "POST",
                            data: combinedData,
                            success: function(response) {
                                Swal.fire({
                                    toast: true,
                                    position: 'top-right',
                                    icon: response.success ? 'success' : 'error',
                                    title: response.message,
                                    showConfirmButton: false,
                                    timer: 5000
                                });
                                $('#hlpModal').modal('hide');
                                $("#sisaHasiHLPlForm")[0].reset();
                                $("#rejectHLPForm")[0].reset();
                                $("#bahanHLPForm")[0].reset();
                                $('#trgt_id').val('');
                                $('#produk').val('');
                                $('#datatableDetail').DataTable().ajax.reload();
                                resetJQuerySteps('#form-hlp',3);
                            }
                        });
                    }
                }

            })

            function validateForm(FormId) {
                // Validate current step before moving to the next
                var isValid = true;

                isValid = $(FormId)[0].checkValidity();

                if (!isValid) {
                    // Highlight invalid fields
                    $(FormId).each(function() {
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

        $('#tgl-input').on('change', function() {
            var tgl = $(this).val();
            $('#datatableDetail').DataTable().ajax.url("{{ route('closing-mesin') }}?tgl=" + tgl).load();
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
                <div class="d-flex flex-column">
                    <div class="row w-75">
                        <div class="col-sm-4 d-flex me-3">
                            <div class="mb-3 flex-grow-1">
                                <label for="filterTahun">Tanggal:</label>
                                <input type="date" class="form-control" name="tgl" id="tgl-input"
                                    value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="col-sm-4 d-flex me-3">
                            <div class="mb-3 flex-grow-1">
                                <label for="filterTahun">Mesin:</label>
                                <input type="text" class="form-control" name="l" id="t-input"
                                    value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                            </div>
                        </div>
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
                <h5 id="modalTitleMaker" class="modal-title">Closing MAKER</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div id="form-maker">
                                    <h3>Sisa Hasil Produksi</h3>
                                    <section>
                                        <input type="hidden" id="trgt_id" name="trgt_id">
                                        <input type="hidden" id="produk" name="produk">
                                        <form id="sisaHasilForm">
                                            <div class="row">
                                                <?php
                                                $fields = [
                                                    ["label" => "TRAY", "placeholder" => "Enter TRAY", "name" => "TRAY"],
                                                    ["label" => "Batangan", "placeholder" => "Enter Batangan", "name" => "BTG"],
                                                    ["label" => "Batangan Reject", "placeholder" => "Enter Batangan Reject", "name" => "btg_reject"],
                                                ];

                                                foreach ($fields as $index => $field) {
                                                    echo '<div class="col-lg-4">
                                                            <div class="mb-3">
                                                                <label for="input-' . $index . '">' . $field['label'] . '</label>
                                                                <input type="text" class="form-control number" value="0" name="' . $field['name'] .'" id="input-' . $index . '" placeholder="' . $field['placeholder'] . '" pattern="\d*" inputmode="numeric" required>
                                                            </div>
                                                          </div>';
                                                }
                                                ?>
                                            </div>
                                        </form>
                                    </section>

                                    <h3>Reject Bahan</h3>
                                    <section>
                                        <form id="rejectForm">
                                            <div class="row">
                                                <?php
                                                $fields = [
                                                    ["label" => "Debu", "placeholder" => "Enter Debu", "name" => "debu"],
                                                    ["label" => "Sapon", "placeholder" => "Enter Sapon", "name" => "sapon"],
                                                    ["label" => "CP Reject", "placeholder" => "Enter CP Reject", "name" => "cp"],
                                                    ["label" => "Filter Reject", "placeholder" => "Enter Filter Reject", "name" => "filter"],
                                                    ["label" => "CTP Reject", "placeholder" => "Enter CTP Reject", "name" => "ctp"],
                                                ];

                                                foreach ($fields as $index => $field) {
                                                    echo '<div class="col-lg-4">
                                                            <div class="mb-3">
                                                                <label for="rejectInput-' . $index . '">' . $field['label'] . '</label>
                                                                <input type="text" class="form-control number" value="" name="' . $field['name'] .'" id="rejectInput-' . $index . '" placeholder="' . $field['placeholder'] . '" pattern="\d*" inputmode="numeric" required>
                                                            </div>
                                                          </div>';
                                                }
                                                ?>
                                            </div>
                                        </form>
                                    </section>

                                    <h3>Sisa Bahan</h3>
                                    <section>
                                        <form id="bahanForm">
                                            <div class="row">
                                                <?php
                                                $fields = [
                                                    ["label" => "TSG", "placeholder" => "Enter TSG", "name" => "tsg"],
                                                    ["label" => "CP", "placeholder" => "Enter CP", "name" => "cp"],
                                                    ["label" => "Filter", "placeholder" => "Enter Filter", "name" => "filter"],
                                                    ["label" => "CTP", "placeholder" => "Enter CTP", "name" => "ctp"],
                                                ];

                                                foreach ($fields as $index => $field) {
                                                    echo '<div class="col-lg-4">
                                                            <div class="mb-3">
                                                                <label for="bahanInput-' . $index . '">' . $field['label'] . '</label>
                                                                <input type="text" class="form-control number" value="" name="' . $field['name'] .'" id="bahanInput-' . $index . '" placeholder="' . $field['placeholder'] . '" pattern="\d*" inputmode="numeric" required>
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
                <h5 id="modalTitleHLP" class="modal-title">Closing HLP</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div id="form-hlp">
                                    <h3>Sisa Hasil Produksi</h3>
                                    <section>
                                        <input type="hidden" id="trgt_id_HLP" name="trgt_id">
                                        <input type="hidden" id="produk_HLP" name="produk">
                                        <form id="sisaHasilHLPForm">
                                            <?php
                                                $fields = [
                                                    ["label" => "Karton", "placeholder" => "Enter Karton", "name" => "karton"],
                                                    ["label" => "Ball", "placeholder" => "Enter Ball", "name" => "ball"],
                                                    ["label" => "Slop", "placeholder" => "Enter Slop", "name" => "slop"],
                                                    ["label" => "Pack OPP", "placeholder" => "Enter Pack OPP", "name" => "opp_pack"],
                                                    ["label" => "NPC", "placeholder" => "Enter NPC", "name" => "npc"],
                                                    ["label" => "Pack Reject", "placeholder" => "Enter Pack Reject", "name" => "pack_reject"],
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
                                                                <input type="text" class="form-control number" value="" name="' . $field['name'] .'" id="input-' . $index . '" placeholder="' . $field['placeholder'] . '" pattern="\d*" inputmode="numeric" required>
                                                            </div>
                                                          </div>';
                                                }
                                                echo '</div>';
                                                ?>

                                        </form>
                                    </section>

                                    <h3>Reject Bahan</h3>
                                    <section>
                                        <form id="rejectHLPForm">
                                            <?php
                                                $fields = [
                                                    ["label" => "Foil", "placeholder" => "Enter Foil", "name" => "foil"],
                                                    ["label" => "Inner", "placeholder" => "Enter Inner", "name" => "inner"],
                                                    ["label" => "Etiket", "placeholder" => "Enter Etiket", "name" => "etiket"],
                                                    ["label" => "Pita Cukai", "placeholder" => "Enter Pita Cukai", "name" => "pita_cukai"],
                                                    ["label" => "OPP Pack", "placeholder" => "Enter OPP Pack", "name" => "opp_pack"],
                                                    ["label" => "Teartape", "placeholder" => "Enter Teartape", "name" => "teartape"],
                                                    ["label" => "OPP Slop", "placeholder" => "Enter OPP Slop", "name" => "opp_slop"],
                                                    ["label" => "Segel Slop", "placeholder" => "Enter Segel Slop", "name" => "segel_slop"],
                                                    ["label" => "Kertas Ball", "placeholder" => "Enter Kertas Ball", "name" => "kertas_ball"],
                                                    ["label" => "Segel Ball", "placeholder" => "Enter Segel Ball", "name" => "segel_ball"],
                                                    ["label" => "Karton", "placeholder" => "Enter Karton", "name" => "karton"],
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
                                                                <input type="text" class="form-control number" value="" name="' . $field['name'] .'" id="input-' . $index . '" placeholder="' . $field['placeholder'] . '" pattern="\d*" inputmode="numeric" required>
                                                            </div>
                                                          </div>';
                                                }
                                                echo '</div>';
                                                ?>
                                        </form>
                                    </section>

                                    <h3>SisaBahan</h3>
                                    <section>
                                        <div>
                                            <form id="bahanHLPForm">
                                                <?php
                                                $fields = [
                                                    ["label" => "Foil", "placeholder" => "Enter Foil", "name" => "foil"],
                                                    ["label" => "Inner", "placeholder" => "Enter Inner", "name" => "inner"],
                                                    ["label" => "Etiket", "placeholder" => "Enter Etiket", "name" => "etiket"],
                                                    ["label" => "Pita Cukai", "placeholder" => "Enter Pita Cukai", "name" => "pita_cukai"],
                                                    ["label" => "OPP Pack", "placeholder" => "Enter OPP Pack", "name" => "opp_pack"],
                                                    ["label" => "Teartape", "placeholder" => "Enter Teartape", "name" => "teartape"],
                                                    ["label" => "OPP Slop", "placeholder" => "Enter OPP Slop", "name" => "opp_slop"],
                                                    ["label" => "Segel Slop", "placeholder" => "Enter Segel Slop", "name" => "segel_slop"],
                                                    ["label" => "Kertas Ball", "placeholder" => "Enter Kertas Ball", "name" => "kertas_ball"],
                                                    ["label" => "Segel Ball", "placeholder" => "Enter Segel Ball", "name" => "segel_ball"],
                                                    ["label" => "Karton", "placeholder" => "Enter Karton", "name" => "karton"],
                                                    ["label" => "Batangan", "placeholder" => "Enter Batangan", "name" => "batangan"],
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
                                                                <input type="text" class="form-control number" value="" name="' . $field['name'] .'" id="input-' . $index . '" placeholder="' . $field['placeholder'] . '" pattern="\d*" inputmode="numeric" required>
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