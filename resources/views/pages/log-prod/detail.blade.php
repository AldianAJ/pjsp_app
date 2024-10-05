<div class="mb-3">
    <label for="trouble" class="form-label">Trouble</label>
    <textarea class="form-control" name="trouble" id="trouble" rows="6" readonly>{{ $data->trouble }}</textarea>
</div>
<input type="hidden" id="msn_trgt_id">
<div class="mb-3">
    <label for="penanganan" class="form-label">Penanganan</label>
    <textarea class="form-control" name="penanganan" id="penanganan" rows="6"
        readonly>{{ $data->penanganan }}</textarea>
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="waktu_mulai" class="form-label">Waktu Mulai</label>
        <input type="time" class="form-control" name="waktu_mulai" id="waktu_mulai" readonly
            value="{{ $data->waktu_mulai }}">
    </div>

    <div class="col-md-6">
        <label for="waktu_selesai" class="form-label">Waktu Selesai</label>
        <input type="time" class="form-control" name="waktu_selesai" id="waktu_selesai" readonly
            value="{{ $data->waktu_selesai }}">
    </div>
</div>
<div class="mb-3">
    <label for="lost_time" class="form-label">Lost Time</label>
    <input type="text" class="form-control" name="lost_time" id="lost_time" readonly value="{{ $data->lost_time }}">
</div>

<div class="mb-3">
    <label for="ket" class="form-label">Keterangan</label>
    <textarea class="form-control" name="ket" id="ket" rows="6" readonly>{{ $data->ket }}</textarea>
</div>
<div class="mb-3">
    <label for="pic" class="form-label">PIC</label>
    <input type="text" class="form-control" name="pic" id="pic" readonly value="{{ $data->pic }}">
</div>