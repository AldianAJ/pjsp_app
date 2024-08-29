<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\DetailPengirimanCounter;
use Illuminate\Http\Request;
use App\Models\Admin\PengirimanBarang;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;

class PengirimanBarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     public function userAuth()
    {
        $user = Auth::guard('user')->user();
        return $user;
    }
    public function index()
    {
        $pengirimanBarangs = PengirimanBarang::all();
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
        $path = "pengiriman-barang";



        return view('pages.history.pengiriman-barang', compact('user'));
    }
}
