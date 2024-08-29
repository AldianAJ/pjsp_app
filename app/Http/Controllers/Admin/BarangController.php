<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Barang;
use App\Models\Admin\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BarangController extends Controller
{
    public function userAuth()
    {
        $user = Auth::guard('user')->user();
        return $user;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $this->userAuth();
        $barangs = Barang::where('status', 0)->get();
        return view('pages.barang.index', compact('user', 'barangs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = $this->userAuth();
        $barang_id = Barang::generateBarangId();
        $suppliers = Supplier::select('supplier_id', 'nama')
            ->where('status', 0)
            ->get();
        return view('pages.barang.create', compact('barang_id', 'user', 'suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'brg_id' => 'required|unique:m_brg,brg_id',
            'supplier_id' => 'required|exists:m_supplier,supplier_id',
            'nm_brg' => 'required|string|max:50',
            'satuan_beli' => 'required|string|max:10',
            'konversi1' => 'required|integer',
            'satuan_besar' => 'required|string|max:10',
            'konversi2' => 'required|integer',
            'satuan_kecil' => 'required|string|max:10',
            'konversi3' => 'required|integer',
        ]);

        Barang::create([
            'brg_id' => $request->brg_id,
            'supplier_id' => $request->supplier_id,
            'nm_brg' => $request->nm_brg,
            'satuan_beli' => $request->satuan_beli,
            'konversi1' => $request->konversi1,
            'satuan_besar' => $request->satuan_besar,
            'konversi2' => $request->konversi2,
            'satuan_kecil' => $request->satuan_kecil,
            'konversi3' => $request->konversi3,
        ]);

        return redirect()->route('barang')->with('success', 'Data barang berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $brg_id)
    {
        $data = Barang::where('brg_id', $brg_id)->first();
        $suppliers = Supplier::select('supplier_id', 'nama')
            ->where('status', 1)
            ->get();
        return view('pages.barang.edit', compact('data', 'suppliers', 'brg_id'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $brg_id)
    {
        $request->validate([
            'supplier_id' => 'required|exists:m_supplier,supplier_id',
            'nm_brg' => 'required|string|max:50',
            'satuan_beli' => 'required|string|max:10',
            'konversi1' => 'required|integer',
            'satuan_besar' => 'required|string|max:10',
            'konversi2' => 'required|integer',
            'satuan_kecil' => 'required|string|max:10',
            'konversi3' => 'required|integer',
        ]);

        $barang = Barang::where('brg_id', $brg_id)->first();
        $barang->update([
            'supplier_id' => $request->supplier_id,
            'nm_brg' => $request->nm_brg,
            'satuan_beli' => $request->satuan_beli,
            'konversi1' => $request->konversi1,
            'satuan_besar' => $request->satuan_besar,
            'konversi2' => $request->konversi2,
            'satuan_kecil' => $request->satuan_kecil,
            'konversi3' => $request->konversi3,
        ]);

        return redirect()->route('barang')->with('success', 'Data barang berhasil diperbarui.');
    }
}
