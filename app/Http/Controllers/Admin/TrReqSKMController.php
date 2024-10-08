<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Barang;
use App\Models\Admin\Gudang;
use App\Models\Admin\TrKrmSKM;
use App\Models\Admin\TrKrmSKMDetail;
use App\Models\Admin\TrReqSKM;
use App\Models\Admin\TrReqSKMDetail;
use App\Models\Admin\TrStok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
                    $html = '<a href="'.route($path.'edit', ['no_reqskm' => $no]).'" class="btn btn-success waves-effect waves-light mx-1">'
                        .' <i class="bx bx-edit align-middle me-2 font-size-18"></i> Edit</a>';
                    $html .= '<button class="btn btn-secondary waves-effect waves-light btn-detail me-2" data-bs-toggle="modal" data-bs-target="#detailModal">'
                        .'<i class="bx bx-detail font-size-18 align-middle me-2"></i>Detail</button>';

                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('pages.permintaan-skm.index', compact('user'));
    }

    /**
     *  the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $user = $this->userAuth();
        $no_reqskm = 'FPB/SKM'.'/'.date('y/m/'.str_pad(TrReqSKM::count() + 1, 3, '0', STR_PAD_LEFT));
        $gudang_id = Gudang::where('gudang_id', 'GU001')->value('gudang_id');

        if ($request->ajax()) {
            $type = $request->input('type');

            if ($type == 'barangs') {
                $barangs = Barang::where('status', 0)
                    ->where('brg_id', 'LIKE', 'BPR%')
                    ->orderBy('brg_id', 'asc')
                    ->get();

                return DataTables::of($barangs)->make(true);

            } elseif ($type == 'speks') {
                $speks = DB::table('m_brg_spek as a')
                    ->join('m_brg as b', 'a.brg_id', '=', 'b.brg_id')
                    ->select('b.brg_id', 'b.nm_brg', 'a.satuan1', 'a.satuan2', 'a.konversi1', 'a.spek_id', 'a.spek')
                    ->where('a.brg_id', $request->brg_id)
                    ->where('a.status', 0)
                    ->get();

                return DataTables::of($speks)->make(true);
            }
        }

        return view('pages.permintaan-skm.create', compact('user','no_reqskm', 'gudang_id'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $no_reqskm = $request->no_reqskm;
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
                'spek_id' => $item['spek_id'],
                'qty_beli' => $item['qty_beli'],
                'satuan_beli' => $item['satuan_beli'],
                'qty_std' => $item['qty_std'],
                'satuan_std' => $item['satuan_std'],
                'ket' => $item['ket'],
            ]);
        }

        return redirect()->route('permintaan-skm')->with('success', 'Data permintaan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function detail(Request $request)
    {
        $details = DB::table('tr_reqskm as a')
            ->join('tr_reqskm_detail as b', 'a.no_reqskm', '=', 'b.no_reqskm')
            ->join('m_brg as c', 'b.brg_id', '=', 'c.brg_id')
            ->join('m_brg_spek as d', 'b.spek_id', '=', 'd.spek_id')
            ->where('a.no_reqskm', $request->no_reqskm)
            ->select('c.nm_brg', 'd.spek', 'b.qty_beli', 'b.satuan_beli')
            ->get();

        return DataTables::of($details)->make(true);
    }

    /**
     *  the form for editing the specified resource.
     */
    public function edit(Request $request, string $no_reqskm)
    {
        $user = $this->userAuth();
        $no_req = str_replace('-', '/', $no_reqskm);

        $tgl = TrReqSKM::where('no_reqskm', $no_req)
            ->where('status', 0)
            ->value('tgl');

        if ($request->ajax()) {
            $type = $request->input('type');

            if ($type == 'details') {
                $details = DB::table('tr_reqskm_detail as a')
                    ->join('m_brg as b', 'a.brg_id', '=', 'b.brg_id')
                    ->join('m_brg_spek as c', 'a.spek_id', '=', 'c.spek_id')
                    ->select('b.brg_id', 'b.nm_brg as nama', 'a.qty_beli', 'a.satuan_beli', 'a.qty_std', 'a.satuan_std', 'c.konversi1')
                    ->where('no_reqskm', $no_req)
                    ->where('a.status', 0)
                    ->get();

                return DataTables::of($details)->make(true);

            } elseif ($type == 'barangs') {
                $barangs = Barang::where('status', 0)
                    ->orderBy('brg_id', 'asc')
                    ->get();

                return DataTables::of($barangs)->make(true);

            }
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
                $data_tr_krmskm_detail = TrReqSKMDetail::where('no_reqskm', operator: $no_req)
                    ->where('brg_id', $item['brg_id'])
                    ->first();

                if ($data_tr_krmskm_detail) {
                    $nama = Barang::where('brg_id', $item['brg_id'])->value('nm_brg');
                    $data_tr_krmskm_detail->update([
                        'qty_beli' => $item['qty_beli'],
                        'qty_std' => $item['qty_std'],
                    ]);
                    $responseMessage = 'Data '.$nama.' berhasil diubah. Menjadi Qty : '.$item['qty_beli'];
                }
            }
        } else {
            $data_tr_krmskm = TrReqSKM::where('no_krmskm', $no_req)->first();
            $data_tr_krmskm->update([
                'tgl' => $request->tgl,
            ]);
            $responseMessage = 'Data transaksi berhasil diubah.';
        }

        return response()->json(['success' => true, 'message' => $responseMessage], 200);
    }

    public function indexHistory(Request $request)
    {
        $user = $this->userAuth();

        if ($request->ajax()) {
            $permintaans = TrReqSKM::where('status', 1)
                ->orderBy('no_reqskm', 'desc')
                ->get();

            return DataTables::of($permintaans)
                ->addColumn('action', function () {
                    return '<button class="btn btn-secondary waves-effect waves-light btn-detail me-2" data-bs-toggle="modal" data-bs-target="#detailModal">'
                        .'<i class="bx bx-detail font-size-18 align-middle me-2"></i>Detail</button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('pages.history.permintaan-skm', compact('user'));
    }

    public function detailHistory(Request $request)
    {
        $details = DB::table('tr_reqskm as a')
            ->join('tr_reqskm_detail as b', 'a.no_reqskm', '=', 'b.no_reqskm')
            ->join('m_brg as c', 'b.brg_id', '=', 'c.brg_id')
            ->join('m_brg_spek as d', 'a.spek_id', '=', 'd.spek_id')
            ->where('a.no_reqskm', $request->no_reqskm)
            ->where('a.status', 1)
            ->select('c.nm_brg', 'd.spek', 'b.qty_beli', 'b.satuan_beli')
            ->get();

        return DataTables::of($details)->make(true);
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
                    if (is_null($object->tgl_trm)) {
                        return '<a href="'.route($path.'create', ['no_krmskm' => $no]).'" class="btn btn-info waves-effect waves-light mx-1">'
                            .'<i class="bx bx-transfer-alt align-middle me-2 font-size-18"></i> Proses</a>';
                    } else {
                        $detailButton = '<button class="btn btn-secondary waves-effect waves-light btn-detail me-2" data-bs-toggle="modal" data-bs-target="#detailModal">'
                            .'<i class="bx bx-detail font-size-18 align-middle me-2"></i> Detail</button>';

                        return $detailButton;
                    }
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

        if ($request->ajax()) {
            $barang_krms = DB::table('tr_krmskm_detail as a')
                ->join('tr_reqskm_detail as b', function ($join) {
                    $join->on('a.no_reqskm', '=', 'b.no_reqskm')
                        ->on('a.brg_id', '=', 'b.brg_id');
                })
                ->join('m_brg_spek as c', 'a.spek_id', '=', 'c.spek_id')
                ->join('m_brg as d', 'c.brg_id', '=', 'd.brg_id')
                ->select(
                    'a.brg_id',
                    'd.nm_brg',
                    'b.qty_beli as qty_minta',
                    'a.qty_beli as qty_kirim',
                    'a.satuan_beli'
                )
                ->where('a.no_krmskm', $no_krm)
                ->where('a.status', 0)
                ->groupBy('a.brg_id', 'd.nm_brg', 'a.satuan_beli', 'b.qty_beli', 'a.qty_beli')
                ->get();

            return DataTables::of($barang_krms)
                ->addColumn('action', function ($object) {
                    return '<div class="d-flex form-check font-size-18">
                        <input type="checkbox" class="form-check-input check-barang" value="'.$object->brg_id.'"></div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('pages.terima-barang-skm.create', compact('user', 'no_krmskm', 'no_krm', 'no_req'));
    }

    public function storeTerima(Request $request)
    {

        $no_krmskms = $request->no_krmskm;
        $penerima = $request->user_id;
        $check_barang = $request->brg_id;
        $gudang_id = TrKrmSKM::where('no_krmskm', $no_krmskms)->value('gudang_id');

        $qty = TrKrmSKMDetail::where('no_krmskm', $no_krmskms)
            ->where('brg_id', $check_barang)
            ->value('qty_beli');

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

            $id = str_pad(TrStok::count() + 1, 3, '0', STR_PAD_LEFT);
            $stok_id = "{$gudang_id}/{$barangId}/{$id}";
            $masuk = $qty;
            $gudangs = Gudang::where('gudang_id', $gudang_id)->value('nama');
            $ket = 'Penerimaan barang dari '.$gudangs;

            TrStok::create([
                'stok_id' => $stok_id,
                'tgl' => $request->tgl_trm,
                'brg_id' => $barangId,
                'gudang_id' => $gudang_id,
                'doc_id' => $no_krmskms,
                'ket' => $ket,
                'awal' => 0,
                'masuk' => $masuk,
                'keluar' => 0,
                'akhir' => 0,
                'cek' => 1,
            ]);
        }

        $pengirimans = TrKrmSKM::where('status', 0)->first();
        if ($pengirimans) {
            $pengirimans->update([
                'status' => 1,
            ]);
        }

        TrKrmSKMDetail::where('status', 0)->update([
            'status' => 1,
        ]);

        return redirect()->route('penerimaan-barang')->with('success', 'Data penerimaan barang berhasil ditambahkan.');
    }

    public function terimaDetail(Request $request)
    {
        $details = DB::table('tr_krmskm as a')
            ->join('tr_krmskm_detail as b', 'a.no_krmskm', '=', 'b.no_krmskm')
            ->join('m_brg as c', 'b.brg_id', '=', 'c.brg_id')
            ->join('m_brg_spek as d', 'b.spek_id', '=', 'd.spek_id')
            ->where('a.no_krmskm', $request->no_krmskm)
            ->where('b.diterima', 0)
            ->select('c.nm_brg', 'd.spek', 'b.qty_beli', 'b.satuan_beli')
            ->get();

        return DataTables::of($details)->make(true);
    }

    public function indexTerimaHistory(Request $request)
    {
        $user = $this->userAuth();

        if ($request->ajax()) {
            $penerimaans = TrKrmSKM::where('status', 1)
                ->orderBy('no_krmskm', 'desc')
                ->get();

            return DataTables::of($penerimaans)
                ->addColumn('action', function () {
                    return '<button class="btn btn-secondary waves-effect waves-light btn-detail me-2" data-bs-toggle="modal" data-bs-target="#detailModal">'
                        .'<i class="bx bx-detail font-size-18 align-middle me-2"></i>Detail</button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('pages.history.penerimaan-barang', compact('user'));
    }

    public function detailTerimaHistory(Request $request)
    {
        $details = DB::table('tr_krmskm as a')
            ->join('tr_krmskm_detail as b', 'a.no_krmskm', '=', 'b.no_krmskm')
            ->join('m_brg as c', 'b.brg_id', '=', 'c.brg_id')
            ->join('m_brg_spek as d', 'b.spek_id', '=', 'd.spek_id')
            ->where('a.no_krmskm', $request->no_krmskm)
            ->where('a.status', 1)
            ->select('c.nm_brg', 'd.spek', 'b.qty_beli', 'b.satuan_beli')
            ->get();

        return DataTables::of($details)->make(true);
    }
}
