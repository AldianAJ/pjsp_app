<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Barang;
use App\Models\Admin\TargetMesin;
use App\Models\Admin\TrKrmMsn;
use App\Models\Admin\TrKrmMsnDetail;
use App\Models\Admin\TrStok;
use Illuminate\Support\Facades\DB;
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
        $path = 'pengiriman-skm.';

        if ($request->ajax()) {
            $pengirimans = TrKrmMsn::where('status', 0)
                ->orderBy('no_krmmsn', 'desc')
                ->get();

            return DataTables::of($pengirimans)
                ->addColumn('action', function ($object) use ($path) {
                    $no = str_replace('/', '-', $object->no_krmmsn);
                    $html = '<a href="' . route($path . "edit", ["no_krmmsn" => $no]) . '" class="btn btn-success waves-effect waves-light mx-1">'
                        . ' <i class="bx bx-edit align-middle me-2 font-size-18"></i> Edit</a>';
                    $html .= '<button class="btn btn-secondary waves-effect waves-light btn-detail me-2" data-bs-toggle="modal" data-bs-target="#detailModal">'
                        . '<i class="bx bx-detail font-size-18 align-middle me-2"></i>Detail</button>';
                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('pages.pengiriman-skm.index', compact('user'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $user = $this->userAuth();

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

            } elseif ($type == 'shifts') {
                $date = $request->input('date');
                $harian = DB::table('tr_target_harian')->where('tgl', $date)->first();

                $shifts = DB::table('tr_target_shift')
                    ->select('shift_id', 'shift')
                    ->where('harian_id', $harian->harian_id)->get();

                return DataTables::of($shifts)->make(true);

            } elseif ($type == 'machines') {
                $shiftId = $request->input('shift_id');
                $machines = DB::table('tr_target_mesin as a')
                    ->join('tr_target_shift as b', 'a.shift_id', '=', 'b.shift_id')
                    ->join('m_mesin as c', 'a.mesin_id', '=', 'c.mesin_id')
                    ->select('a.msn_trgt_id', 'c.nama')
                    ->where('a.shift_id', $shiftId)->get();

                return DataTables::of($machines)->make(true);
            }
        }
        return view('pages.pengiriman-skm.create', compact('user'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $no_krmmsn = 'TBI/SKM' . '/' . date('y/m/' . str_pad(TrKrmMsn::count() + 1, 3, '0', STR_PAD_LEFT));
        $msn_trgt_id = $request->msn_trgt_id;

        TrKrmMsn::create([
            'no_krmmsn' => $no_krmmsn,
            'msn_trgt_id' => $msn_trgt_id,
            'tgl' => $request->tgl,
        ]);

        foreach ($request->items as $item) {
            TrKrmMsnDetail::create([
                'no_krmmsn' => $no_krmmsn,
                'brg_id' => $item['brg_id'],
                'spek_id' => $item['spek_id'],
                'qty_beli' => $item['qty_beli'],
                'satuan_beli' => $item['satuan_beli'],
                'qty_std' => $item['qty_std'],
                'satuan_std' => $item['satuan_std'],
                'ket' => $item['ket'],
            ]);

            $id = str_pad(TrStok::count() + 1, 3, '0', STR_PAD_LEFT);
            $stok_id = "{$gudang_id}/{$item['brg_id']}/{$id}";
            $keluar = $item['qty_beli'];
            $gudangs = Gudang::where('gudang_id', $gudang_id)->value('nama');
            $ket = "Pengiriman barang dari " . $gudangs;

            TrStok::create([
                'stok_id' => $stok_id,
                'tgl' => $request->tgl_krm,
                'brg_id' => $item['brg_id'],
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

        return redirect()->route('pengiriman-skm')->with('success', 'Data permintaan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function showDetail(Request $request)
    {
        $details = DB::table('tr_reqskm as a')
            ->join('tr_reqskm_detail as b', 'a.no_krmmsn', '=', 'b.no_krmmsn')
            ->join('m_brg as c', 'b.brg_id', '=', 'c.brg_id')
            ->where('a.no_krmmsn', $request->no_krmmsn)
            ->select('c.nm_brg', 'b.qty_beli', 'b.satuan_beli')
            ->get();

        return DataTables::of($details)->make(true);
    }
}
