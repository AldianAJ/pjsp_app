<form action="{{ route('log-produksi.update') }}" method="post" enctype="multipart/form-data">
    @csrf

    <div class="mb-3">
        <label for="trouble" class="form-label">Trouble</label>
        <textarea class="form-control" name="trouble" id="trouble" rows="6">{{ $data->trouble }}</textarea>
    </div>
    <input type="hidden" id="msn_trgt_id">
    <div class="mb-3">
        <label for="penanganan" class="form-label">Penanganan</label>
        <textarea class="form-control" name="penanganan" id="penanganan" rows="6">{{ $data->penanganan }}</textarea>
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="waktu_mulai" class="form-label">Waktu Mulai</label>
            <input type="time" class="form-control" name="waktu_mulai" id="waktu_mulai" value="{{ $data->waktu_mulai }}"
                onblur="calculateTimeDifference()">
        </div>

        <div class="col-md-6">
            <label for="waktu_selesai" class="form-label">Waktu Selesai</label>
            <input type="time" class="form-control" name="waktu_selesai" id="waktu_selesai"
                value="{{ $data->waktu_selesai }}" onblur="calculateTimeDifference()">
        </div>
    </div>
    <div class="mb-3">
        <label for="lost_time_text" class="form-label">Lost Time</label>
        <input type="text" class="form-control" name="lost_time_text" id="lost_time_text"
            value="{{ $data->lost_time_text }}">
    </div>
    <input type="hidden" class="form-control" name="lost_time" id="lost_time" value="{{ $data->lost_time }}">

    <div class="mb-3">
        <label for="ket" class="form-label">Keterangan</label>
        <textarea class="form-control" name="ket" id="ket" rows="6">{{ $data->ket }}</textarea>
    </div>
    <div class="mb-3">
        <label for="pic" class="form-label">PIC</label>
        <input type="text" class="form-control" name="pic" id="pic" value="{{ $data->pic }}">
    </div>
    <input type="hidden" name="logprod_id" id="logprod_id" value="{{ $data->logprod_id }}">
    <input type="hidden" name="msn_trgt_id" id="msn_trgt_id" value="{{ $data->msn_trgt_id }}">
    <div class="d-flex justify-content-end mt-3">
        <button type="button" class="btn btn-secondary waves-effect waves-light me-1"
            data-bs-dismiss="modal">Tutup</button>
        <button type="submit" class="btn btn-primary waves-effect waves-light" id="saveButton">
            <i class="bx bx bxs-save align-middle me-2 font-size-18"></i>Simpan
        </button>
    </div>
</form>