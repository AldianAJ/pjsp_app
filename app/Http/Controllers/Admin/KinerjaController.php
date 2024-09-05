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

    /**
     * resource Kinerja Mingguan.
     */
    public function index(Request $request)
    {
        $user = $this->userAuth();
        $path = 'kinerja-minggu.';
        if ($request->ajax()) {
            $targetMinggu = Mingguan::with('barang')->orderby('week_id', 'desc')->get();
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

    /**
     * resource Kinerja Harian.
     */
    public function indexhari(Request $request)
    {
        $user = $this->userAuth();
        $path = 'kinerja-hari.';
        if ($request->ajax()) {
            $targetMinggu = Mingguan::with('barang')->orderby('week_id', 'asc')->get();
            return DataTables::of($targetMinggu)
                ->addColumn('action', function ($object) use ($path) {
                    $html = '<a href="' . route($path . "create", ["week_id" => $object->week_id]) . '" class="btn btn-secondary waves-effect waves-light me-1">'
                        . ' <i class="bx bx-edit align-middle me-2 font-size-18"></i> Proses</a>'
                        . '<a href="' . route($path . "detail", ["week_id" => $object->week_id]) . '" class="btn btn-secondary waves-effect waves-light">'
                        . ' <i class="bx bx-edit align-middle me-2 font-size-18"></i> Detail</a>';
                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('pages.kinerja-hari.index', compact('user'));
    }

    public function detailhari(Request $request)
    {
        $user = $this->userAuth();
        if ($request->ajax()) {
            $targetHari = Harian::where('week_id', $request->week_id)->get();
            return DataTables::of($targetHari)
                ->make(true);
        }
        return view('pages.kinerja-hari.detail', compact('user'));
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

        $cek = Harian::where('week_id', $request->week_id)->where('tgl', $request->tgl)->get();

        if ($cek->count() > 0) {
            return redirect()->route('kinerja-hari')->with('error', 'Data target harian sudah ada.');
        }

        $weeklyTarget = Mingguan::findOrFail($request->week_id);
        // Hitung total target harian untuk minggu tersebut
        $totalHarian = $weeklyTarget->targetHari()->where('week_id', $request->week_id)->sum('qty');
        $sisa = $weeklyTarget->qty - $totalHarian;
        $totalHarian += $request->qty;

        if ($totalHarian > $weeklyTarget->qty) {
            return redirect()->route('kinerja-hari')->with('error', 'Target harian melebihi batas target mingguan. Sisa target harian : ' . $sisa);
        }

        Harian::create([
            'harian_id' => $harian_id,
            'week_id' => $request->week_id,
            'tgl' => $request->tgl,
            'qty' => $request->qty,
        ]);
        return redirect()->route('kinerja-hari')->with('success', 'Data target harian berhasil ditambahkan. Sisa target harian : ' . $sisa);
    }

    /**
     * resource Kinerja Shift.
     */
    public function indexshift(Request $request)
    {
        $user = $this->userAuth();
        $path = 'kinerja-shift.';
        if ($request->ajax()) {
            $targetMinggu = Harian::with('targetWeek.barang')->get();
            return DataTables::of($targetMinggu)
                ->addColumn('action', function ($object) use ($path) {
                    $html = '<a href="' . route($path . "create", ["harian_id" => $object->harian_id]) . '" class="btn btn-secondary waves-effect waves-light me-1">'
                        . ' <i class="bx bx-edit align-middle me-2 font-size-18"></i> Proses</a>'
                        . '<a href="' . route($path . "detail", ["harian_id" => $object->harian_id]) . '" class="btn btn-secondary waves-effect waves-light">'
                        . ' <i class="bx bx-edit align-middle me-2 font-size-18"></i> Detail</a>';
                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('pages.kinerja-shift.index', compact('user'));
    }

    public function detailshift(Request $request)
    {
        $user = $this->userAuth();
        if ($request->ajax()) {
            $targetHari = Harian::where('week_id', $request->week_id)->get();
            return DataTables::of($targetHari)
                ->make(true);
        }
        return view('pages.kinerja-shift.detail', compact('user'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function createshift(Request $request)
    {
        $user = $this->userAuth();
        $harians = Harian::where('harian_id', $request->week_id)->get();

        return view('pages.kinerja-shift.create', compact('user', 'harians'));
    }

    public function storeshift(Request $request)
    {

        $date = Carbon::parse($request->tgl); // Ganti dengan tanggal yang Anda inginkan
        $dayNumber = $date->dayOfWeek; // Carbon's dayOfWeek starts from 0 (Sunday) to 6 (Saturday)

        $harian_id = $request->week_id . '/' . $dayNumber;

        $cek = Harian::where('week_id', $request->week_id)->where('tgl', $request->tgl)->get();

        if ($cek->count() > 0) {
            return redirect()->route('kinerja-shift')->with('error', 'Data target harian sudah ada.');
        }

        $weeklyTarget = Mingguan::findOrFail($request->week_id);
        // Hitung total target harian untuk minggu tersebut
        $totalHarian = $weeklyTarget->targetHari()->where('week_id', $request->week_id)->sum('qty');
        $sisa = $weeklyTarget->qty - $totalHarian;
        $totalHarian += $request->qty;

        if ($totalHarian > $weeklyTarget->qty) {
            return redirect()->route('kinerja-shift')->with('error', 'Target harian melebihi batas target mingguan. Sisa target harian : ' . $sisa);
        }

        Harian::create([
            'harian_id' => $harian_id,
            'week_id' => $request->week_id,
            'tgl' => $request->tgl,
            'qty' => $request->qty,
        ]);
        return redirect()->route('kinerja-shift')->with('success', 'Data target harian berhasil ditambahkan. Sisa target harian : ' . $sisa);
    }

    /**
     * resource Kinerja Mesin.
     */
    public function indexmesin(Request $request)
    {
        $user = $this->userAuth();
        $path = 'kinerja-hari.';
        if ($request->ajax()) {
            $targetMinggu = Mingguan::with('barang')->orderby('week_id', 'asc')->get();
            return DataTables::of($targetMinggu)
                ->addColumn('action', function ($object) use ($path) {
                    $html = '<a href="' . route($path . "create", ["week_id" => $object->week_id]) . '" class="btn btn-secondary waves-effect waves-light me-1">'
                        . ' <i class="bx bx-edit align-middle me-2 font-size-18"></i> Proses</a>'
                        . '<a href="' . route($path . "detail", ["week_id" => $object->week_id]) . '" class="btn btn-secondary waves-effect waves-light">'
                        . ' <i class="bx bx-edit align-middle me-2 font-size-18"></i> Detail</a>';
                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('pages.kinerja-hari.index', compact('user'));
    }

    public function detailmesin(Request $request)
    {
        $user = $this->userAuth();
        if ($request->ajax()) {
            $targetHari = Harian::where('week_id', $request->week_id)->get();
            return DataTables::of($targetHari)
                ->make(true);
        }
        return view('pages.kinerja-hari.detail', compact('user'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function createmesin(Request $request)
    {
        $user = $this->userAuth();
        $barangs = Mingguan::where('week_id', $request->week_id)->get();

        return view('pages.kinerja-mesin.create', compact('user', 'barangs'));
    }

    public function storemesin(Request $request)
    {

        $date = Carbon::parse($request->tgl); // Ganti dengan tanggal yang Anda inginkan
        $dayNumber = $date->dayOfWeek; // Carbon's dayOfWeek starts from 0 (Sunday) to 6 (Saturday)

        $harian_id = $request->week_id . '/' . $dayNumber;

        $cek = Harian::where('week_id', $request->week_id)->where('tgl', $request->tgl)->get();

        if ($cek->count() > 0) {
            return redirect()->route('kinerja-mesin')->with('error', 'Data target harian sudah ada.');
        }

        $weeklyTarget = Mingguan::findOrFail($request->week_id);
        // Hitung total target harian untuk minggu tersebut
        $totalHarian = $weeklyTarget->targetHari()->where('week_id', $request->week_id)->sum('qty');
        $sisa = $weeklyTarget->qty - $totalHarian;
        $totalHarian += $request->qty;

        if ($totalHarian > $weeklyTarget->qty) {
            return redirect()->route('kinerja-mesin')->with('error', 'Target harian melebihi batas target mingguan. Sisa target harian : ' . $sisa);
        }

        Harian::create([
            'harian_id' => $harian_id,
            'week_id' => $request->week_id,
            'tgl' => $request->tgl,
            'qty' => $request->qty,
        ]);
        return redirect()->route('kinerja-mesin')->with('success', 'Data target harian berhasil ditambahkan. Sisa target harian : ' . $sisa);
    }
}
