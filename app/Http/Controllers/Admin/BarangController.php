<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Barang;
use App\Models\Admin\Gudang;
use App\Models\Admin\Supplier;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
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
        $path = 'barang.';
        if ($request->ajax()) {
            $barangs = Barang::where('status', 0)->get();
            return DataTables::of($barangs)
                ->addColumn('action', function ($object) use ($path) {
                    $html = '<a href="' . route($path . "edit", ["brg_id" => $object->brg_id]) . '" class="btn btn-success waves-effect waves-light">'
                        . ' <i class="bx bx-edit align-middle me-2 font-size-18"></i> Edit</a>';
                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('pages.barang.index', compact('user'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = $this->userAuth();
        $barang_id = 'B' . str_pad(Barang::count() + 1, 3, '0', STR_PAD_LEFT);
        $suppliers = Supplier::where('status', 0)->get();
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
        $suppliers = Supplier::where('status', 0)->get();
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
        ]);

        return redirect()->route('barang')->with('success', 'Data barang berhasil diperbarui.');
    }

    public function getSidebarData()
    {
        $totalBarangs = Barang::where('status', 0)->count();

        return response()->json([
            'totalBarangs' => $totalBarangs
        ]);
    }

    public function indexStok(Request $request)
    {
        $user = $this->userAuth();

        if ($request->ajax()) {
            $type = $request->input('type');

            if ($type == 'gudangs') {
                $gudangs = Gudang::where('status', 0)->where('jenis', 2)->get();
                return DataTables::of($gudangs)->make(true);

            } elseif ($type == 'barangs') {
                $barangs = Barang::where('status', 0)->get();
                return DataTables::of($barangs)->make(true);

            } elseif ($type == 'data_stoks') {
                $gudang_id = $request->input('gudang_id');
                $brg_id = $request->input('brg_id');

                $data_stoks = DB::table('tr_stok as a')
                    ->join('m_brg as b', 'a.brg_id', '=', 'b.brg_id')
                    ->select('a.tgl', 'b.nm_brg', 'a.doc_id', 'a.ket', 'a.akhir')
                    ->where('a.gudang_id', $gudang_id) // Assuming 'gudang_id' is a column in 'tr_stok'
                    ->where('a.brg_id', $brg_id)
                    ->orderBy('a.tgl', 'desc')
                    ->get();

                return DataTables::of($data_stoks)->make(true);
            }
        }
        return view('pages.stok.index', compact('user'));
    }

}
