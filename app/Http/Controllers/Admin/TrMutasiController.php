<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Barang;
use App\Models\Admin\TrMutasi;
use App\Models\Admin\TrMutasiDetail;
use App\Models\Admin\Gudang;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class TrMutasiController extends Controller
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
        $path = 'pengiriman-batangan.';

        if ($request->ajax()) {
            $mutasis = TrMutasi::where('status', 0)
                ->where('mutasi_id', 'LIKE', 'MKR%')
                ->orderBy('mutasi_id', 'desc')
                ->get();

            return DataTables::of($mutasis)
                ->addColumn('action', function ($object) use ($path) {
                    $no = str_replace('/', '-', $object->mutasi_id);
                    $html = '<a href="' . route($path . "edit", ["mutasi_id" => $no]) . '" class="btn btn-success waves-effect waves-light mx-1">'
                        . ' <i class="bx bx-edit align-middle me-2 font-size-18"></i> Edit</a>';
                    $html .= '<button class="btn btn-secondary waves-effect waves-light btn-detail me-2" data-bs-toggle="modal" data-bs-target="#detailModal">'
                        . '<i class="bx bx-detail font-size-18 align-middle me-2"></i>Detail</button>';
                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('pages.pengiriman-batangan.index', compact('user'));
    }

    /**
     *  the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $user = $this->userAuth();

        $msn_asal = DB::table('tr_target_mesin as a')
            ->join('m_mesin as b', 'a.mesin_id', '=', 'b.mesin_id')
            ->select('a.msn_trgt_id', 'a.mesin_id', 'b.nama')
            ->get();

        $msn_tujuan = DB::table('tr_target_mesin as a')
            ->join('m_mesin as b', 'a.mesin_id', '=', 'b.mesin_id')
            ->select('a.msn_trgt_id', 'a.mesin_id', 'b.nama')
            ->get();

        if ($request->ajax()) {
            $speks = DB::table('m_brg_spek as a')
                ->join('m_brg as b', 'a.brg_id', '=', 'b.brg_id')
                ->select('b.brg_id', 'b.nm_brg', 'a.satuan1', 'a.satuan2', 'a.konversi1', 'a.spek_id', 'a.spek')
                ->where('b.brg_id', 'LIKE', 'WIP%')
                ->where('a.satuan1', 'TRAY')
                ->where('a.status', 0)
                ->get();

            return DataTables::of($speks)->make(true);
        }

        return view('pages.pengiriman-batangan.create', compact('user', 'msn_asal', 'msn_tujuan'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $mutasi_id = 'MKR/SKM' . '/' . date('y/m/' . str_pad(TrMutasi::count() + 1, 3, '0', STR_PAD_LEFT));

        TrMutasi::create([
            'mutasi_id' => $mutasi_id,
            'tgl' => $request->tgl,
        ]);

        foreach ($request->items as $item) {
            TrMutasiDetail::create([
                'mutasi_id' => $mutasi_id,
                'gdg_asal' => $request->msn_asal,
                'gdg_tujuan' => $request->msn_tujuan,
                'spek_id' => $item['spek_id'],
                'qty' => $item['qty'],
                'satuan' => $item['satuan'],
                'qty_std' => $item['qty_std'],
                'satuan_std' => $item['satuan_std'],
                'ket' => $item['ket'],
            ]);
        }

        return redirect()->route('pengiriman-batangan')->with('success', 'Data pengiriman berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function detail(Request $request)
    {
        $details = DB::table('tr_mutasi as a')
            ->join('tr_mutasi_detail as b', 'a.mutasi_id', '=', 'b.mutasi_id')
            ->join('m_brg_spek as c', 'b.spek_id', '=', 'c.spek_id')
            ->where('a.mutasi_id', $request->mutasi_id)
            ->select('c.spek', 'b.qty', 'b.satuan')
            ->get();

        return DataTables::of($details)->make(true);
    }

    /**
     *  the form for editing the specified resource.
     */
    public function edit(Request $request, string $mutasi_id)
    {
        $user = $this->userAuth();
        $no_req = str_replace('-', '/', $mutasi_id);

        $tgl = TrMutasi::where('mutasi_id', $no_req)
            ->where('status', 0)
            ->value('tgl');

        if ($request->ajax()) {
            $type = $request->input('type');

            if ($type == 'details') {
                $details = DB::table('tr_mutasi_detail as a')
                    ->join('m_brg as b', 'a.brg_id', '=', 'b.brg_id')
                    ->join('m_brg_spek as c', 'a.spek_id', '=', 'c.spek_id')
                    ->select('b.brg_id', 'b.nm_brg as nama', 'a.qty_beli', 'a.satuan_beli', 'a.qty_std', 'a.satuan_std', 'c.konversi1')
                    ->where('mutasi_id', $no_req)
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

        return view('pages.penerimaan-skm.edit', compact('user', 'tgl', 'mutasi_id', 'no_req'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $mutasi_id)
    {
        $no_req = str_replace('-', '/', $mutasi_id);
        $responseMessage = '';

        if (!empty($request->items)) {
            foreach ($request->items as $item) {
                $data_tr_mutasi_detail = TrMutasiDetail::where('mutasi_id', operator: $no_req)
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
            $data_tr_mutasi = TrMutasi::where('mutasi_id', $no_req)->first();
            $data_tr_mutasi->update([
                'tgl' => $request->tgl,
            ]);
            $responseMessage = 'Data transaksi berhasil diubah.';
        }
        return response()->json(['success' => true, 'message' => $responseMessage], 200);
    }

    public function indexBJSK(Request $request)
    {
        $user = $this->userAuth();
        $path = 'pengiriman-bjsk.';

        if ($request->ajax()) {
            $mutasis = TrMutasi::where('status', 0)
                ->where('mutasi_id', 'LIKE', 'PKG%')
                ->orderBy('mutasi_id', 'desc')
                ->get();

            return DataTables::of($mutasis)
                ->addColumn('action', function ($object) use ($path) {
                    $no = str_replace('/', '-', $object->mutasi_id);
                    $html = '<a href="' . route($path . "edit", ["mutasi_id" => $no]) . '" class="btn btn-success waves-effect waves-light mx-1">'
                        . ' <i class="bx bx-edit align-middle me-2 font-size-18"></i> Edit</a>';
                    $html .= '<button class="btn btn-secondary waves-effect waves-light btn-detail me-2" data-bs-toggle="modal" data-bs-target="#detailModal">'
                        . '<i class="bx bx-detail font-size-18 align-middle me-2"></i>Detail</button>';
                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('pages.pengiriman-bjsk.index', compact('user'));
    }

    /**
     *  the form for creating a new resource.
     */
    public function createBJSK(Request $request)
    {
        $user = $this->userAuth();

        $msn_asal = DB::table('tr_target_mesin as a')
            ->join('m_mesin as b', 'a.mesin_id', '=', 'b.mesin_id')
            ->select('a.msn_trgt_id', 'a.mesin_id', 'b.nama')
            ->get();

        $msn_tujuan = Gudang::select('gudang_id', 'nama')->where('gudang_id', 'GU006')->first();

        if ($request->ajax()) {
            $speks = DB::table('m_brg_spek as a')
                ->join('m_brg as b', 'a.brg_id', '=', 'b.brg_id')
                ->select('b.brg_id', 'b.nm_brg', 'a.satuan1', 'a.satuan2', 'a.konversi1', 'a.spek_id', 'a.spek')
                ->where('b.brg_id', 'LIKE', 'BJR%')
                ->where('a.satuan1', 'BOX')
                ->where('a.status', 0)
                ->get();

            return DataTables::of($speks)->make(true);
        }

        return view('pages.pengiriman-bjsk.create', compact('user', 'msn_asal', 'msn_tujuan'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeBJSK(Request $request)
    {
        $mutasi_id = 'PKG/SKM' . '/' . date('y/m/' . str_pad(TrMutasi::count() + 1, 3, '0', STR_PAD_LEFT));

        TrMutasi::create([
            'mutasi_id' => $mutasi_id,
            'tgl' => $request->tgl,
        ]);

        foreach ($request->items as $item) {
            TrMutasiDetail::create([
                'mutasi_id' => $mutasi_id,
                'gdg_asal' => $request->msn_asal,
                'gdg_tujuan' => $request->gudang_id,
                'spek_id' => $item['spek_id'],
                'qty' => $item['qty'],
                'satuan' => $item['satuan'],
                'qty_std' => $item['qty_std'],
                'satuan_std' => $item['satuan_std'],
                'ket' => $item['ket'],
            ]);
        }

        return redirect()->route('pengiriman-bjsk')->with('success', 'Data pengiriman berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function detailBJSK(Request $request)
    {
        $details = DB::table('tr_mutasi as a')
            ->join('tr_mutasi_detail as b', 'a.mutasi_id', '=', 'b.mutasi_id')
            ->join('m_brg_spek as c', 'b.spek_id', '=', 'c.spek_id')
            ->where('a.mutasi_id', $request->mutasi_id)
            ->select('c.spek', 'b.qty', 'b.satuan')
            ->get();

        return DataTables::of($details)->make(true);
    }

    /**
     *  the form for editing the specified resource.
     */
    public function editBJSK(Request $request, string $mutasi_id)
    {
        $user = $this->userAuth();
        $no_req = str_replace('-', '/', $mutasi_id);

        $tgl = TrMutasi::where('mutasi_id', $no_req)
            ->where('status', 0)
            ->value('tgl');

        if ($request->ajax()) {
            $type = $request->input('type');

            if ($type == 'details') {
                $details = DB::table('tr_mutasi_detail as a')
                    ->join('m_brg as b', 'a.brg_id', '=', 'b.brg_id')
                    ->join('m_brg_spek as c', 'a.spek_id', '=', 'c.spek_id')
                    ->select('b.brg_id', 'b.nm_brg as nama', 'a.qty_beli', 'a.satuan_beli', 'a.qty_std', 'a.satuan_std', 'c.konversi1')
                    ->where('mutasi_id', $no_req)
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

        return view('pages.penerimaan-skm.edit', compact('user', 'tgl', 'mutasi_id', 'no_req'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateBJSK(Request $request, string $mutasi_id)
    {
        $no_req = str_replace('-', '/', $mutasi_id);
        $responseMessage = '';

        if (!empty($request->items)) {
            foreach ($request->items as $item) {
                $data_tr_mutasi_detail = TrMutasiDetail::where('mutasi_id', operator: $no_req)
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
            $data_tr_mutasi = TrMutasi::where('mutasi_id', $no_req)->first();
            $data_tr_mutasi->update([
                'tgl' => $request->tgl,
            ]);
            $responseMessage = 'Data transaksi berhasil diubah.';
        }
        return response()->json(['success' => true, 'message' => $responseMessage], 200);
    }


}
