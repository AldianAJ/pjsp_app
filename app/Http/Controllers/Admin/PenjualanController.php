<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\user;
use App\Models\Admin\Barang;
use App\Models\Admin\TrSJ;
use App\Models\Admin\TrSJDetail;
use App\Models\Admin\Cust;
use App\Models\Admin\Armada;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class PenjualanController extends Controller
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
        $path = 'penjualan.';

        if ($request->ajax()) {
            $no_sjs = DB::table('tr_sj as a')
                ->join('tr_hms_po as b', 'a.no_po', '=', 'b.no_po')
                ->join('tr_hms_po_detail as c', 'b.no_po', '=', 'c.no_po')
                ->join('m_armada as d', 'a.no_pol', '=', 'd.no_pol')
                ->select('a.no_sj', 'a.tgl', 'b.no_po', 'a.no_segel', 'd.no_pol', 'a.driver')
                ->where('b.status', 0)
                ->where('a.status', 0)
                ->orderBy('no_sj', 'desc')
                ->get();

            return DataTables::of($no_sjs)
                ->addColumn('action', function ($object) use ($path) {
                    $no = str_replace('/', '-', $object->no_sj);
                    $html = '<a href="' . route($path . "edit", ["no_sj" => $no]) . '" class="btn btn-success waves-effect waves-light mx-1">'
                        . ' <i class="bx bx-edit align-middle me-2 font-size-18"></i> Edit</a>';
                    $html .= '<button class="btn btn-secondary waves-effect waves-light btn-detail me-2" data-bs-toggle="modal" data-bs-target="#detailModal">'
                        . '<i class="bx bx-detail font-size-18 align-middle me-2"></i>Detail</button>';
                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('pages.penjualan.index', compact('user'));
    }

    /**
     *  the form for creating a new resource.
     */
    public function create(Request $request)
{
    $user = $this->userAuth();

    $hms_poo = DB::table('tr_hms_po as a')
        ->join('tr_hms_po_detail as b', 'a.no_po', '=', 'b.no_po')
        ->where('a.status', 0)
        ->get();

    $cust_id = Cust::where('cust_id', 'CS0001')->value('cust_id');

    $armadas = Armada::where('status', 0)->get();

    if ($request->ajax()) {
        $no_po = $request->get('no_po');
        $speks = DB::table('tr_hms_po as a')
            ->join('tr_hms_po_detail as b', 'a.no_po', '=', 'b.no_po')
            ->join('m_brg_spek as c', 'b.spek_id', '=', 'c.spek_id')
            ->select('c.spek_id','c.spek', 'b.qty_po', 'b.qty_krm', 'b.selisih_krm', 'b.satuan_po')
            ->where('a.no_po', $no_po)
            ->where('a.status', 0)
            ->get();

        return DataTables::of($speks)->make(true);
    }

    return view('pages.penjualan.create', compact('user', 'hms_poo', 'armadas', 'cust_id'));
}


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $no_sj = 'SJ/RJW-HMS' . '/' . date('y/m/' . str_pad(TrSJ::count() + 1, 4, '0', STR_PAD_LEFT));

    $items = json_decode($request->items, true);

    $trSj = TrSJ::create([
        'no_sj' => $no_sj,
        'tgl' => $request->tgl,
        'cust_id' => $request->cust_id,
        'no_po' => $request->no_po,
        'no_segel' => $request->no_segel,
        'no_pol' => $request->no_pol,
        'driver' => $request->driver,
    ]);

    // Create each detail item
    foreach ($items as $item) {
        TrSJDetail::create([
            'no_sj' => $trSj->no_sj,
            'spek_id' => $item['spek_id'],
            'qty_krm' => $item['qty_krm'],
            'qty_ball' => 0,
            'qty_slop' => 0,
            'qty_pack' => 0,
            'total' => 0,
            'no_batch' => $item['no_batch'],
            'ket' => $item['ket'] ?? $request->ket,
        ]);
    }

    return redirect()->route('penjualan')->with('success', 'Data surat jalan berhasil ditambahkan.');
}

    /**
     * Display the specified resource.
     */
    public function detail(Request $request)
    {
        $details = DB::table('tr_mutasi as a')
            ->join('tr_mutasi_detail as b', 'a.no_sj', '=', 'b.no_sj')
            ->join('m_brg_spek as c', 'b.spek_id', '=', 'c.spek_id')
            ->where('a.no_sj', $request->no_sj)
            ->select('c.spek', 'b.qty', 'b.satuan')
            ->get();

        return DataTables::of($details)->make(true);
    }

    /**
     *  the form for editing the specified resource.
     */
    public function edit(Request $request, string $no_sj)
    {
        $user = $this->userAuth();
        $no_req = str_replace('-', '/', $no_sj);

        $tgl = TrMutasi::where('no_sj', $no_req)
            ->where('status', 0)
            ->value('tgl');

        if ($request->ajax()) {
            $type = $request->input('type');

            if ($type == 'details') {
                $details = DB::table('tr_mutasi_detail as a')
                    ->join('m_brg as b', 'a.brg_id', '=', 'b.brg_id')
                    ->join('m_brg_spek as c', 'a.spek_id', '=', 'c.spek_id')
                    ->select('b.brg_id', 'b.nm_brg as nama', 'a.qty_beli', 'a.satuan_beli', 'a.qty_std', 'a.satuan_std', 'c.konversi1')
                    ->where('no_sj', $no_req)
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

        return view('pages.penerimaan-skm.edit', compact('user', 'tgl', 'no_sj', 'no_req'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $no_sj)
    {
        $no_req = str_replace('-', '/', $no_sj);
        $responseMessage = '';

        if (!empty($request->items)) {
            foreach ($request->items as $item) {
                $data_tr_mutasi_detail = TrMutasiDetail::where('no_sj', operator: $no_req)
                    ->where('brg_id', $item['brg_id'])
                    ->first();

                if ($data_tr_mutasi_detail) {
                    $nama = Barang::where('brg_id', $item['brg_id'])->value('nm_brg');
                    $data_tr_mutasi_detail->update([
                        'qty_beli' => $item['qty_beli'],
                        'qty_std' => $item['qty_std']
                    ]);
                    $responseMessage = 'Data ' . $nama . ' berhasil diubah. Menjadi Qty : ' . $item['qty_beli'];
                }
            }
        } else {
            $data_tr_mutasi = TrMutasi::where('no_sj', $no_req)->first();
            $data_tr_mutasi->update([
                'tgl' => $request->tgl,
            ]);
            $responseMessage = 'Data transaksi berhasil diubah.';
        }
        return response()->json(['success' => true, 'message' => $responseMessage], 200);
    }

    public function exportPDF($no_sj) {

    }
}
