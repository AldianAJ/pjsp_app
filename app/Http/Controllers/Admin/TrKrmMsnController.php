<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Barang;
use App\Models\Admin\TargetMesin;
use App\Models\Admin\TrKrmMsn;
use App\Models\Admin\TrKrmMsnDetail;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class TrKrmMsnController extends Controller
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

        if ($request->ajax()) {
            $type = $request->input('type');

            if ($type == 'barangs') {

                $barangs = Barang::where('status', 0)
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
    public function showDetail(Request $request)
    {
        $details = DB::table('tr_reqskm as a')
            ->join('tr_reqskm_detail as b', 'a.no_reqskm', '=', 'b.no_reqskm')
            ->join('m_brg as c', 'b.brg_id', '=', 'c.brg_id')
            ->where('a.no_reqskm', $request->no_reqskm)
            ->select('c.nm_brg', 'b.qty_beli', 'b.satuan_beli')
            ->get();

        return DataTables::of($details)->make(true);
    }
}
