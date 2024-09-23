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
        $tgl = '2024-09-23'; // ganti dengan tanggal yang sesuai
        if ($request->ajax()) {

            $targetMesin = TargetMesin::with('targetShift.targetHari.targetWeek.barang')
                ->with('mesin')
                ->whereHas('targetShift.targetHari', function ($query) use ($tgl) {
                    $query->whereDate('tgl', $tgl); // memastikan bahwa tgl adalah kolom tanggal
                })
                ->orderby('msn_trgt_id', 'desc')->get();

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
        $sisa = '';
        foreach ($request->sisaHasil as $key => $value) {
            $sisa = $value;
        }
        return response()->json(['success' => false, 'message' => 'Target melebihi target mingguan. Sisa target harian : ' . $sisa], 200);
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
