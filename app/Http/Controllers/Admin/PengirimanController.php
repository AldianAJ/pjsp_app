<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Barang;
use App\Models\Admin\Pengiriman;
use App\Models\Admin\DetailPengiriman;
use App\Models\Admin\Permintaan;
use App\Models\Admin\DetailPermintaan;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;

class PengirimanController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function userAuth()
    {
        $user = Auth::guard('user')->user();
        return $user;
    }
    public function index(Request $request)
    {
        $user = $this->userAuth();
        $path = 'pengiriman.';

        if ($request->ajax()) {
            $permintaans = DB::table('tr_reqskm as a')
                ->leftJoin('tr_krmskm_detail as b', 'a.no_reqskm', '=', 'b.no_reqskm')
                ->leftJoin('tr_krmskm as c', 'b.no_krmskm', '=', 'c.no_krmskm')
                ->select('a.no_reqskm', 'a.tgl as tgl_minta', 'c.tgl as tgl_kirim')
                ->where('a.status', 0)
                ->get();
            return DataTables::of($permintaans)
                ->addColumn('action', function ($object) use ($path) {
                    $no = str_replace('/','-', $object->no_reqskm);
                    $html = '<a href="' . route($path . "create", ["no_reqskm" => $no]) . '" class="btn btn-secondary waves-effect waves-light mx-1">'
                        . ' <i class="bx bx-edit align-middle me-2 font-size-18"></i> Proses</a>';
                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('pages.pengiriman.index', compact('user'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(string $no_reqskm, Request $request)
    {
        $user = $this->userAuth();
        $no = str_replace('-','/', $no_reqskm);
        $datas = DetailPermintaan::with('barang')->where('no_reqskm', $no)->get();
        $path = 'pengiriman.create.';
        if ($request->ajax()) {
            $barangs = Barang::where('status', 0)->get();
            return DataTables::of($barangs)
                ->make(true);
        }
        return view('pages.pengiriman.create', compact('user', 'datas', 'no_reqskm'));

    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $no_krmskm = 'SJ/GU' . '/' . date('y/m/' . str_pad(Pengiriman::count() + 1, 3, '0', STR_PAD_LEFT));

        $krmSKM = Pengiriman::create([
            'no_krmskm' => $no_krmskm,
            'tgl' => $request->tgl,

        ]);

        foreach ($request->items as $item) {
            DetailPengiriman::create([
                'no_krmskm' => $no_krmskm,
                'brg_id' => $item['brg_id'],
                'qty' => $item['qty'],
                'satuan_besar' => $item['satuan_besar'],
            ]);
        }

        return redirect()->route('pengiriman')->with('success', 'Data pengiriman berhasil ditambahkan.');
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


}
