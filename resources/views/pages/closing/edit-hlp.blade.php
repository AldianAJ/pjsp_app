<div id="form-edit-hlp">
    <h3>Sisa Hasil Produksi</h3>
    <section>
        <input type="hidden" id="trgt_id_HLP" name="trgt_id">
        <input type="hidden" id="produk_HLP" name="produk">
        <form id="sisaHasilEditHLPForm">
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
                                        <input type="text" class="form-control number" value="'. $formData[$field['name']] .'" name="' . $field['name'] .'" id="input-' . $index . '" placeholder="' . $field['placeholder'] . '" pattern="^\d+(\.\d+)?$" inputmode="numeric" required>
                                    </div>
                                    <label class="col-md-2 col-form-label fw-bolder ms-2">' . $field['satuan'] . '</label>
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
        <form id="rejectEditHLPForm">
            <?php
                $fields = [
                    ["label" => "Foil", "placeholder" => "Enter Foil", "name" => "foil", 'satuan' => 'KG'],
                    ["label" => "Inner", "placeholder" => "Enter Inner", "name" => "inner", 'satuan' => 'KG'],
                    ["label" => "Etiket", "placeholder" => "Enter Etiket", "name" => "etiket", 'satuan' => 'KG'],
                    ["label" => "Pita Cukai", "placeholder" => "Enter Pita Cukai", "name" => "pc", 'satuan' => 'KG'],
                    ["label" => "OPP Pack & Teartape", "placeholder" => "Enter OPP Pack", "name" => "opp_pack_teartape", 'satuan' => 'KG'],
                    ["label" => "OPP Slop", "placeholder" => "Enter OPP Slop", "name" => "opp_slop", 'satuan' => 'KG'],
                    ["label" => "Barcode Slop", "placeholder" => "Enter Segel Slop", "name" => "barcode_slop", 'satuan' => 'KG'],
                    ["label" => "Kertas Ball", "placeholder" => "Enter Kertas Ball", "name" => "kertas_ball", 'satuan' => 'KG'],
                    ["label" => "Cap Ball", "placeholder" => "Enter Segel Ball", "name" => "cap_ball", 'satuan' => 'KG'],
                    ["label" => "Karton", "placeholder" => "Enter Karton", "name" => "karton", 'satuan' => 'KG'],
                    ["label" => "Batangan", "placeholder" => "Enter Batangan", "name" => "batangan", 'satuan' => 'KG'],
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
                                        <input type="text" class="form-control number" value="'. $formReject[$field['name']] .'" name="' . $field['name'] .'" id="input-' . $index . '" placeholder="' . $field['placeholder'] . '" pattern="^\d+(\.\d+)?$" inputmode="numeric" required>
                                    </div>
                                    <label class="col-md-2 col-form-label fw-bolder ms-2">' . $field['satuan'] . '</label>
                                </div>
                            </div>
                          </div>';
                }
                echo '</div>';
                ?>
        </form>
    </section>

    <h3>Sisa Bahan</h3>
    <section>
        <div>
            <form id="bahanEditHLPForm">
                <?php
                $fields = [
                    ["label" => "Foil", "placeholder" => "Enter Foil", "name" => "foil", 'satuan' => 'ROLL'],
                    ["label" => "Foil", "placeholder" => "Enter Foil", "name" => "foil", 'satuan' => 'KG'],
                    ["label" => "Inner", "placeholder" => "Enter Inner", "name" => "inner", 'satuan' => 'ROLL'],
                    ["label" => "Inner", "placeholder" => "Enter Inner", "name" => "inner", 'satuan' => 'KG'],
                    ["label" => "Etiket", "placeholder" => "Enter Etiket", "name" => "etiket", 'satuan' => 'KARTON'],
                    ["label" => "Etiket", "placeholder" => "Enter Etiket", "name" => "etiket", 'satuan' => 'BANDEL'],
                    ["label" => "Pita Cukai", "placeholder" => "Enter Pita Cukai", "name" => "pc", 'satuan' => 'PCS'],
                    ["label" => "Pita Cukai", "placeholder" => "Enter Pita Cukai", "name" => "pc", 'satuan' => 'KG'],
                    ["label" => "OPP Pack", "placeholder" => "Enter OPP Pack", "name" => "opp_pack", 'satuan' => 'ROLL'],
                    ["label" => "OPP Pack", "placeholder" => "Enter OPP Pack", "name" => "opp_pack", 'satuan' => 'KG'],
                    ["label" => "Teartape", "placeholder" => "Enter Teartape", "name" => "teartape", 'satuan' => 'ROLL'],
                    ["label" => "Teartape", "placeholder" => "Enter Teartape", "name" => "teartape", 'satuan' => 'KG'],
                    ["label" => "OPP Slop", "placeholder" => "Enter OPP Slop", "name" => "opp_slop", 'satuan' => 'PCS'],
                    ["label" => "OPP Slop", "placeholder" => "Enter OPP Slop", "name" => "opp_slop", 'satuan' => 'KG'],
                    ["label" => "Barcode Slop", "placeholder" => "Enter Segel Slop", "name" => "barcode_slop", 'satuan' => 'PCS'],
                    ["label" => "Barcode Slop", "placeholder" => "Enter Segel Slop", "name" => "barcode_slop", 'satuan' => 'KG'],
                    ["label" => "Kertas Ball", "placeholder" => "Enter Kertas Ball", "name" => "kertas_ball", 'satuan' => 'RIM'],
                    ["label" => "Kertas Ball", "placeholder" => "Enter Kertas Ball", "name" => "kertas_ball", 'satuan' => 'LBR'],
                    ["label" => "Cap Ball", "placeholder" => "Enter Segel Ball", "name" => "cap_ball", 'satuan' => 'PCS'],
                    ["label" => "Cap Ball", "placeholder" => "Enter Segel Ball", "name" => "cap_ball", 'satuan' => 'KG'],
                    ["label" => "Karton", "placeholder" => "Enter Karton", "name" => "karton", 'satuan' => 'PCS'],
                    ["label" => "Karton", "placeholder" => "Enter Karton", "name" => "karton", 'satuan' => 'KG'],
                    ["label" => "Batangan", "placeholder" => "Enter Batangan", "name" => "batangan", 'satuan' => 'TRAY'],
                    ["label" => "Batangan", "placeholder" => "Enter Batangan", "name" => "batangan", 'satuan' => 'BTG'],
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
                                        <input type="text" class="form-control number" value="'. $formBahan[$field['name']] .'" name="' . $field['name'] .'" id="input-' . $index . '" placeholder="' . $field['placeholder'] . '" pattern="^\d+(\.\d+)?$" inputmode="numeric" required>
                                        </div>
                                    <label class="col-md-2 col-form-label fw-bolder ms-2">' . $field['satuan'] . '</label>
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