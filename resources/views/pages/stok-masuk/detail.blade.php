@extends('layouts.app')

@section('content')
    <h3>Details for No Doc: {{ $detaiPersediaanMasuks->no_doc }}</h3>

    <form action="{{ route('persediaanMasuk.saveDetail', $detaiPersediaanMasuks->no_doc) }}" method="POST">
        @csrf
        @foreach ($detaiPersediaanMasuks->details as $detail)
            <div>
                <label>Barang:</label>
                <select name="details[{{ $loop->index }}][brg_id]">
                    @foreach ($barangs as $barang)
                        <option value="{{ $barang->brg_id }}" {{ $detail->brg_id == $barang->brg_id ? 'selected' : '' }}>
                            {{ $barang->name }}
                        </option>
                    @endforeach
                </select>
                <input type="hidden" name="details[{{ $loop->index }}][id]" value="{{ $detail->id }}">
            </div>
            <div>
                <label>Quantity:</label>
                <input type="number" name="details[{{ $loop->index }}][qty]" value="{{ $detail->qty }}">
            </div>
            <div>
                <label>Satuan Besar:</label>
                <input type="text" name="details[{{ $loop->index }}][satuan_besar]"
                    value="{{ $detail->satuan_besar }}">
            </div>
            <div>
                <label>Keterangan:</label>
                <input type="text" name="details[{{ $loop->index }}][ket]" value="{{ $detail->ket }}">
            </div>
        @endforeach
        <button type="submit">Save</button>
    </form>
@endsection
