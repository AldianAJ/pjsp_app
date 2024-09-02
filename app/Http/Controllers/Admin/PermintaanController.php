<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\DetailPengirimanCounter;
use App\Models\Admin\DetailPermintaanCounter;
use App\Models\Admin\PengirimanCounter;
use App\Models\Admin\Permintaan;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class PermintaanController extends Controller
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
    $path = 'permintaan';

    if ($request->ajax()) {
        $permintaans = Permintaan::all();

        return DataTables::of($permintaans)
            ->addColumn('action', function ($object) use ($path) {
                $html = '<a href="' . route($path . "edit", ["brg_id" => $object->brg_id]) . '" class="btn btn-secondary waves-effect waves-light mx-1">'
                    . ' <i class="bx bx-edit align-middle me-2 font-size-18"></i> Edit</a>';
                $html .= '<button class="btn btn-danger waves-effect waves-light mx-1 btn-delete">'
                    . ' <i class="bx bx-trash align-middle me-2 font-size-18" ></i> Hapus</button>';
                return $html;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    return view('pages.permintaan.index', compact('user'));
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
        $path = "permintaan-counter";

        if ($request->ajax()) {
            $query = DB::table('permintaan_counters as p')
                ->join('counters as c', 'p.counter_id', '=', 'c.counter_id')
                ->join('users as u', 'c.user_id', '=', 'u.user_id')
                ->select('p.permintaan_counter_id', 'u.name', 'p.tanggal_permintaan', 'p.slug', 'p.status_permintaan')
                ->orderBy('p.tanggal_permintaan', 'desc');

            if ($user->role == 'gudang' || $user->role == 'owner') {
                $query->where('p.status_permintaan', 'Diterima/Selesai');
            } else {
                $counter = DB::table('counters')
                    ->where('user_id', $user->user_id)
                    ->first();

                $query->where('p.counter_id', $counter->counter_id);
            }

            $permintaans = $query->get();

            return DataTables::of($permintaans)
                ->addColumn('action', function ($object) use ($path) {
                    $html = '<button class="btn btn-secondary waves-effect waves-light btn-detail me-2" data-bs-toggle="modal" data-bs-target="#detailModal">'
                        . '<i class="bx bx-detail font-size-18 align-middle me-2"></i>Detail</button>';
                    $html .= '<a href="' . route($path . '.exportPDF', ["slug" => $object->slug]) . '" class="btn btn-primary waves-effect waves-light">'
                        . '<i class="bx bxs-printer align-middle me-2 font-size-18"></i>Cetak PDF</a>';
                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('pages.history.permintaan-barang', compact('user'));
    }
}
