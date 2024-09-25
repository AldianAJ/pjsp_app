<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Barang;
use App\Models\Admin\TrStok;
use App\Models\Admin\Supplier;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
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
        $all_barang = Barang::where('status', 0)->get();

        if ($request->ajax()) {
            $brg_id = $request->get('brg_id');
            $date = $request->get('date');

            $barangs = TrStok::with('barang')
                ->when($brg_id, function ($query, $brg_id) {
                    return $query->where('brg_id', $brg_id);
                })
                ->when($date, function ($query, $date) {
                    $formattedDate = \Carbon\Carbon::createFromFormat('d-m-Y', $date)->format('Y-m-d');
                    return $query->whereDate('tgl', $formattedDate);
                })
                ->get();

            return DataTables::of($barangs)->make(true);
        }

        return view('pages.stok.index', compact('user', 'all_barang'));
    }

}
