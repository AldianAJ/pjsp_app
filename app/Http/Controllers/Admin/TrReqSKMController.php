<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\user;
use App\Models\Admin\Barang;
use App\Models\Admin\TrStok;
use App\Models\Admin\TrReqSKM;
use App\Models\Admin\TrReqSKMDetail;
use App\Models\Admin\Gudang;
use App\Models\Admin\TrKrmSKM;
use App\Models\Admin\TrKrmSKMDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class TrReqSKMController extends Controller
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
        $path = 'permintaan-skm.';

        if ($request->ajax()) {
            $permintaans = TrReqSKM::where('status', 0)
                ->orderBy('no_reqskm', 'desc')
                ->get();

            return DataTables::of($permintaans)
                ->addColumn('action', function ($object) use ($path) {
                    $no = str_replace('/', '-', $object->no_reqskm);
                    $html = '<a href="' . route($path . "edit", ["no_reqskm" => $no]) . '" class="btn btn-success waves-effect waves-light mx-1">'
                        . ' <i class="bx bx-edit align-middle me-2 font-size-18"></i> Edit</a>';
                    $html .= '<button class="btn btn-secondary waves-effect waves-light btn-detail me-2" data-bs-toggle="modal" data-bs-target="#detailModal">'
                        . '<i class="bx bx-detail font-size-18 align-middle me-2"></i>Detail</button>';
                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('pages.permintaan-skm.index', compact('user'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $user = $this->userAuth();
        $gudang_id = Gudang::where('jenis', 2)->value('gudang_id');
        $path = 'permintaan-skm.create.';
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
        return view('pages.permintaan-skm.create', compact('user', 'gudang_id'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $no_reqskm = 'FPB/SKM' . '/' . date('y/m/' . str_pad(TrReqSKM::count() + 1, 3, '0', STR_PAD_LEFT));

        $gudang_id = $request->gudang_id;

        TrReqSKM::create([
            'no_reqskm' => $no_reqskm,
            'tgl' => $request->tgl,
            'gudang_id' => $gudang_id,

        ]);

        foreach ($request->items as $item) {
            TrReqSKMDetail::create([
                'no_reqskm' => $no_reqskm,
                'brg_id' => $item['brg_id'],
                'qty' => $item['qty'],
                'satuan_besar' => $item['satuan_besar'],
            ]);
        }

        return redirect()->route('permintaan-skm')->with('success', 'Data permintaan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function showDetail(Request $request)
    {
        $details = DB::table('tr_reqskm as a')
            ->join('tr_reqskm_detail as b', 'a.no_reqskm', '=', 'b.no_reqskm')
            ->join('m_brg as c', 'b.brg_id', '=', 'c.brg_id')
            ->where('a.no_reqskm', $request->no_reqskm)
            ->select('c.nm_brg', 'b.qty', 'b.satuan_besar')
            ->get();

        return DataTables::of($details)->make(true);
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, string $no_reqskm)
    {
        $user = $this->userAuth();
        $no_req = str_replace('-', '/', $no_reqskm);

        $tgl = TrReqSKM::where('no_reqskm', $no_req)
            ->where('status', 0)
            ->value('tgl');

        if ($request->ajax()) {
            $data_mintas = TrReqSKMDetail::with('barang')
                ->where('no_reqskm', $no_req)
                ->where('status', 0)
                ->get();
            return DataTables::of($data_mintas)->make(true);
        }


        return view('pages.permintaan-skm.edit', compact('user', 'tgl', 'no_reqskm', 'no_req'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $no_reqskm)
    {
        $no_req = str_replace('-', '/', $no_reqskm);
        $responseMessage = '';

        if (!empty($request->items)) {
            foreach ($request->items as $item) {
                $data_tr_reqskm_detail = TrReqSKMDetail::where('no_reqskm', operator: $no_req)
                    ->where('brg_id', $item['brg_id'])
                    ->first();

                if ($data_tr_reqskm_detail) {
                    $nama = Barang::where('brg_id', $item['brg_id'])->value('nm_brg');
                    $data_tr_reqskm_detail->update(['qty' => $item['qty']]);
                    $responseMessage = 'Data ' . $nama . ' berhasil diubah. Menjadi Qty : ' . $item['qty'];
                }
            }
        } else {
            $data_tr_reqskm = TrReqSKM::where('no_reqskm', $no_req)->first();
            $data_tr_reqskm->update([
                'tgl' => $request->tgl,
            ]);
            $responseMessage = 'Data transaksi berhasil diubah.';
        }
        return response()->json(['success' => true, 'message' => $responseMessage], 200);
    }

    public function indexHistory(Request $request)
    {
        $user = $this->userAuth();
        $path = 'permintaan-skm.history.';

        if ($request->ajax()) {
            $permintaans = TrReqSKM::where('status', 0)->get();
            return DataTables::of($permintaans)
                ->addColumn('action', function ($object) use ($path) {
                    $no = str_replace('/', '-', $object->no_reqskm);
                    $html = '<a href="' . route($path . "edit", ["no_reqskm" => $no]) . '" class="btn btn-secondary waves-effect waves-light mx-1">'
                        . ' <i class="bx bx-edit align-middle me-2 font-size-18"></i> Edit</a>';
                    $html .= '<button class="btn btn-primary waves-effect waves-light mx-1 btn-detail" data-no_reqskm="' . $object->no_reqskm . '">' .
                        '<i class="bx bx-show align-middle font-size-18"></i> Detail</button>';
                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('pages.permintaan-skm.index', compact('user'));
    }

    public function indexTerima(Request $request)
    {
        $user = $this->userAuth();
        $path = 'penerimaan-barang.';

        if ($request->ajax()) {
            $pengirimans = TrKrmSKM::where('status', 0)
                ->orderBy('no_krmskm', 'desc')
                ->get();

            return DataTables::of($pengirimans)
                ->addColumn('action', function ($object) use ($path) {
                    $no = str_replace('/', '-', $object->no_krmskm);
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

        $no_req = TrKrmSKMDetail::where('no_krmskm', $no_krm)->value('no_reqskm');

        $user_id = User::where('role', 'skm')->value('user_id');

        $path = 'penerimaan-barang.create.';

        if ($request->ajax()) {
            $type = $request->input('type');

            if ($type == 'data_reqs') {

                $data_reqs = DB::table('tr_krmskm_detail as a')
                    ->join('tr_reqskm_detail as b', 'a.no_reqskm', '=', 'b.no_reqskm')
                    ->join('m_brg as c', 'b.brg_id', '=', 'c.brg_id')
                    ->select('c.nm_brg', 'b.qty', 'b.satuan_besar')
                    ->where('b.status', 1)
                    ->where('no_krmskm', $no_krm)
                    ->distinct()
                    ->get();

                return DataTables::of($data_reqs)->make(true);
            } elseif ($type == 'data_krms') {

                $data_krms = TrKrmSKMDetail::with('barang')
                    ->where('no_krmskm', $no_krm)
                    ->where('status', 0)
                    ->get();

                return DataTables::of($data_krms)->make(true);
            } elseif ($type == 'barang_krms') {

                $barang_krms = TrKrmSKMDetail::with('barang')
                    ->where('no_krmskm', $no_krm)
                    ->where('status', 0)
                    ->get();

                return DataTables::of($barang_krms)
                    ->addColumn('action', function ($object) {
                        if (is_null($object->tgl_trm)) {
                            return '<div class="d-flex form-check font-size-18">
                        <input type="checkbox" class="form-check-input check-barang" value="' . $object->brg_id . '"></div>';
                        } else {
                        }
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            } else {
                return response()->json(['error' => 'Invalid type parameter'], 400);
            }
        }

        return view('pages.terima-barang-skm.create', compact('user', 'no_krmskm', 'no_krm', 'no_req', 'user_id'));
    }

    public function storeTerima(Request $request)
    {

        $no_krmskms = $request->no_krmskm;
        $penerima = $request->user_id;
        $check_barang = $request->brg_id;
        $gudang_id = TrKrmSKM::where('gudang_id')->first();
        $qty = TrKrmSKM::where();

        TrKrmSKM::where('no_krmskm', $no_krmskms)
            ->update([
                'tgl_trm' => $request->tgl_trm,
                'penerima' => $penerima,
            ]);

        foreach ($check_barang as $barangId) {
            $detailPengiriman = TrKrmSKMDetail::where('no_krmskm', $no_krmskms)
                ->where('brg_id', $barangId)
                ->first();

            if ($detailPengiriman) {
                $detailPengiriman->update([
                    'diterima' => 0,
                ]);
            }

            $lastStok = TrStok::where('gudang_id', $gudang_id)
                ->where('brg_id', $barangId)
                ->orderBy('stok_id', 'desc')
                ->first();

            $akhir = ($awal = ($lastStok ? $lastStok->akhir : 0)) + ($masuk = $qty);

            $id = str_pad(TrStok::count() + 1, 3, '0', STR_PAD_LEFT);
            $stok_id = "{$gudang_id}/{$barangId}/{$id}";

            TrStok::create([
                'stok_id' => $stok_id,
                'tgl' => $request->tgl_trm,
                'brg_id' => $barangId,
                'gudang_id' => $gudang_id,
                'doc_id' => $no_krmskms,
                'awal' => $awal,
                'masuk' => $masuk,
                'keluar' => 0,
                'akhir' => $akhir,
            ]);
        }

        return redirect()->route('penerimaan-barang')
            ->with('success', 'Data penerimaan barang berhasil ditambahkan.');
    }
}
