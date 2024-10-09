<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Barang;
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
            ->select('a.no_reqskm as id', 'a.tgl as tgl_minta', 'c.tgl_krm', 'c.no_krmskm')
            ->where('a.status', 0)
            ->distinct()
            ->orderBy('a.no_reqskm', 'desc')
            ->get();

        $pengirimans = DB::table('tr_krmskm as a')
            ->leftJoin('tr_krmskm_detail as b', 'a.no_krmskm', '=', 'b.no_krmskm')
            ->leftJoin('tr_reqskm as c', 'b.no_reqskm', '=', 'c.no_reqskm')
            ->select('a.no_krmskm', 'c.tgl as tgl_minta', 'a.tgl_krm')
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
                    $no = str_replace('/', '-', isset($object->no_krmskm) ? $object->no_krmskm : $object->id);
                    if (is_null($object->tgl_krm)) {
                        return '<a href="' . route($path . "create", ["no_reqskm" => $no]) . '" class="btn btn-info waves-effect waves-light mx-1">'
                            . '<i class="bx bx-transfer-alt align-middle me-2 font-size-18"></i> Proses</a>';
                    } else {
                        $editButton = '<a href="' . route($path . "edit", ["no_krmskm" => $no]) . '" class="btn btn-success waves-effect waves-light mx-1">'
                            . '<i class="bx bx-edit align-middle me-2 font-size-18"></i> Edit</a>';

                        $detailButton = '<button class="btn btn-secondary waves-effect waves-light btn-detail me-2" data-bs-toggle="modal" data-bs-target="#detailModal">'
                            . '<i class="bx bx-detail font-size-18 align-middle me-2"></i> Detail</button>';

                        return $editButton . ' ' . $detailButton;
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
        $no_krmskm = 'SJ/GU' . '/' . date('y/m/' . str_pad(TrKrmSKM::count() + 1, 3, '0', STR_PAD_LEFT));
        $gudang_id = Gudang::where('gudang_id', 'GU001')->value('gudang_id');

        if ($request->ajax()) {
            $type = $request->input('type');

            if ($type == 'details') {
                $details = DB::table('tr_reqskm_detail as a')
                    ->join('m_brg as b', 'a.brg_id', '=', 'b.brg_id')
                    ->join('m_brg_spek as c', 'a.spek_id', '=', 'c.spek_id')
                    ->select('b.brg_id', 'b.nm_brg', 'a.qty_beli', 'a.qty_std', 'a.satuan_beli', 'c.satuan1', 'c.satuan2', 'c.konversi1', 'c.spek_id', 'c.spek')
                    ->where('a.no_reqskm', $no_req)
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
        return view('pages.pengiriman-gu.create', compact('user','no_krmskm', 'no_reqskm', 'no_req', 'gudang_id'));
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $no_krmskm = $request->no_krmskm;
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
                'spek_id' => $item['spek_id'],
                'qty_beli' => $item['qty_beli'],
                'satuan_beli' => $item['satuan_beli'],
                'qty_std' => $item['qty_std'],
                'satuan_std' => $item['satuan_std'],
                'ket' => $item['ket'],
            ]);

            $id = str_pad(TrStok::count() + 1, 3, '0', STR_PAD_LEFT);
            $stok_id = "{$gudang_id}/{$item['spek_id']}/{$id}";
            $keluar = $item['qty_beli'];
            $gudangs = Gudang::where('gudang_id', $gudang_id)->value('nama');
            $ket = "Pengiriman barang dari " . $gudangs;

            TrStok::create([
                'stok_id' => $stok_id,
                'tgl' => $request->tgl_krm,
                'spek_id' => $item['spek_id'],
                'gudang_id' => $gudang_id,
                'doc_id' => $no_krmskm,
                'ket' => $ket,
                'awal' => 0,
                'masuk' => 0,
                'keluar' => $keluar,
                'akhir' => 0,
                'cek' => 1,
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
    public function detail(Request $request)
    {
        $details = DB::table('tr_krmskm as a')
            ->join('tr_krmskm_detail as b', 'a.no_krmskm', '=', 'b.no_krmskm')
            ->join('m_brg as c', 'b.brg_id', '=', 'c.brg_id')
            ->join('m_brg_spek as d', 'b.spek_id', '=', 'd.spek_id')
            ->where('a.no_krmskm', $request->no_krmskm)
            ->select('c.nm_brg', 'd.spek', 'b.qty_beli', 'b.satuan_beli')
            ->get();

        return DataTables::of($details)->make(true);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, string $no_krmskm)
    {
        $user = $this->userAuth();
        $no_krm = str_replace('-', '/', $no_krmskm);

        $no_req = TrKrmSKMDetail::where('no_krmskm', $no_krm)
            ->where('status', 0)
            ->value('no_reqskm');

        $tgl_krm = TrKrmSKM::where('no_krmskm', $no_krm)
            ->where('status', 0)
            ->value('tgl_krm');

        if ($request->ajax()) {
            $type = $request->input('type');

            if ($type == 'details') {
                $details = DB::table('tr_krmskm_detail as a')
                    ->join('m_brg as b', 'a.brg_id', '=', 'b.brg_id')
                    ->join('m_brg_spek as c', 'a.spek_id', '=', 'c.spek_id')
                    ->select('b.brg_id', 'b.nm_brg as nama', 'a.qty_beli', 'a.satuan_beli', 'a.qty_std', 'a.satuan_std', 'c.konversi1')
                    ->where('no_krmskm', $no_krm)
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

        return view('pages.pengiriman-gu.edit', compact('user', 'tgl_krm', 'no_krmskm', 'no_krm', 'no_req'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $no_krmskm)
    {
        $no_krm = str_replace('-', '/', $no_krmskm);
        $responseMessage = '';
        $data_tr_stok = TrStok::where('doc_id', $no_krm)->first();

        if (!empty($request->items)) {
            foreach ($request->items as $item) {
                $data_tr_krmskm_detail = TrKrmskmDetail::where('no_krmskm', operator: $no_krm)
                    ->where('brg_id', $item['brg_id'])
                    ->first();

                if ($data_tr_krmskm_detail) {
                    $nama = Barang::where('brg_id', $item['brg_id'])->value('nm_brg');
                    $data_tr_krmskm_detail->update([
                        'qty_beli' => $item['qty_beli'],
                        'qty_std' => $item['qty_std']
                    ]);

                    $keluar = $item['qty_beli'];
                    $data_tr_stok->update([
                        'keluar' => $keluar,
                        'cek' => 1,
                    ]);

                    $responseMessage = 'Data ' . $nama . ' berhasil diubah. Menjadi Qty : ' . $item['qty_beli'];
                }
            }
        } else {
            $data_tr_krmskm = TrKrmskm::where('no_krmskm', $no_krm)->first();
            $data_tr_krmskm->update([
                'tgl_krm' => $request->tgl_krm,
            ]);
            $responseMessage = 'Data transaksi berhasil diubah.';
        }
        return response()->json(['success' => true, 'message' => $responseMessage], 200);
    }

    public function indexHistory(Request $request)
    {
        $user = $this->userAuth();

        if ($request->ajax()) {
            $pengirimans = TrKrmSKM::where('status', 1)
                ->orderBy('no_krmskm', 'desc')
                ->get();

            return DataTables::of($pengirimans)
                ->addColumn('action', function () {
                    return '<button class="btn btn-secondary waves-effect waves-light btn-detail me-2" data-bs-toggle="modal" data-bs-target="#detailModal">'
                        . '<i class="bx bx-detail font-size-18 align-middle me-2"></i>Detail</button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('pages.history.proses-permintaan', compact('user'));
    }


    public function detailHistory(Request $request)
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
