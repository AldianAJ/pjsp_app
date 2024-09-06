<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\user;
use App\Models\Admin\Barang;
use App\Models\Admin\Permintaan;
use App\Models\Admin\Gudang;
use App\Models\Admin\DetailPermintaan;
use App\Models\Admin\Pengiriman;
use App\Models\Admin\DetailPengiriman;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class PermintaanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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
        $path = 'permintaan.';

        if ($request->ajax()) {
            $permintaans = Permintaan::where('status', 0)->get();
            return DataTables::of($permintaans)
                ->addColumn('action', function ($object) use ($path) {
                    $html = '<a href="' . route($path . "edit", ["no_reqskm" => $object->no_reqskm]) . '" class="btn btn-secondary waves-effect waves-light mx-1">'
                        . ' <i class="bx bx-edit align-middle me-2 font-size-18"></i> Edit</a>';
                    $html .= '<button class="btn btn-primary waves-effect waves-light mx-1 btn-detail" data-no_reqskm="' . $object->no_reqskm . '">' .
                        '<i class="bx bx-show align-middle font-size-18"></i> Detail</button>';
                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('pages.permintaan.index', compact('user'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $user = $this->userAuth();
        $gudang_id = Gudang::where('jenis', 2)->value('gudang_id');
        $path = 'permintaan.create.';
        if ($request->ajax()) {
            $barangs = Barang::where('status', 0)->get();
            return DataTables::of($barangs)
                ->addColumn('action', function ($object) use ($path) {
                    $html = '<div class="d-flex justify-content-center"><button class="btn btn-primary waves-effect waves-light btn-add" data-bs-toggle="modal"' .
                        'data-bs-target="#qtyModal"><i class="bx bx-plus-circle align-middle font-size-18"></i></button></div>';
                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('pages.permintaan.create', compact('user', 'gudang_id'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $no_reqskm = 'FPB/SKM' . '/' . date('y/m/' . str_pad(Permintaan::count() + 1, 3, '0', STR_PAD_LEFT));

        $gudang_id = $request->gudang_id;

        $reqSKM = Permintaan::create([
            'no_reqskm' => $no_reqskm,
            'tgl' => $request->tgl,
            'gudang_id' => $gudang_id,

        ]);

        foreach ($request->items as $item) {
            DetailPermintaan::create([
                'no_reqskm' => $no_reqskm,
                'brg_id' => $item['brg_id'],
                'qty' => $item['qty'],
                'satuan_besar' => $item['satuan_besar'],
            ]);
        }

        return redirect()->route('permintaan')->with('success', 'Data permintaan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function indexDetail($no_reqskm)
    {
        $details = DetailPermintaan::where('no_reqskm', $no_reqskm)->first();
        return response()->json($details);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit()
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update()
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy()
    {
        //
    }

    public function indexTerima(Request $request)
    {
        $user = $this->userAuth();
        $path = 'penerimaan-barang.';

        if ($request->ajax()) {
            $pengirimans = Pengiriman::where('status', 0)->get();
            return DataTables::of($pengirimans)
                ->addColumn('action', function ($object) use ($path) {
                    $no = str_replace('/', '-', $object->id);
                    return '<a href="' . route($path . "create", ["no_krmskm" => $no]) . '" class="btn btn-primary waves-effect waves-light mx-1">'
                        . '<i class="bx bx-transfer-alt align-middle me-2 font-size-18"></i> Proses</a>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('pages.terima-barang-skm.index', compact('user'));
    }

    public function createTerima(string $no_krmskm, Request $request)
    {
        $user = $this->userAuth();
        $no_krm = str_replace('-', '/', $no_krmskm);
        $no_req = Permintaan::select('no_reqskm')->where('status', 0)->get();

        $data_krms = DetailPengiriman::with('barang')
            ->where('no_krmskm', $no_krm)
            ->where('status', 0)
            ->get();

        $data_reqs = DetailPermintaan::with('barang')
            ->where('no_reqskm')
            ->where('status', 0)
            ->get();

        $user_id = User::where('role', 'skm')->where('status', 0)->get();

        $gudang_id = Gudang::where('jenis', 2)->value('gudang_id');
        $path = 'penerimaan-barang.create.';

        if ($request->ajax()) {
            $barangs = Barang::where('status', 0)->get();
            return DataTables::of($barangs)->make(true);
        }


        return view('pages.terima-barang-skm.create', compact('user', 'datas', 'no_reqskm', 'no_req', 'gudang_id'));
    }
}
