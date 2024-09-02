<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Admin\Barang;
use Illuminate\Http\Request;
use App\Models\Admin\Mingguan;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class KinerjaController extends Controller
{
    public function userAuth()
    {
        $user = Auth::guard('user')->user();
        return $user;
    }

    public function index(Request $request)
    {
        $user = $this->userAuth();
        $path = 'stok-masuk.';
        if($request->ajax()) {
            $targetMinggu = Mingguan::all();
            return DataTables::of($targetMinggu)
            ->addColumn('action', function ($object) use ($path) {
                $html = '<a href="' . route($path . "edit", ["week_id" => $object->week_id]) . '" class="btn btn-secondary waves-effect waves-light">'
                    . ' <i class="bx bx-edit align-middle me-2 font-size-18"></i> Edit</a>';
                return $html;
            })
            ->rawColumns(['action'])
            ->make(true);
        }
        return view('pages.kinerja.index', compact('user'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $user = $this->userAuth();
        $tahun = date('Y'); // Atau ganti dengan tahun tertentu, misalnya '2024'
        $mingguList = [];
        $tahunList = [];

        $tanggal = Carbon::now()->setISODate($tahun, 1);
        $barangs = Barang::where('status', 0)->get();

        for ($minggu = 1; $minggu <= 53; $minggu++) {
            if ($tanggal->year != $tahun) {
                break;
            }

            $mingguList[] = [
                'minggu' => $minggu,
                'tanggal' => $tanggal->startOfWeek()->format('Y-m-d')
            ];

            $tanggal->addWeek();
        }

        $path = 'kinerja-minggu.create.';
        if ($request->ajax()) {
            $barangs = Barang::where('status', 0)->get();
            return DataTables::of($barangs)
            ->addColumn('action', function ($object) use ($path) {
                // $html = '<div class="d-flex justify-content-center"><button class="btn btn-primary waves-effect waves-light btn-add" data-bs-toggle="modal"' .
                //         'data-bs-target="#qtyModal"><i class="bx bx-plus-circle align-middle font-size-18"></i></button></div>';
                //     return $html;
            })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('pages.kinerja.create', compact('user', 'mingguList','barangs'));
    }

    public function store(Request $request)
    {
        //
    }

}
