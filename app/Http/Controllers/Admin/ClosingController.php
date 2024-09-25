<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Admin\Shift;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\Admin\TargetMesin;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Admin\Closing;
use App\Models\Admin\DetailClosing;
use Illuminate\Support\Facades\Auth;

class ClosingController extends Controller
{
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
        $path = 'kinerja-mesin.';
        $tgl = Carbon::parse($request->tgl); // ganti dengan tanggal yang sesuai
        if ($request->ajax()) {

            $targetMesin = DB::table('tr_target_mesin as a')
                ->join('m_mesin as b', 'a.mesin_id', '=', 'b.mesin_id')
                ->join('tr_target_shift as c', 'a.shift_id', '=', 'c.shift_id')
                ->join('tr_target_harian as d', 'c.harian_id', '=', 'd.harian_id')
                ->join('tr_target_week as e', 'd.week_id', '=', 'e.week_id')
                ->join('m_brg as f', 'e.brg_id', '=', 'f.brg_id')
                ->where('d.tgl', $tgl)
                ->where('a.status', 0)
                ->select('a.msn_trgt_id', 'a.qty', 'a.mesin_id', 'c.shift', 'b.nama', 'b.jenis_id', 'f.nm_brg', 'e.brg_id')
                ->orderBy('a.msn_trgt_id', 'desc')
                ->get();

            return DataTables::of($targetMesin)
                ->addColumn('action', function ($object) use ($path) {
                    $html = '<button class="btn btn-info btn-process waves-effect waves-light me-1" data-msn-trgt-id="' . $object->msn_trgt_id . '">'
                        . '<i class="bx bx-transfer-alt align-middle me-2 font-size-18"></i> Proses</button>';

                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('pages.closing.detail', compact('user'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = $this->userAuth();
        return view('pages.closing.create', compact('user'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->sisaHasil;
        $request->reject;
        $request->bahan;
        $sisa = '|';
        $msn_trgt_id = $request->trgt_id;
        $produk = $request->produk;

        $closing = Closing::create([
            'closing_id' => 'CL001',
            'msn_trgt_id' => $msn_trgt_id,
            'jenis' => '1',
        ]);
        $id = $closing->closing_id;
        foreach ($request->sisaHasil as $item) {
            DetailClosing::create([
                'closing_id' => $id,
                'brg_id' => $produk,
                'qty' => $item['value'],
                'kode' => 1,
                'satuan' => $item['name'],
                'cek' => 1,
            ]);
        }
        foreach ($request->reject as $item) {
            $brg_id = DB::table('tmp_produk_material as a')
                ->join('m_brg as b', 'a.brg_id', '=', 'b.brg_id')
                ->where('a.brg_prod_id', $produk)
                ->where('a.tahap', 2)
                ->where('b.nm_brg', 'like', '%' . $item['name'] . '%')
                ->value('a.brg_id');

            DetailClosing::create([
                'closing_id' => $id,
                'brg_id' => $brg_id,
                'qty' => $item['value'],
                'kode' => 2,
                'cek' => 1,
            ]);
        }
        foreach ($request->bahan as $item) {
            $brg_id = DB::table('tmp_produk_material as a')
                ->join('m_brg as b', 'a.brg_id', '=', 'b.brg_id')
                ->where('a.brg_prod_id', $produk)
                ->where('a.tahap', 3)
                ->where('b.nm_brg', 'like', '%' . $item['name'] . '%')
                ->value('a.brg_id');

            DetailClosing::create([
                'closing_id' => $id,
                'brg_id' => $brg_id,
                'qty' => $item['value'],
                'kode' => 3,
                'cek' => 1,
            ]);
        }
        TargetMesin::where('msn_trgt_id', $msn_trgt_id)->update([
            'status' => 1,
        ]);
        return response()->json(['success' => true, 'message' => 'Berhasil ditambahkan.'], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
