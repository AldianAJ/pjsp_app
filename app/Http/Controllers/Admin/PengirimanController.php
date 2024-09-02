<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\DetailPengirimanCounter;
use Illuminate\Http\Request;
use App\Models\Admin\Pengiriman;
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
    $path = 'pengiriman';

    if ($request->ajax()) {
        $pengirimans = Pengiriman::all();

        return DataTables::of($pengirimans)
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
