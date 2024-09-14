<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Barang;
use App\Models\Admin\TargetMesin;
use App\Models\Admin\PengirimanSKM;
use App\Models\Admin\DetailPengirimanSKM;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class PengirimanSKMController extends Controller
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
            $pengirimans = PengirimanSKM::where('status', 0)->get();
            return DataTables::of($pengirimans)
                ->addColumn('action', function ($object) use ($path) {
                    $html = '<a href="' . route($path . "edit", ["no_krmmsn" => $object->no_krmmsn]) . '" class="btn btn-secondary waves-effect waves-light mx-1">'
                        . ' <i class="bx bx-edit align-middle me-2 font-size-18"></i> Edit</a>';
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
        $tgl = $request->tgl;

        $targetMesin = TargetMesin::with('targetShift.targetHari.targetWeek.barang')
            ->whereHas('targetShift.targetHari', function ($query) use ($tgl) {
                $query->whereDate('tgl', $tgl);
            })
            ->with('mesin')
            ->orderby('msn_trgt_id', 'desc')
            ->get();

        if ($request->ajax()) {
            $barangs = Barang::where('status', 0)->get();
            return DataTables::of($barangs)
                ->addColumn('action', function ($object) {
                    $html = '<div class="d-flex justify-content-center">
                            <button class="btn btn-primary waves-effect waves-light btn-add" data-bs-toggle="modal"
                            data-bs-target="#qtyModal">
                                <i class="bx bx-plus-circle align-middle font-size-18"></i>
                            </button>
                         </div>';
                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('pages.pengiriman-skm.create', compact('user'));
    }




    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $no_krmmsn = 'TBI/SKM' . '/' . date('y/m/' . str_pad(PengirimanSKM::count() + 1, 3, '0', STR_PAD_LEFT));

        $msn_trgt_id = $request->msn_trgt_id;

        $krmMSN = PengirimanSKM::create([
            'no_krmmsn' => $no_krmmsn,
            'tgl' => $request->tgl,
            'msn_trgt_id' => $msn_trgt_id,
        ]);

        foreach ($request->items as $item) {
            DetailPengirimanSKM::create([
                'no_krmmsn' => $no_krmmsn,
                'brg_id' => $item['brg_id'],
                'qty' => $item['qty'],
                'satuan_besar' => $item['satuan_besar'],
            ]);
        }

        return redirect()->route('pengiriman-skm')->with('success', 'Data pengiriman berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        //
    }

    public function detailKRM()
    {
        return view('pages.pengiriman-gu.detail');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit()
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update()
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy()
    {
        //
    }
}
