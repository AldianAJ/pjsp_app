<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Barang;
use App\Models\Admin\BarangSpek;
use App\Models\Admin\Gudang;
use App\Models\Admin\Mesin;
use App\Models\Admin\TargetMesin;
use App\Models\Admin\TrKrmMsn;
use App\Models\Admin\TrKrmMsnDetail;
use App\Models\Admin\TrMutasi;
use App\Models\Admin\TrMutasiDetail;
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
            $pengirimans = TrMutasi::where('status', 0)
                ->where('mutasi_id', 'LIKE', 'TBI/SKM%')
                ->orderBy('mutasi_id', 'desc')
                ->get();

            return DataTables::of($pengirimans)
                ->addColumn('action', function ($object) use ($path) {
                    $no = str_replace('/', '-', $object->mutasi_id);
                    $html = '<a href="' . route($path . "edit", ["no_krmmsn" => $no]) . '" class="btn btn-success waves-effect waves-light mx-1">'
                        . ' <i class="bx bx-edit align-middle me-2 font-size-18"></i> Edit</a>';
                    $html .= '<button class="btn btn-secondary waves-effect waves-light btn-detail me-2" >'
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
        $users = auth()->user();
        $mesins = Mesin::where('status', 0)->get();
        $gudang_id = DB::table('m_user_gdg as a')
            ->where('a.user_id', $users->user_id)
            ->value('gudang_id');
        $gudangs = Gudang::where('gudang_id', $gudang_id)->where('jenis', 2)->where('status', 0)->get();

        if ($request->has('tgl')) {
            $date = $request->tgl;
            $machines = DB::table('tr_target_mesin as a')
                ->join('tr_target_shift as b', 'a.shift_id', '=', 'b.shift_id')
                ->join('tr_target_harian as c', 'b.harian_id', '=', 'c.harian_id')
                ->join('m_mesin as d', 'a.mesin_id', '=', 'd.mesin_id')
                ->select('a.msn_trgt_id', 'd.nama', 'b.shift', 'c.tgl', 'a.mesin_id')
                ->where('tgl', $date)
                ->where('a.status', 0)
                ->get();

            return response()->json($machines);
        };

        if ($request->ajax()) {
            $type = $request->input('type');

            if ($type == 'speks') {
                $speks = DB::table('m_brg_spek as a')
                    ->join('m_brg as b', 'a.brg_id', '=', 'b.brg_id')
                    ->select('b.brg_id', 'b.nm_brg', 'a.satuan1', 'a.satuan2', 'a.konversi1', 'a.spek_id', 'a.spek')
                    ->where('a.status', 0)
                    ->get();

                return DataTables::of($speks)->make(true);
            }
        }
        return view('pages.pengiriman-skm.create', compact('user', 'users', 'mesins', 'gudangs', 'gudang_id'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $no_krmmsn = 'TBI/SKM' . '/' . date('y/m/' . str_pad(TrMutasi::where('mutasi_id', 'like', 'TBI/SKM' . '/' . date('y/m/') . '%')->count() + 1, 3, '0', STR_PAD_LEFT));
        $msn_trgt_id = $request->msn_trgt_id;
        $gudang_id = $request->gdg_asal;
        // dd($request->all(), $no_krmmsn, $msn_trgt_id);
        TrMutasi::create([
            'mutasi_id' => $no_krmmsn,
            'tgl' => $request->tgl,
        ]);

        foreach ($request->items as $item) {
            TrMutasiDetail::create([
                'mutasi_id' => $no_krmmsn,
                'spek_id' => $item['spek_id'],
                'gdg_asal' => $request->gdg_asal,
                'gdg_tujuan' => $request->gdg_tujuan,
                'qty' => $item['qty_beli'],
                'satuan' => $item['satuan_beli'],
                'qty_std' => $item['qty_std'],
                'satuan_std' => $item['satuan_std'],
                'ket' => $msn_trgt_id,
            ]);

            $id = str_pad(TrStok::count() + 1, 3, '0', STR_PAD_LEFT);
            $stok_id = "{$gudang_id}/{$item['spek_id']}/{$id}";
            $keluar = $item['qty_beli'];
            $gudangs = Gudang::where('gudang_id', $gudang_id)->value('nama');
            $ket = "Pengiriman barang dari " . $gudangs;

            TrStok::create([
                'stok_id' => $stok_id,
                'tgl' => $request->tgl,
                'spek_id' => $item['spek_id'],
                'gudang_id' => $gudang_id,
                'doc_id' => $no_krmmsn,
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
    public function Detail(Request $request)
    {
        $details = DB::table('tr_mutasi as a')
            ->join('tr_mutasi_detail as b', 'a.mutasi_id', '=', 'b.mutasi_id')
            ->join('m_brg_spek as c', 'b.spek_id', '=', 'c.spek_id')
            ->join('m_brg as d', 'c.brg_id', '=', 'd.brg_id')
            ->where('a.mutasi_id', $request->mutasi_id)
            ->select('d.nm_brg', 'c.spek', 'b.qty', 'b.satuan')
            ->get();

        return DataTables::of($details)->make(true);
    }

    function edit(Request $request, $mutasi_id)
    {
        $user = $this->userAuth();
        $users = auth()->user();
        $no_mutasi = str_replace('-', '/', $mutasi_id);
        $mesins = Mesin::where('status', 0)->get();
        $gudang_id = DB::table('m_user_gdg as a')
            ->where('a.user_id', $users->user_id)
            ->value('gudang_id');
        $gudangs = Gudang::where('gudang_id', $gudang_id)->where('jenis', 2)->where('status', 0)->get();

        $tgl = TrMutasi::where('mutasi_id', $no_mutasi)
            ->where('status', 0)
            ->value('tgl');

        $gdg_tujuan = TrMutasiDetail::where('mutasi_id', $no_mutasi)
            ->where('status', 0)
            ->value('gdg_tujuan');

        if ($request->ajax()) {

            $details = DB::table('tr_mutasi_detail as a')
                ->join('m_brg_spek as b', 'a.spek_id', '=', 'b.spek_id')
                ->join('m_brg as c', 'b.brg_id', '=', 'c.brg_id')
                ->select('b.spek_id', 'c.nm_brg', 'a.qty', 'a.satuan', 'a.qty_std', 'a.satuan_std', 'b.konversi1')
                ->where('a.mutasi_id', $no_mutasi)
                ->where('a.status', 0)
                ->get();

            return DataTables::of($details)->make(true);
        }
        return view('pages.pengiriman-skm.edit', compact('user', 'tgl', 'mutasi_id', 'no_mutasi', 'users', 'mesins', 'gudangs', 'gdg_tujuan', 'gudang_id'));
    }

    function update(Request $request, $mutasi_id)
    {
        $no_mutasi = str_replace('-', '/', $mutasi_id);
        $responseMessage = '';

        if (!empty($request->items)) {
            foreach ($request->items as $item) {
                $data_tr_mutasi_detail = TrMutasiDetail::where('mutasi_id', operator: $no_mutasi)
                    ->where('spek_id', $item['spek_id'])
                    ->first();

                if ($data_tr_mutasi_detail) {
                    $nama = BarangSpek::where('spek_id', $item['spek_id'])->value('spek');
                    $data_tr_mutasi_detail->update([
                        'qty' => $item['qty'],
                        'qty_std' => $item['qty_std']
                    ]);
                    $keluar = $item['qty'];
                    $data_stok = TrStok::where('doc_id', $no_mutasi);
                    $data_stok->update([
                        'keluar' => $keluar,
                        'cek' => 1,
                    ]);
                    $responseMessage = 'Data ' . $nama . ' berhasil diubah. Menjadi Qty : ' . $item['qty'];
                }
            }
        } else {
            $data_tr_mutasi = TrMutasi::where('mutasi_id', $no_mutasi)->first();
            $data_tr_mutasi->update([
                'tgl' => $request->tgl,
            ]);
            $responseMessage = 'Data transaksi berhasil diubah.';
        }
        return response()->json(['success' => true, 'message' => $responseMessage], 200);
    }
}
