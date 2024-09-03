<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Pengiriman;
use App\Models\Admin\DetailPengiriman;
use App\Models\Admin\Permintaan;
use App\Models\Admin\DetailPermintaan;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;

class PengirimanController extends Controller
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
        $path = 'pengiriman.';

        if ($request->ajax()) {
            $permintaans = DB::table('tr_reqskm as a')
                ->leftJoin('tr_krmskm_detail as b', 'a.no_reqskm', '=', 'b.no_reqskm')
                ->leftJoin('tr_krmskm as c', 'b.no_krmskm', '=', 'c.no_krmskm')
                ->select('a.no_reqskm', 'a.tgl as tgl_minta', 'c.tgl as tgl_kirim')
                ->where('a.status', 0)
                ->get();
            return DataTables::of($permintaans)
                ->addColumn('action', function ($object) use ($path) {
                    $html = '<a href="' . route($path . "detail", ["no_reqskm" => $object->no_reqskm]) . '" class="btn btn-secondary waves-effect waves-light mx-1">'
                        . ' <i class="bx bx-edit align-middle me-2 font-size-18"></i> Proses</a>';
                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('pages.pengiriman.index', compact('user'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        //
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

    public function indexHistory(Request $request)
    {
        $user = $this->userAuth();
        $path = "pengiriman-";



        return view('pages.history.pengiriman-', compact('user'));
    }
}
