<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\Shift;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\Admin\TargetMesin;
use App\Http\Controllers\Controller;
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
        $tgl = '2024-09-14'; // ganti dengan tanggal yang sesuai
        if ($request->ajax()) {

            $targetMesin = TargetMesin::with('targetShift.targetHari.targetWeek.barang')
                ->whereHas('targetShift.targetHari', function ($query) use ($tgl) {
                    $query->whereDate('tgl', $tgl); // memastikan bahwa tgl adalah kolom tanggal
                })
                ->with('mesin')
                ->orderby('msn_trgt_id', 'desc')->get();

            return DataTables::of($targetMesin)
                ->make(true);
        }

        return view('pages.closing.detail', compact('user'));
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
