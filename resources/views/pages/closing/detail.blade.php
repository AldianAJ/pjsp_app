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
                data: function(d) {
                    d.tgl = $('#tgl-input').val()
                    d.msn = $('#msn-input').val()
                }
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

        $('#datatableDetail').on('click', '.btn-process', function() {
            const trgtId = $(this).data('msn-trgt-id');
            const row = $(this).closest('tr');
            const data = $('#datatableDetail').DataTable().row(row).data();
            const nm_brg = data.nm_brg;
            const nm_mesin = data.nama;
            const shift = data.shift;
            const jenis = data.jenis_id;
            const brg_id = data.brg_id;
            if ($(this).hasClass('btn-process')) {
                window.currentWeekId = trgtId;
                if (jenis.substring(0, 3) == 'HLP') {
                    $('#modalTitleHLP').html(`Closing HLP (Shift ${shift}) (${nm_mesin}) (${nm_brg})`);
                    $('#trgt_id_HLP').val(trgtId);
                    $('#produk_HLP').val(brg_id);
                    $('#hlpModal').modal('show');
                } else if (jenis.substring(0, 2) == 'MK') {
                    $('#modalTitleMaker').html(`Closing MAKER (Shift ${shift}) (${nm_mesin}) (${nm_brg})`);
                    $('#trgt_id').val(trgtId);
                    $('#produk').val(brg_id);
                    $('#makerModal').modal('show');
                }
            }
        });

        $('#datatableDetail').on('click', '.btn-detail', function() {
            const trgtId = $(this).data('msn-trgt-id');
            const row = $(this).closest('tr');
            const data = $('#datatableDetail').DataTable().row(row).data();
            const nm_brg = data.nm_brg;
            const nm_mesin = data.nama;
            const shift = data.shift;
            const jenis = data.jenis_id;
            const brg_id = data.brg_id;
            if ($(this).hasClass('btn-detail')) {
                window.currentWeekId = trgtId;
                if (jenis.substring(0, 3) == 'HLP') {
                    $('#modalTitleDetailHLP').html(`Detail HLP (Shift ${shift}) (${nm_mesin}) (${nm_brg})`);
                    $('#trgt_id_HLP').val(trgtId);
                    $('#produk_HLP').val(brg_id);
                    $.ajax({
                        url: "{{ route('closing-mesin.detailHlp') }}/", // Replace with your update route
                        type: "get",
                        data: {
                            closing_id: trgtId,
                            jenis: 2,
                        },
                        success: function(data) {
                            $(".detail-hlp").html(data);
                            $('#detailHLPModal').modal('show');
                            $("#form-detail-hlp").steps({
                                headerTag: "h3",
                                bodyTag: "section",
                                transitionEffect: "slide",
                                labels: {
                                    finish: "Tidak Aktif",
                                    next: "Selanjutnya",
                                    previous: "Sebelumnya",
                                },
                                autoFocus: true,
                                enableFinishButton: false,
                                onStepChanged: function(event, currentIndex) {
                                    // Format numbers on step change if needed
                                    formatNumbers(); // Panggil fungsi untuk format angka
                                },
                            });
                            resetJQuerySteps('#form-detail-hlp',3);
                        },
                        error: function(xhr, status, error) {
                            console.error("AJAX Error: ", status, error);
                        }
                    });
                } else if (jenis.substring(0, 2) == 'MK') {
                    $('#modalTitleDetailMaker').html(`Detail MAKER (Shift ${shift}) (${nm_mesin}) (${nm_brg})`);
                    $('#trgt_id').val(trgtId);
                    $('#produk').val(brg_id);
                    $.ajax({
                        url: "{{ route('closing-mesin.detail') }}/", // Replace with your update route
                        type: "get",
                        data: {
                            closing_id: trgtId,
                            jenis: 1,
                        },
                        success: function(data) {
                            $(".detail-maker").html(data);
                            $('#detailMakerModal').modal('show');
                            $("#form-detail-maker").steps({
                                headerTag: "h3",
                                bodyTag: "section",
                                transitionEffect: "slide",
                                labels: {
                                    finish: "Tidak Aktif",
                                    next: "Selanjutnya",
                                    previous: "Sebelumnya",
                                },
                                autoFocus: true,
                                enableFinishButton: false,
                                onStepChanged: function(event, currentIndex) {
                                    $('.number').each(function() {
                                        let value = $(this).val().replace(/[^0-9]/g, '');
                                        $(this).val(formatNumber(value));
                                    });
                                },
                            });
                            resetJQuerySteps('#form-detail-maker',3);
                        },
                        error: function(xhr, status, error) {
                            console.error("AJAX Error: ", status, error);
                        }
                    });

                }
            }
        });

        $('#datatableDetail').on('click', '.btn-edit', function() {
            const trgtId = $(this).data('msn-trgt-id');
            const row = $(this).closest('tr');
            const data = $('#datatableDetail').DataTable().row(row).data();
            const nm_brg = data.nm_brg;
            const nm_mesin = data.nama;
            const shift = data.shift;
            const jenis = data.jenis_id;
            const brg_id = data.brg_id;
            if ($(this).hasClass('btn-edit')) {
                window.currentWeekId = trgtId;
                if (jenis.substring(0, 3) == 'HLP') {
                    $('#modalTitleEditHLP').html(`Edit HLP (Shift ${shift}) (${nm_mesin}) (${nm_brg})`);
                    $('#trgt_id').val(trgtId);
                    $('#produk').val(brg_id);
                    $.ajax({
                        url: "{{ route('closing-mesin.editHlp') }}/", // Replace with your update route
                        type: "get",
                        data: {
                            closing_id: trgtId,
                            jenis: 2,
                        },
                        success: function(data) {
                            $(".edit-hlp").html(data);
                            $('#editHLPModal').modal('show');
                            $("#form-edit-hlp").steps({
                                headerTag: "h3",
                                bodyTag: "section",
                                transitionEffect: "slide",
                                labels: {
                                    finish: "Perbarui",
                                    next: "Selanjutnya",
                                    previous: "Sebelumnya",
                                },
                                autoFocus: true,
                                enableFinishButton: true,
                                onStepChanged: function(event, currentIndex) {
                                    // Format numbers on step change if needed
                                    formatNumbers(); // Panggil fungsi untuk format angka
                                },
                                onFinished: function(event, currentIndex) {
                                    // if (validateForm("#bahanDetailForm")) {
                                        var sisaHasilData = $("#sisaHasilEditHLPForm").serializeArray();
                                        var rejectData = $("#rejectEditHLPForm").serializeArray();
                                        var bahanData = $("#bahanEditHLPForm").serializeArray();
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
                                            url: "{{ route('closing-mesin.updateHlp') }}", // Replace with your update route
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
                                                $('#editHLPModal').modal('hide');
                                                $("#sisaHasilEditHLPForm")[0].reset();
                                                $("#rejectEditHLPForm")[0].reset();
                                                $("#bahanEditHLPForm")[0].reset();
                                                $('#trgt_id').val('');
                                                $('#produk').val('');
                                                $('#datatableDetail').DataTable().ajax.reload();
                                                resetJQuerySteps('#form-edit-hlp',3);
                                            }
                                        });
                                    // }
                                }
                            });
                        },
                        error: function(xhr, status, error) {
                            console.error("AJAX Error: ", status, error);
                        }
                    });
                } else if (jenis.substring(0, 2) == 'MK') {
                    $('#modalTitleEditMaker').html(`Edit MAKER (Shift ${shift}) (${nm_mesin}) (${nm_brg})`);
                    $('#trgt_id').val(trgtId);
                    $('#produk').val(brg_id);
                    $.ajax({
                        url: "{{ route('closing-mesin.edit') }}/", // Replace with your update route
                        type: "get",
                        data: {
                            closing_id: trgtId,
                            jenis: 1,
                        },
                        success: function(data) {
                            $(".edit-maker").html(data);
                            $('#editMakerModal').modal('show');
                            $("#form-edit-maker").steps({
                                headerTag: "h3",
                                bodyTag: "section",
                                transitionEffect: "slide",
                                labels: {
                                    finish: "Perbarui",
                                    next: "Selanjutnya",
                                    previous: "Sebelumnya",
                                },
                                autoFocus: true,
                                enableFinishButton: true,
                                onStepChanged: function(event, currentIndex) {
                                    // Format numbers on step change if needed
                                    formatNumbers(); // Panggil fungsi untuk format angka
                                },
                                onFinished: function(event, currentIndex) {
                                    // if (validateForm("#bahanDetailForm")) {
                                        var sisaHasilData = $("#sisaHasilEditForm").serializeArray();
                                        var rejectData = $("#rejectEditForm").serializeArray();
                                        var bahanData = $("#bahanEditForm").serializeArray();
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
                                            url: "{{ route('closing-mesin.update') }}", // Replace with your update route
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
                                                $('#editMakerModal').modal('hide');
                                                $("#sisaHasilEditForm")[0].reset();
                                                $("#rejectEditForm")[0].reset();
                                                $("#bahanEditForm")[0].reset();
                                                $('#trgt_id').val('');
                                                $('#produk').val('');
                                                $('#datatableDetail').DataTable().ajax.reload();
                                                resetJQuerySteps('#form-edit-maker',3);
                                            }
                                        });
                                    // }
                                }
                            });
                        },
                        error: function(xhr, status, error) {
                            console.error("AJAX Error: ", status, error);
                        }
                    });

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

        function formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."); // Format dengan titik sebagai pemisah ribuan
        }

        $('#tgl-input, #msn-input').on('change', function() {
            $('#datatableDetail').DataTable().ajax.reload(); // Reload data based on new filters
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
                                <select name="mesin" id="msn-input" class="form-control">
                                    <option value="">---SEMUA---</option>
                                    <option value="MK">MAKER</option>
                                    <option value="HLP">HLP</option>
                                </select>
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
                                                    ["label" => "TRAY", "placeholder" => "Enter TRAY", "name" => "TRAY", "satuan" => "TRAY"],
                                                    ["label" => "Batangan", "placeholder" => "Enter Batangan", "name" => "BTG", "satuan" => "BTG"],
                                                    ["label" => "Batangan Reject", "placeholder" => "Enter Batangan Reject", "name" => "btg_reject", "satuan" => "BTG"],
                                                ];

                                                foreach ($fields as $index => $field) {
                                                    echo '<div class="col-lg-4">
                                                            <div class="mb-3">
                                                                <label for="input-' . $index . '">' . $field['label'] . '</label>
                                                                <div class="input-group">
                                                                    <div class="col-xl-9">
                                                                        <input type="text" class="form-control number" value="0" name="' . $field['name'] .'" id="input-' . $index . '" placeholder="' . $field['placeholder'] . '" pattern="\d*" inputmode="numeric" required>
                                                                    </div>
                                                                    <label class="col-md-2 col-form-label ms-2">' . $field['satuan'] . '</label>
                                                                </div>
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
                                                    ["label" => "Debu", "placeholder" => "Enter Debu", "name" => "debu", "satuan" => "KG"],
                                                    ["label" => "Sapon", "placeholder" => "Enter Sapon", "name" => "sapon", "satuan" => "KG"],
                                                    ["label" => "CP Reject", "placeholder" => "Enter CP Reject", "name" => "cp", "satuan" => "KG"],
                                                    ["label" => "Filter Reject", "placeholder" => "Enter Filter Reject", "name" => "filter", "satuan" => "KG"],
                                                    ["label" => "CTP Reject", "placeholder" => "Enter CTP Reject", "name" => "ctp", "satuan" => "KG"],
                                                ];

                                                foreach ($fields as $index => $field) {
                                                    echo '<div class="col-lg-4">
                                                            <div class="mb-3">
                                                                <label for="rejectInput-' . $index . '">' . $field['label'] . '</label>
                                                                <div class="input-group">
                                                                    <div class="col-xl-9">
                                                                        <input type="text" class="form-control number" value="0" name="' . $field['name'] .'" id="rejectInput-' . $index . '" placeholder="' . $field['placeholder'] . '" pattern="\d*" inputmode="numeric" required>
                                                                    </div>
                                                                    <label class="col-md-2 col-form-label ms-2">' . $field['satuan'] . '</label>
                                                                </div>
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
                                                    ["label" => "TSG", "placeholder" => "Enter TSG", "name" => "tsg", "satuan" => "KG"],
                                                    ["label" => "CP", "placeholder" => "Enter CP", "name" => "cp", "satuan" => "ROLL"],
                                                    ["label" => "Filter", "placeholder" => "Enter Filter", "name" => "filter", "satuan" => "BTG"],
                                                    ["label" => "CTP", "placeholder" => "Enter CTP", "name" => "ctp", "satuan" => "ROLL"],
                                                ];

                                                foreach ($fields as $index => $field) {
                                                    echo '<div class="col-lg-4">
                                                            <div class="mb-3">
                                                                <label for="bahanInput-' . $index . '">' . $field['label'] . '</label>
                                                                <div class="input-group">
                                                                    <div class="col-xl-9">
                                                                        <input type="text" class="form-control number" value="0" name="' . $field['name'] .'" id="bahanInput-' . $index . '" placeholder="' . $field['placeholder'] . '" pattern="\d*" inputmode="numeric" required>
                                                                    </div>
                                                                    <label class="col-md-2 col-form-label ms-2">' . $field['satuan'] . '</label>
                                                                </div>
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

<!-- Modal DetailMaker-->
<div class="modal fade" id="detailMakerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="modalTitleDetailMaker" class="modal-title">Closing MAKER</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body detail-maker">
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

<!-- Modal EditMaker-->
<div class="modal fade" id="editMakerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="modalTitleEditMaker" class="modal-title">Closing MAKER</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body edit-maker">
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
                                                    ["label" => "Karton", "placeholder" => "Enter Karton", "name" => "karton", 'satuan' => 'Karton'],
                                                    ["label" => "Ball", "placeholder" => "Enter Ball", "name" => "ball", 'satuan' => 'Ball'],
                                                    ["label" => "Slop", "placeholder" => "Enter Slop", "name" => "slop", 'satuan' => 'Slop'],
                                                    ["label" => "Pack OPP", "placeholder" => "Enter Pack OPP", "name" => "opp_pack", 'satuan' => 'Pack'],
                                                    ["label" => "NPC", "placeholder" => "Enter NPC", "name" => "npc", 'satuan' => 'Pack'],
                                                    ["label" => "Pack Reject", "placeholder" => "Enter Pack Reject", "name" => "pack_reject", 'satuan' => 'Pack'],
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
                                                                <div class="input-group">
                                                                    <div class="col-xl-9">
                                                                        <input type="text" class="form-control number" value="0" name="' . $field['name'] .'" id="input-' . $index . '" placeholder="' . $field['placeholder'] . '" pattern="\d*" inputmode="numeric" required>
                                                                    </div>
                                                                <label class="col-md-2 col-form-label ms-2">' . $field['satuan'] . '</label>
                                                            </div>
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
                                                    ["label" => "Foil", "placeholder" => "Enter Foil", "name" => "foil", 'satuan' => 'ROLL'],
                                                    ["label" => "Inner", "placeholder" => "Enter Inner", "name" => "inner", 'satuan' => 'ROLL'],
                                                    ["label" => "Etiket", "placeholder" => "Enter Etiket", "name" => "etiket", 'satuan' => 'PCS'],
                                                    ["label" => "Pita Cukai", "placeholder" => "Enter Pita Cukai", "name" => "pc", 'satuan' => 'PCS'],
                                                    ["label" => "OPP Pack", "placeholder" => "Enter OPP Pack", "name" => "opp_pack", 'satuan' => 'ROLL'],
                                                    ["label" => "Teartape", "placeholder" => "Enter Teartape", "name" => "teartape", 'satuan' => 'ROLL'],
                                                    ["label" => "OPP Slop", "placeholder" => "Enter OPP Slop", "name" => "opp_slop", 'satuan' => 'PCS'],
                                                    ["label" => "Barcode Slop", "placeholder" => "Enter Segel Slop", "name" => "barcode_slop", 'satuan' => 'PCS'],
                                                    ["label" => "Kertas Ball", "placeholder" => "Enter Kertas Ball", "name" => "kertas_ball", 'satuan' => 'PCS'],
                                                    ["label" => "Cap Ball", "placeholder" => "Enter Segel Ball", "name" => "cap_ball", 'satuan' => 'PCS'],
                                                    ["label" => "Karton", "placeholder" => "Enter Karton", "name" => "karton", 'satuan' => 'PCS'],
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
                                                                <div class="input-group">
                                                                    <div class="col-xl-9">
                                                                        <input type="text" class="form-control number" value="0" name="' . $field['name'] .'" id="input-' . $index . '" placeholder="' . $field['placeholder'] . '" pattern="\d*" inputmode="numeric" required>
                                                                    </div>
                                                                <label class="col-md-2 col-form-label ms-2">' . $field['satuan'] . '</label>
                                                            </div>
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
                                                    ["label" => "Foil", "placeholder" => "Enter Foil", "name" => "foil", 'satuan' => 'ROLL'],
                                                    ["label" => "Inner", "placeholder" => "Enter Inner", "name" => "inner", 'satuan' => 'ROLL'],
                                                    ["label" => "Etiket", "placeholder" => "Enter Etiket", "name" => "etiket", 'satuan' => 'PCS'],
                                                    ["label" => "Pita Cukai", "placeholder" => "Enter Pita Cukai", "name" => "pc", 'satuan' => 'PCS'],
                                                    ["label" => "OPP Pack", "placeholder" => "Enter OPP Pack", "name" => "opp_pack", 'satuan' => 'ROLL'],
                                                    ["label" => "Teartape", "placeholder" => "Enter Teartape", "name" => "teartape", 'satuan' => 'ROLL'],
                                                    ["label" => "OPP Slop", "placeholder" => "Enter OPP Slop", "name" => "opp_slop", 'satuan' => 'PCS'],
                                                    ["label" => "Barcode Slop", "placeholder" => "Enter Segel Slop", "name" => "barcode_slop", 'satuan' => 'PCS'],
                                                    ["label" => "Kertas Ball", "placeholder" => "Enter Kertas Ball", "name" => "kertas_ball", 'satuan' => 'PCS'],
                                                    ["label" => "Cap Ball", "placeholder" => "Enter Segel Ball", "name" => "cap_ball", 'satuan' => 'PCS'],
                                                    ["label" => "Karton", "placeholder" => "Enter Karton", "name" => "karton", 'satuan' => 'PCS'],
                                                    ["label" => "Batangan", "placeholder" => "Enter Batangan", "name" => "batangan", 'satuan' => 'PCS'],
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
                                                                <div class="input-group">
                                                                    <div class="col-xl-9">
                                                                        <input type="text" class="form-control number" value="0" name="' . $field['name'] .'" id="input-' . $index . '" placeholder="' . $field['placeholder'] . '" pattern="\d*" inputmode="numeric" required>
                                                                    </div>
                                                                <label class="col-md-2 col-form-label ms-2">' . $field['satuan'] . '</label>
                                                            </div>
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

<!-- Modal DetailHLP-->
<div class="modal fade" id="detailHLPModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="modalTitleDetailHLP" class="modal-title">Closing HLP</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body detail-hlp">
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

<!-- Modal EditHLP-->
<div class="modal fade" id="editHLPModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="modalTitleEditHLP" class="modal-title">Closing HLP</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body edit-hlp">
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