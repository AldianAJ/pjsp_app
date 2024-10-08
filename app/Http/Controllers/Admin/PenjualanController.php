<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\BarangSpek;
use App\Models\Admin\TrSJ;
use App\Models\Admin\TrSJDetail;
use App\Models\Admin\TrHMSPO;
use App\Models\Admin\TrHMSPODetail;
use App\Models\Admin\Cust;
use App\Models\Admin\Armada;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;

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
            $no_suja = DB::table('tr_sj as a')
                ->join('tr_hms_po as b', 'a.no_po', '=', 'b.no_po')
                ->join('tr_hms_po_detail as c', 'b.no_po', '=', 'c.no_po')
                ->join('m_cust as d', 'a.cust_id', '=', 'd.cust_id')
                ->join('m_armada as e', 'a.no_pol', '=', 'e.no_pol')
                ->select('a.no_sj', 'a.tgl', 'd.nama', 'b.no_po', 'a.no_segel', 'e.no_pol', 'a.driver')
                ->where('b.status', 0)
                ->where('a.status', 0)
                ->distinct()
                ->orderBy('no_sj', 'desc')
                ->get();

            return DataTables::of($no_suja)
                ->addColumn('action', function ($object) use ($path) {
                    $no = str_replace('/', '_', $object->no_sj);
                    $html = '<a href="' . route($path . "edit", ["no_sj" => $no]) . '" class="btn btn-success waves-effect waves-light mx-1">'
                        . ' <i class="bx bx-edit align-middle me-2 font-size-18"></i> Edit</a>';

                    $html .= '<button class="btn btn-secondary waves-effect waves-light btn-detail mx-1" data-bs-toggle="modal" data-bs-target="#detailModal">'
                        . '<i class="bx bx-detail font-size-18 align-middle me-2"></i> Detail</button>';

                    $html .= '<a href="' . route($path . "exportPDF", ["no_sj" => $no]) . '" class="btn btn-primary waves-effect waves-light mx-1">'
                        . ' <i class="bx bx-printer align-middle me-2 font-size-18"></i> Cetak PDF</a>';

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
        $no_sj = 'SJ/RSR-HMS' . '/' . date('y/m/' . str_pad(TrSJ::count() + 1, 3, '0', STR_PAD_LEFT));
        $hms_poo = TrHMSPO::with(['tr_hms_po_detail'])->where('status', 0)->get();
        $cust_id = Cust::where('cust_id', 'CS0001')->value('cust_id');
        $armadas = Armada::where('status', 0)->get();

        if ($request->ajax()) {
            $no_po = $request->get('no_po');
            $speks = DB::table('tr_hms_po as a')
                ->join('tr_hms_po_detail as b', 'a.no_po', '=', 'b.no_po')
                ->join('m_brg_spek as c', 'b.spek_id', '=', 'c.spek_id')
                ->select('c.spek_id', 'c.spek', 'b.qty_po', 'b.qty_krm', 'b.selisih_krm', 'c.satuan1', 'c.konversi1', 'c.satuan2')
                ->where('a.no_po', $no_po)
                ->where('a.status', 0)
                ->get();

            return DataTables::of($speks)->make(true);
        }
        return view('pages.penjualan.create', compact('user', 'no_sj','hms_poo', 'armadas', 'cust_id'));
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $no_sj = $request->no_sj;
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

        foreach ($items as $item) {
            TrSJDetail::create([
                'no_sj' => $trSj->no_sj,
                'spek_id' => $item['spek_id'],
                'qty_karton' => $item['qty_po'],
                'qty_ball' => 0,
                'qty_slop' => 0,
                'qty_pack' => 0,
                'qty_total' => $item['qty_total'],
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
        $details = DB::table('tr_sj as a')
            ->join('tr_sj_detail as b', 'a.no_sj', '=', 'b.no_sj')
            ->join('m_brg_spek as c', 'b.spek_id', '=', 'c.spek_id')
            ->where('a.no_sj', $request->no_sj)
            ->select('c.spek', 'c.pc', 'b.qty_karton', 'b.qty_total', 'b.no_batch')
            ->get();

        return DataTables::of($details)->make(true);
    }

    /**
     *  the form for editing the specified resource.
     */
    public function edit(Request $request, string $no_sj)
    {
        $user = $this->userAuth();
        $no_suja = str_replace('_', '/', $no_sj);

        $no_pos = TrSJ::where('no_sj', $no_suja)
            ->where('status', 0)
            ->value('no_po');

        $tgl = TrSJ::where('no_sj', $no_suja)
            ->where('status', 0)
            ->value('tgl');

        $segels = TrSJ::where('no_sj', $no_suja)
            ->where('status', 0)
            ->value('no_segel');

        $data_pol = DB::table('tr_sj as a')
            ->join('tr_sj_detail as b', 'a.no_sj','=', 'b.no_sj')
            ->join('m_armada as c', 'a.no_pol', '=', 'c.no_pol')
            ->where('a.no_sj', $no_suja)
            ->where('a.status', 0)
            ->where('b.status', 0)
            ->value('a.no_pol');

        $pols = Armada::where('status', 0)->get();

        $drivers = TrSJ::where('no_sj', $no_suja)
            ->where('status', 0)
            ->value('driver');

        if ($request->ajax()) {
            $details = DB::table('tr_sj_detail as a')
                ->join('m_brg_spek as b', 'a.spek_id', '=', 'b.spek_id')
                ->select('b.spek_id', 'b.spek', 'a.qty_karton', 'a.qty_total', 'b.konversi1', 'a.no_batch')
                ->where('a.no_sj', $no_suja)
                ->where('a.status', 0)
                ->get();

            return DataTables::of($details)->make(true);
        }

        return view('pages.penjualan.edit', compact('user', 'no_pos', 'tgl', 'segels', 'data_pol','pols', 'drivers', 'no_sj', 'no_suja'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $no_sj)
    {
        $no_suja = str_replace('_', '/', $no_sj);
        $responseMessage = '';

        if (!empty($request->items)) {
            foreach ($request->items as $item) {
                $data_tr_sj_detail = TrSJDetail::where('no_sj', operator: $no_suja)
                    ->where('spek_id', $item['spek_id'])
                    ->first();

                if ($data_tr_sj_detail) {
                    $nama = BarangSpek::where('spek_id', $item['spek_id'])->value('spek');
                    $ket = "{$item['qty_karton']} Karton @{$item['qty_total']} PACK";
                    $data_tr_sj_detail->update([
                        'qty_karton' => $item['qty_karton'],
                        'qty_total' => $item['qty_total'],
                        'ket' => $ket
                    ]);
                    $responseMessage = 'Data ' . $nama . ' berhasil diubah. Menjadi Qty : ' . $item['qty_karton'];
                }
            }
        } else {
            $data_tr_sj = TrSJ::where('no_sj', $no_suja)->first();
            $data_tr_sj->update([
                'tgl' => $request->tgl,
                'no_segel' => $request->no_segel,
                'no_pol' => $request->no_pol,
                'driver' => $request->driver,
            ]);
            $responseMessage = 'Data transaksi berhasil diubah.';
        }
        return response()->json(['success' => true, 'message' => $responseMessage], 200);
    }

    public function exportPDF(string $no_sj)
    {
        $no_suja = str_replace('_', '/', $no_sj);
        $sj_header = DB::table('tr_sj as a')
            ->join('m_cust as b', 'a.cust_id', '=', 'b.cust_id')
            ->select('a.no_sj', 'a.tgl', 'b.nama', 'b.alamat_sj', 'b.kota_sj', 'a.no_po', 'a.no_segel', 'a.no_pol', 'a.driver')
            ->where('a.no_sj', $no_suja)
            ->where('a.status', 0)
            ->first();

        $sj_details = DB::table('tr_sj as a')
            ->join('tr_sj_detail as b', 'a.no_sj', '=', 'b.no_sj')
            ->join('m_brg_spek as c', 'b.spek_id', '=', 'c.spek_id')
            ->select('a.no_sj', 'c.spek', 'c.pc', 'b.qty_karton', 'b.qty_total', 'b.ket', 'b.no_batch')
            ->where('a.no_sj', $no_suja)
            ->where('a.status', 0)
            ->get();

        $title = 'Laporan Surat Jalan ' . $no_sj;

        $pdf = PDF::loadView('pages.export.penjualan', compact('sj_header', 'sj_details', 'title'));
        return $pdf->download($title . ".pdf");
    }
}
