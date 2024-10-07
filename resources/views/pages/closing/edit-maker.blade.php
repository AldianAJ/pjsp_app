<div id="form-edit-maker">
    <h3>Sisa Hasil Produksi</h3>
    <section>
        <input type="hidden" id="trgt_id" name="trgt_id">
        <input type="hidden" id="produk" name="produk">
        <form id="sisaHasilEditForm">
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
                                        <input type="text" class="form-control number" value="'. $formData[$field['name']] .'" name="' . $field['name'] .'" id="input-' . $index . '" placeholder="' . $field['placeholder'] . '" pattern="^\d+(\.\d+)?$" inputmode="numeric" required>
                                    </div>
                                    <label class="col-md-2 col-form-label fw-bolder ms-2">' . $field['satuan'] . '</label>
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
        <form id="rejectEditForm">
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
                                        <input type="text" class="form-control number" value="'. $formReject[$field['name']] .'" name="' . $field['name'] .'" id="rejectInput-' . $index . '" placeholder="' . $field['placeholder'] . '" pattern="^\d+(\.\d+)?$" inputmode="numeric" required>
                                    </div>
                                    <label class="col-md-2 col-form-label fw-bolder ms-2">' . $field['satuan'] . '</label>
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
        <form id="bahanEditForm">
            <div class="row">
                <?php
                $fields = [
                    ["label" => "TSG", "placeholder" => "Enter TSG", "name" => "tsg", "satuan" => "KG"],
                    ["label" => "CP", "placeholder" => "Enter CP", "name" => "cp", "satuan" => "ROLL"],
                    ["label" => "CP KG", "placeholder" => "Enter CP", "name" => "cp_kg", "satuan" => "KG"],
                    ["label" => "Filter", "placeholder" => "Enter Filter", "name" => "filter", "satuan" => "BTG"],
                    ["label" => "Filter KG", "placeholder" => "Enter Filter", "name" => "filter_kg", "satuan" => "KG"],
                    ["label" => "CTP", "placeholder" => "Enter CTP", "name" => "ctp", "satuan" => "ROLL"],
                    ["label" => "CTP KG", "placeholder" => "Enter CTP", "name" => "ctp_kg", "satuan" => "KG"],
                ];

                foreach ($fields as $index => $field) {
                    echo '<div class="col-lg-6">
                            <div class="mb-3">
                                <label for="bahanInput-' . $index . '">' . $field['label'] . '</label>
                                <div class="input-group">
                                    <div class="col-xl-9">
                                        <input type="text" class="form-control number" value="'. $formBahan[$field['name']] .'" name="' . $field['name'] .'" id="bahanInput-' . $index . '" placeholder="' . $field['placeholder'] . '" pattern="^\d+(\.\d+)?$" inputmode="numeric" required>
                                    </div>
                                    <label class="col-md-2 col-form-label fw-bolder ms-2">' . $field['satuan'] . '</label>
                                </div>
                            </div>
                          </div>';
                }
                ?>
            </div>
        </form>
    </section>
</div>