<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Gudang;
use App\Models\Admin\TrStok;
use App\Models\Admin\TrKrmSKM;
use App\Models\Admin\TrKrmSKMDetail;
use App\Models\Admin\TrReqSKM;
use App\Models\Admin\TrReqSKMDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class TrKrmSKMController extends Controller
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
        $path = 'pengiriman-gudang-utama.';

        $permintaans = DB::table('tr_reqskm as a')
            ->leftJoin('tr_krmskm_detail as b', 'a.no_reqskm', '=', 'b.no_reqskm')
            ->leftJoin('tr_krmskm as c', 'b.no_krmskm', '=', 'c.no_krmskm')
            ->select('a.no_reqskm as id', 'a.tgl as tgl_minta', 'c.tgl_krm')
            ->where('a.status', 0)
            ->distinct()
            ->orderBy('a.no_reqskm', 'desc')
            ->get();


        $pengirimans = DB::table('tr_krmskm as a')
            ->leftJoin('tr_krmskm_detail as b', 'a.no_krmskm', '=', 'b.no_krmskm')
            ->leftJoin('tr_reqskm as c', 'b.no_reqskm', '=', 'c.no_reqskm')
            ->select('a.no_krmskm as id', 'c.tgl as tgl_minta', 'a.tgl_krm')
            ->where('a.status', 0)
            ->distinct()
            ->orderBy('a.no_krmskm', 'desc')
            ->get();


        $activeVariable = !$permintaans->isEmpty() && !$pengirimans->isEmpty()
            ? $permintaans->merge($pengirimans)
            : (!$permintaans->isEmpty()
                ? $permintaans
                : $pengirimans);

        if ($request->ajax()) {
            return DataTables::of($activeVariable)
                ->addColumn('action', function ($object) use ($path) {
                    $no = str_replace('/', '-', $object->id);
                    if (is_null($object->tgl_krm)) {
                        return '<a href="' . route($path . "create", ["no_reqskm" => $no]) . '" class="btn btn-info waves-effect waves-light mx-1">'
                            . '<i class="bx bx-transfer-alt align-middle me-2 font-size-18"></i> Proses</a>';
                    } else {
                        return '<a href="' . route($path . "detailKRM", ["no_krmskm" => $no]) . '" class="btn btn-secondary waves-effect waves-light mx-1">'
                            . '<i class="bx bx-show align-middle me-2 font-size-18"></i> Detail</a>';
                    }
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('pages.pengiriman-gu.index', compact('user'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request, string $no_reqskm)
    {
        $user = $this->userAuth();
        $no_req = str_replace('-', '/', $no_reqskm);

        // $datas = TrReqSKMDetail::with('barang')
        //     ->where('no_reqskm', $no_req)
        //     ->where('status', 0)
        //     ->get();

        $gudang_id = Gudang::where('jenis', 2)->value('gudang_id');
        $path = 'pengiriman-gudang-utama.create.';

        if ($request->ajax()) {
            $data_mintas = TrReqSKMDetail::with('barang')
                ->where('no_reqskm', $no_req)
                ->where('status', 0)
                ->get();
            return DataTables::of($data_mintas)->make(true);
        }


        return view('pages.pengiriman-gu.create', compact('user',  'no_reqskm', 'no_req', 'gudang_id'));
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $no_krmskm = 'SJ/GU' . '/' . date('y/m/' . str_pad(TrKrmSKM::count() + 1, 3, '0', STR_PAD_LEFT));

        $gudang_id = $request->gudang_id;

        $no_reqskm = $request->no_reqskm;

        TrKrmSKM::create([
            'no_krmskm' => $no_krmskm,
            'tgl_krm' => $request->tgl_krm,
            'gudang_id' => $gudang_id,
        ]);

        foreach ($request->items as $item) {
            TrKrmSKMDetail::create([
                'no_krmskm' => $no_krmskm,
                'no_reqskm' => $no_reqskm,
                'brg_id' => $item['brg_id'],
                'qty' => $item['qty'],
                'satuan_besar' => $item['satuan_besar'],
            ]);

            $lastStok = TrStok::where('gudang_id', $gudang_id)
                ->where('brg_id', $item['brg_id'])
                ->orderBy('stok_id', 'desc')
                ->first();

            $awal = $lastStok ? $lastStok->akhir : 0;
            $keluar = $item['qty'];
            $akhir = $awal - $keluar;

            $id = str_pad(TrStok::count() + 1, 3, '0', STR_PAD_LEFT);
            $stok_id = "{$gudang_id}/{$item['brg_id']}/{$id}";

            TrStok::create([
                'stok_id' => $stok_id,
                'tgl' => $request->tgl_krm,
                'brg_id' => $item['brg_id'],
                'gudang_id' => $gudang_id,
                'doc_id' => $no_krmskm,
                'awal' => $awal,
                'masuk' => 0,
                'keluar' => $keluar,
                'akhir' => $akhir,
            ]);

        }

        $permintaan = TrReqSKM::where('status', 0)->first();
        if ($permintaan) {
            $permintaan->update([
                'status' => 1,
            ]);
        }

        TrReqSKMDetail::where('status', 0)->update([
            'status' => 1,
        ]);

        return redirect()->route('pengiriman-gudang-utama')->with('success', 'Data pengiriman berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        //
    }

    public function detailKRM()
    {
        return view('pages.pengiriman-gu.detail');
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
