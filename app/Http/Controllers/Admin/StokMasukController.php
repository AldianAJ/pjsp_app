<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\StokMasuk;
use App\Models\Admin\DetailStokMasuk;
use App\Models\Admin\Gudang;
use App\Models\Admin\Supplier;
use App\Models\Admin\Barang;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
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
        $path = 'stok-masuk.';
        if ($request->ajax()) {
            $stokMasuks = StokMasuk::where('status', 0)->get();
            return DataTables::of($stokMasuks)
                ->addColumn('action', function ($object) use ($path) {
                    $html = '<a href="' . route($path . "edit", ["no_trm" => $object->no_trm]) . '" class="btn btn-secondary waves-effect waves-light">'
                        . ' <i class="bx bx-edit align-middle me-2 font-size-18"></i> Edit</a>';
                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('pages.stok-masuk.index', compact('user'));
    }

    public function create(Request $request)
    {
        $user = $this->userAuth();

        // $gudangs = Gudang::where('jenis', 2)->get();
        $suppliers = Supplier::where('status', 0)->get();

        $path = 'stok-masuk.create.';
        if ($request->ajax()) {
            $barangs = Barang::where('status', 0)->get();
            return DataTables::of($barangs)
                ->addColumn('action', function ($object) use ($path) {
                    // $html = '<div class="d-flex justify-content-center"><button class="btn btn-primary waves-effect waves-light btn-add" data-bs-toggle="modal"' .
                    //         'data-bs-target="#qtyModal"><i class="bx bx-plus-circle align-middle font-size-18"></i></button></div>';
                    //     return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('pages.stok-masuk.create', compact('user', 'suppliers'));
    }

    public function store(Request $request)
    {

        $no_trm = 'RCV/GU' . '/' . date('y/m/' . str_pad(StokMasuk::count() + 1, 3, '0', STR_PAD_LEFT));

        $stokMasuk = StokMasuk::create([
            'no_trm' => $no_trm,
            'no_sj' => $request->no_sj,
            'supplier_id' => $request->supplier_id,
            'tgl' => $request->tgl,
        ]);

        foreach ($request->items as $item) {
            DetailStokMasuk::create([
                'no_trm' => $no_trm,
                'brg_id' => $item['brg_id'],
                'qty' => $item['qty'],
                'satuan_beli' => $item['satuan_beli'],
                'ket' => $item['ket'],
            ]);
        }

        return redirect()->route('stok-masuk')->with('success', 'Data stok masuk berhasil ditambahkan.');
    }



}

