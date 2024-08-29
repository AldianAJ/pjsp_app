<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\StokMasuk;
use App\Models\Admin\DetailStokMasuk;
use App\Models\Admin\Gudang;
use App\Models\Admin\Supplier;
use App\Models\Admin\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class StokMasukController extends Controller
{
    public function userAuth()
    {
        $user = Auth::guard('user')->user();
        return $user;
    }

    public function index(Request $request)
    {
        $user = $this->userAuth();
        $stokMasuks = StokMasuk::where('status', 0)->get();
        return view('pages.stok-masuk.index', compact('user', 'stokMasuks'));
    }

    public function create()
    {
        $user = $this->userAuth();
        $NoTrms = StokMasuk::generateNoTrm();
        // $gudangs = Gudang::where('jenis', 2)->get();
        $barangs = Barang::select('brg_id', 'nm_brg', 'satuan_besar')
            ->where('status', 0)
            ->get();
        $suppliers = Supplier::select('supplier_id', 'nama')
            ->where('status', 0)
            ->get();
        return view('pages.stok-masuk.create', compact('user', 'NoTrms', 'barangs', 'suppliers'));
    }

    public function store(Request $request)
{
    Log::info($request->all());

    $request->validate([
        'no_trm' => 'required|string|unique:tr_terima_supplier',
        'no_sj' => 'required|string',
        'supplier_id' => 'required|exists:m_supplier,supplier_id',
        'tgl' => 'required|date',
        'items' => 'required|array',
        'items.*.brg_id' => 'required|exists:m_brg,brg_id',
        'items.*.qty' => 'required|integer|min:1',
        'items.*.satuan_besar' => 'required|string',
        'items.*.ket' => 'required',
    ]);

    $stokMasuk = StokMasuk::create([
        'no_trm' => $request->no_trm,
        'no_sj' => $request->no_sj,
        'supplier_id' => $request->supplier_id,
        'tgl' => $request->tgl,
    ]);

    foreach ($request->items as $item) {
        DetailStokMasuk::create([
            'no_trm' => $stokMasuk->no_trm,
            'brg_id' => $item['brg_id'],
            'qty' => $item['qty'],
            'satuan_besar' => $item['satuan_besar'],
            'ket' => $item['ket'] ?? null,
        ]);
    }

    return redirect()->route('stok-masuk.index')->with('success', 'Data stok masuk berhasil ditambahkan.');
}


}

