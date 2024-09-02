<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Admin\Barang;
use App\Models\Admin\Harian;
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
        $path = 'kinerja-minggu.';
        if ($request->ajax()) {
            $targetMinggu = Mingguan::all();
            return DataTables::of($targetMinggu)
                ->addColumn('action', function ($object) use ($path) {
                    $html = '<a href="' . route($path . "edit", ["supplier_id" => $object->week_id]) . '" class="btn btn-secondary waves-effect waves-light">'
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
        return view('pages.kinerja.create', compact('user', 'mingguList', 'barangs'));
    }

    public function store(Request $request)
    {
        foreach ($request->items as $item) {
            $week_id = $request->tahun . $request->minggu . '/' . str_pad(Mingguan::count() + 1, 3, '0', STR_PAD_LEFT);
            Mingguan::create([
                'week_id' => $week_id,
                'tahun' => $request->tahun,
                'week' => $request->minggu,
                'brg_id' => $item['brg_id'],
                'qty' => $item['qty'],
            ]);
        }
        return redirect()->route('kinerja-minggu')->with('success', 'Data target mingguan berhasil ditambahkan.');
    }

    public function indexhari(Request $request)
    {
        $user = $this->userAuth();
        $path = 'kinerja-hari.';
        if ($request->ajax()) {
            $targetMinggu = Mingguan::all();
            return DataTables::of($targetMinggu)
                ->addColumn('action', function ($object) use ($path) {
                    $html = '<a href="' . route($path . "create", ["week_id" => $object->week_id]) . '" class="btn btn-secondary waves-effect waves-light">'
                        . ' <i class="bx bx-edit align-middle me-2 font-size-18"></i> Proses</a>';
                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('pages.kinerja-hari.index', compact('user'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function createhari(Request $request)
    {
        $user = $this->userAuth();
        $barangs = Mingguan::where('week_id', $request->week_id)->get();

        return view('pages.kinerja-hari.create', compact('user', 'barangs'));
    }

    public function storehari(Request $request)
    {

        $date = Carbon::parse($request->tgl); // Ganti dengan tanggal yang Anda inginkan
        $dayNumber = $date->dayOfWeek; // Carbon's dayOfWeek starts from 0 (Sunday) to 6 (Saturday)

        $harian_id = $request->week_id . '/' . $dayNumber;
        Harian::create([
            'harian_id' => $harian_id,
            'week_id' => $request->week_id,
            'tgl' => $request->tgl,
            'qty' => $request->qty,
        ]);
        return redirect()->route('kinerja-hari')->with('success', 'Data target harian berhasil ditambahkan.');
    }
}
