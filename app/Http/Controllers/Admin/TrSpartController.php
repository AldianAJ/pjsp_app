<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\TrSpart;
use App\Models\Admin\TrSpartDetail;
use App\Models\Admin\TrStok;
use App\Models\Admin\Mesin;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TrSpartController extends Controller
{
    public function userAuth()
    {
        $user = Auth::guard('user')->user();
        return $user;
    }
    public function index(Request $request)
    {
        $user = $this->userAuth();
        $path = 'pemakaian-sparepart.';

        if ($request->ajax()) {
            $spareparts = DB::table('tr_spart as a')
                ->join('m_mesin as b','a.mesin_id','=','b.mesin_id')
                ->select('a.no_spart', 'a.tgl', 'b.nama', 'a.ket')
                ->where('a.status', 0)
                ->orderBy('a.no_spart', 'desc')
                ->get();

            return DataTables::of($spareparts)
                ->addColumn('action', function ($object) use ($path) {
                    $no = str_replace('/', '-', $object->no_spart);
                    $html = '<a href="' . route($path . 'edit', ['no_spart' => $no]) . '" class="btn btn-success waves-effect waves-light mx-1">'
                        . ' <i class="bx bx-edit align-middle me-2 font-size-18"></i> Edit</a>';
                    $html .= '<button class="btn btn-secondary waves-effect waves-light btn-detail me-2" data-bs-toggle="modal" data-bs-target="#detailModal">'
                        . '<i class="bx bx-detail font-size-18 align-middle me-2"></i>Detail</button>';
                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('pages.pemakaian-sparepart.index', compact('user'));
    }

    public function create(Request $request)
    {
        $user = $this->userAuth();
        $no_spart = 'SPT' . '/' . date('y/m/' . str_pad(TrSpart::count() + 1, 3, '0', STR_PAD_LEFT));
        $mesins = Mesin::where('status', 0)->get();

        if ($request->ajax()) {
            $speks = DB::table('m_brg_spek as a')
                ->join('m_brg as b', 'a.brg_id', '=', 'b.brg_id')
                ->select('b.brg_id', 'b.nm_brg', 'a.satuan1', 'a.satuan2', 'a.konversi1', 'a.spek_id', 'a.spek')
                ->where('a.status', 0)
                ->get();

            return DataTables::of($speks)->make(true);
        }
        return view('pages.pemakaian-sparepart.create', compact('user', 'no_spart', 'mesins'));
    }

    public function store(Request $request)
    {
        $no_spart = $request->no_spart;

        TrSpart::create([
            'no_spart' => $no_spart,
            'tgl' => $request->tgl,
            'mesin_id' => $request->mesin_id,
            'ket' => $request->ket,
        ]);

        foreach ($request->items as $item) {
            TrSpartDetail::create([
                'no_spart' => $no_spart,
                'spek_id' => $item['spek_id'],
                'qty' => $item['qty'],
                'satuan' => $item['satuan'],
                'qty_std' => $item['qty_std'],
                'satuan_std' => $item['satuan_std'],
            ]);
        }
        return redirect()->route('pemakaian-sparepart')->with('success', 'Data pemakaian spare part berhasil ditambahkan.');
    }
}
