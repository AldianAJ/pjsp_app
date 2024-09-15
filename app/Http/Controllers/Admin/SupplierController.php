<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Supplier;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
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
        $path = 'supplier.';
        if($request->ajax()) {
            $suppliers = Supplier::where('status', 0)->get();
            return DataTables::of($suppliers)
            ->addColumn('action', function ($object) use ($path) {
                $html = '<a href="' . route($path . "edit", ["supplier_id" => $object->supplier_id]) . '" class="btn btn-success waves-effect waves-light">'
                    . ' <i class="bx bx-edit align-middle me-2 font-size-18"></i> Edit</a>';
                return $html;
            })
            ->rawColumns(['action'])
            ->make(true);
        }
        return view('pages.supplier.index', compact('user'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = $this->userAuth();
        $supplier_id = 'S' . str_pad(Supplier::count() + 1, 3, '0', STR_PAD_LEFT);
        return view('pages.supplier.create', compact('supplier_id', 'user'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|unique:m_supplier,supplier_id',
            'nama' => 'required|string|max:50',
            'address' => 'required',
            'telp' => 'required|numeric',
        ]);

        Supplier::create([
            'supplier_id' => $request->supplier_id,
            'nama' => $request->nama,
            'address' => $request->address,
            'telp' => $request->telp,
        ]);

        return redirect()->route('supplier')->with('success', 'Data supplier berhasil ditambahkan.');
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
    public function edit(string $supplier_id)
    {
        $data = Supplier::where('supplier_id', $supplier_id)->first();
        return view('pages.supplier.edit', compact('data',  'supplier_id'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $supplier_id)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'address' => 'required',
            'telp' => 'required|numeric',
        ]);

        $supplier = Supplier::where('supplier_id', $supplier_id)->first();
        $supplier->update([
            'nama' => $request->nama,
            'address' => $request->address,
            'telp' => $request->telp,
        ]);

        return redirect()->route('supplier')->with('success', 'Data supplier berhasil diperbarui.');
    }
}
