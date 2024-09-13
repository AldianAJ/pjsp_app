<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Barang;
use App\Models\Admin\Harian;
use App\Models\Admin\Mesin;
use App\Models\Admin\Mingguan;
use App\Models\Admin\Shift;
use App\Models\Admin\TargetMesin;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

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

        $tahun = date('Y'); // Atau ganti dengan tahun tertentu, misalnya '2024'
        $mingguList = [];
        $tahunList = [];

        $tanggal = Carbon::now()->setISODate($tahun, 1);

        for ($minggu = 1; $minggu <= 53; $minggu++) {
            if ($tanggal->year != $tahun) {
                break;
            }

            $mingguList[] = [
                'minggu' => $minggu,
                'tanggal' => $tanggal->startOfWeek()->format('Y-m-d'),
            ];

            $tanggal->addWeek();
        }

        $path = 'kinerja-minggu.';
        if ($request->ajax()) {
            $targetMinggu = Mingguan::with('barang')->orderby('week_id', 'desc');
            if ($request->has('tahun') && $request->tahun != '') {
                $targetMinggu->where('tahun', $request->tahun);
            }

            if ($request->has('week') && $request->week != '') {
                $targetMinggu->whereBetween('WEEK', [$request->week - 4, $request->week]);
            }
            $targetMinggu->get();

            return DataTables::of($targetMinggu)
                ->addColumn('action', function ($object) use ($path) {
                    $html = '<button class="btn btn-secondary btn-detailHari waves-effect waves-light me-1" data-week-id="' . $object->week_id . '">'
                        . '<i class="bx bx-list-check align-middle me-2 font-size-18"></i> Detail</button>';
                    if ($object->WEEK == date('W')) {
                        $html .= '<button class="btn btn-success btn-editWeek waves-effect waves-light me-1" data-week-id="' . $object->week_id . '">'
                            . '<i class="bx bx-edit align-middle me-2 font-size-18"></i> Edit</button>';
                    }

                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('pages.kinerja-week.index', compact('user', 'mingguList'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $user = $this->userAuth();
        $tahun = date('Y');
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
                'tanggal' => $tanggal->startOfWeek()->format('Y-m-d'),
            ];

            $tanggal->addWeek();
        }

        $path = 'kinerja-minggu.create.';
        if ($request->ajax()) {
            $barangs = Barang::where('status', 0)->get();

            return DataTables::of($barangs)
                ->addColumn('action', function ($object) {
                    // $html = '<div class="d-flex justify-content-center"><button class="btn btn-primary waves-effect waves-light btn-add" data-bs-toggle="modal"' .
                    //         'data-bs-target="#qtyModal"><i class="bx bx-plus-circle align-middle font-size-18"></i></button></div>';
                    //     return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('pages.kinerja-week.create', compact('user', 'mingguList', 'barangs'));
    }

    public function store(Request $request)
    {
        foreach ($request->items as $item) {
            $cek = Mingguan::where('tahun', $request->tahun)->where('week', $request->minggu)->where('brg_id', $item['brg_id'])->count();
            if ($cek > 0) {
                // return response()->json(['success' => false, 'message' => 'Target ' . $item['nm_brg'] . ' sudah ada.'], 200);
                return redirect()->route('kinerja-minggu')->with('success', 'Target ' . $item['nm_brg'] . ' sudah ada.');
            }

            $week_id = $request->tahun . $request->minggu . '/' . str_pad(Mingguan::count() + 1, 3, '0', STR_PAD_LEFT);
            Mingguan::create([
                'week_id' => $week_id,
                'tahun' => $request->tahun,
                'week' => $request->minggu,
                'brg_id' => $item['brg_id'],
                'qty' => $item['qty'],
            ]);
        }
        return redirect()->route('kinerja-minggu')->with('success', 'Target mingguan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $user = $this->userAuth();
        $data = Mingguan::find($id);
        $barangs = Barang::where('status', 0)->get();
        return view('pages.kinerja-week.edit', compact('user', 'data', 'barangs'));
    }

    public function update(Request $request)
    {
        $data = Mingguan::find($request->id);
        $data->update([
            'qty' => $request->qty,
        ]);
        // return redirect()->route('kinerja-minggu')->with('success', 'Target mingguan berhasil diubah.');
        return response()->json(['success' => true, 'message' => 'Target mingguan berhasil diubah.'], 200);
    }

    /**
     * resource Kinerja Harian.
     */
    public function indexhari(Request $request)
    {
        $user = $this->userAuth();

        $tahun = date('Y'); // Atau ganti dengan tahun tertentu, misalnya '2024'
        $mingguList = [];
        $tahunList = [];

        $tanggal = Carbon::now()->setISODate($tahun, 1);

        for ($minggu = 1; $minggu <= 53; $minggu++) {
            if ($tanggal->year != $tahun) {
                break;
            }

            $mingguList[] = [
                'minggu' => $minggu,
                'tanggal_start' => $tanggal->startOfWeek()->format('Y-m-d'),
                'tanggal_end' => $tanggal->endOfWeek()->format('Y-m-d'),
            ];

            $tanggal->addWeek();
        }

        $path = 'kinerja-hari.';
        if ($request->ajax()) {
            $targetMinggu = Mingguan::with('barang')->orderby('week_id', 'desc');
            if ($request->has('tahun') && $request->tahun != '') {
                $targetMinggu->where('tahun', $request->tahun);
            }

            if ($request->has('week') && $request->week != '') {
                $targetMinggu->whereBetween('WEEK', [$request->week - 4, $request->week]);
            }
            $targetMinggu->get();

            return DataTables::of($targetMinggu)
                ->addColumn('action', function ($object) {
                    if ($object->WEEK == date('W')) {
                        $html = '<button class="btn btn-info btn-edit waves-effect waves-light me-1" data-week-id="' . $object->week_id . '">'
                            . '<i class="bx bx-transfer-alt align-middle me-2 font-size-18"></i> Proses</button>';
                    } else {
                        $html = '<button class="btn btn-secondary btn-detailHari waves-effect waves-light me-1" data-week-id="' . $object->week_id . '">'
                            . '<i class="bx bx-list-check align-middle me-2 font-size-18"></i> Detail</button>';
                    }

                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('pages.kinerja-hari.index', compact('user', 'mingguList'));
    }

    public function detailhari(Request $request)
    {
        $user = $this->userAuth();
        $path = 'kinerja-shift.';
        if ($request->ajax()) {
            $targetHari = Harian::with('targetWeek.barang')->where('week_id', $request->week_id)->get();

            return DataTables::of($targetHari)
                ->addColumn('action', function ($object) {
                    $html = '<button class="btn btn-info btn-shift waves-effect waves-light me-1" data-harian-id="' . $object->harian_id . '">'
                        . '<i class="bx bx-transfer-alt align-middle me-2 font-size-18"></i> Proses</button>';
                    if ($object->tgl == date('Y-m-d')) {
                        $html .= '<button class="btn btn-success btn-editHari waves-effect waves-light me-1" data-harian-id="' . $object->harian_id . '">'
                            . '<i class="bx bx-edit align-middle me-2 font-size-18"></i> Edit</button>';
                    }
                    return $html;
                })
                ->rawColumns(['action'])
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
            // return redirect()->route('kinerja-hari')->with('error', 'Data target harian sudah ada.');
            return response()->json(['success' => false, 'message' => 'Data target harian sudah ada.'], 200);
        }

        $weeklyTarget = Mingguan::findOrFail($request->week_id);
        if ($weeklyTarget != null) {
            // Hitung total target harian untuk minggu tersebut
            $totalHarian = $weeklyTarget->targetHari()->where('week_id', $request->week_id)->sum('qty');
            $sisa = $weeklyTarget->qty - $totalHarian;
            $totalHarian += $request->qty;
            $sisaBerhasil = $weeklyTarget->qty - $totalHarian;

            if ($totalHarian > $weeklyTarget->qty || $sisa == 0) {
                return response()->json(['success' => false, 'message' => 'Target melebihi target mingguan. Sisa target harian : ' . $sisa], 200);
            }
        }

        Harian::create([
            'harian_id' => $harian_id,
            'week_id' => $request->week_id,
            'tgl' => $request->tgl,
            'qty' => $request->qty,
        ]);

        return response()->json(['success' => true, 'message' => 'Target harian berhasil ditambahkan. Sisa target harian : ' . $sisaBerhasil], 200);
    }

    public function updatehari(Request $request)
    {
        $weeklyTarget = Mingguan::findOrFail($request->week_id);
        if ($weeklyTarget != null) {
            // Hitung total target harian untuk minggu tersebut
            $totalHarian = $weeklyTarget->targetHari()->where('week_id', $request->week_id)->sum('qty') - $request->qtyOri;
            $sisa = $weeklyTarget->qty - $totalHarian;
            $totalHarian += $request->qty;
            $sisaBerhasil = $weeklyTarget->qty - $totalHarian;

            if ($totalHarian > $weeklyTarget->qty || $sisa == 0) {
                return response()->json(['success' => false, 'message' => 'Target melebihi target mingguan. Sisa target harian : ' . $sisa], 200);
            }
        }

        $data = Harian::find($request->id);
        $data->update([
            'qty' => $request->qty,
        ]);
        // return redirect()->route('kinerja-hari')->with('success', 'Target harian berhasil diubah.');
        return response()->json(['success' => true, 'message' => 'Target harian berhasil diubah. Sisa target harian : ' . $sisaBerhasil], 200);
    }

    /**
     * resource Kinerja Shift.
     */
    public function indexshift(Request $request)
    {
        $user = $this->userAuth();
        $path = 'kinerja-shift.';
        if ($request->ajax()) {
            $targetMinggu = Harian::with('targetWeek.barang')->orderby('harian_id', 'desc')->get();

            return DataTables::of($targetMinggu)
                ->addColumn('action', function ($object) use ($path) {
                    $html = '<a href="' . route($path . 'create', ['harian_id' => $object->harian_id]) . '" class="btn btn-info waves-effect waves-light me-1">'
                        . ' <i class="bx bx-edit align-middle me-2 font-size-18"></i> Proses</a>'
                        . '<a href="' . route($path . 'detail', ['harian_id' => $object->harian_id]) . '" class="btn btn-secondary waves-effect waves-light">'
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
            $targetShift = Shift::with('targetHari.targetWeek.barang')->where('harian_id', $request->harian_id)->get();

            return DataTables::of($targetShift)
                ->addColumn('action', function ($object) {
                    $html = '';
                    // if ($object->tgl == date('Y-m-d')) {
                    $html .= '<button class="btn btn-success btn-editShift waves-effect waves-light me-1" data-shift-id="' . $object->shift_id . '">'
                        . '<i class="bx bx-edit align-middle me-2 font-size-18"></i> Edit</button>';
                    // }
                    return $html;
                })
                ->rawColumns(['action'])
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
        $harians = Harian::where('harian_id', $request->harian_id)->get();

        return view('pages.kinerja-shift.create', compact('user', 'harians'));
    }

    public function storeshift(Request $request)
    {

        $shift_id = $request->harian_id . $request->shift;
        $cek = Shift::where('harian_id', $request->harian_id)->where('shift', $request->shift)->get();

        if ($cek->count() > 0) {
            return response()->json(['success' => false, 'message' => 'Data target shift ' . $request->shift . ' sudah ada.'], 200);
        }

        // dd($request->harian_id);
        $dailyTarget = Harian::findOrFail($request->harian_id);
        // Hitung total target harian untuk minggu tersebut
        $totalShift = $dailyTarget->targetShift()->where('harian_id', $request->harian_id)->sum('qty');
        $sisa = $dailyTarget->qty - $totalShift;
        $totalShift += $request->qty;
        $sisaBerhasil = $dailyTarget->qty - $totalShift;

        if ($totalShift > $dailyTarget->qty || $sisa == 0) {
            return response()->json(['success' => false, 'message' => 'Target shift melebihi batas target mingguan. Sisa target shift : ' . $sisa], 200);
        }

        Shift::create([
            'shift_id' => $shift_id,
            'harian_id' => $request->harian_id,
            'shift' => $request->shift,
            'qty' => $request->qty,
        ]);

        return response()->json(['success' => true, 'message' => 'Data target shift berhasil ditambahkan. Sisa target shift : ' . $sisaBerhasil], 200);
    }

    public function updateshift(Request $request)
    {
        // dd($request->harian_id);
        $dailyTarget = Harian::findOrFail($request->harian_id);
        // Hitung total target harian untuk minggu tersebut
        $totalShift = $dailyTarget->targetShift()->where('harian_id', $request->harian_id)->sum('qty') - $request->qtyOri;
        $sisa = $dailyTarget->qty - $totalShift;
        $totalShift += $request->qty;
        $sisaBerhasil = $dailyTarget->qty - $totalShift;

        if ($totalShift > $dailyTarget->qty || $sisa == 0) {
            return response()->json(['success' => false, 'message' => 'Target shift melebihi batas target mingguan. Sisa target shift : ' . $sisa], 200);
        }
        $data = Shift::find($request->id);
        $data->update([
            'qty' => $request->qty,
        ]);
        return response()->json(['success' => true, 'message' => 'Target shift berhasil diubah. Sisa target shift : ' . $sisaBerhasil], 200);
    }

    /**
     * resource Kinerja Mesin.
     */
    public function indexmesin(Request $request)
    {
        $user = $this->userAuth();
        $path = 'kinerja-mesin.';
        if ($request->ajax()) {
            $targetMinggu = Shift::with('targetHari.targetWeek.barang')->orderby('shift_id', 'desc')->get();

            return DataTables::of($targetMinggu)
                ->addColumn('action', function ($object) use ($path) {
                    $html = '<a href="' . route($path . 'create', ['shift_id' => $object->shift_id]) . '" class="btn btn-info waves-effect waves-light me-1">'
                        . ' <i class="bx bx-edit align-middle me-2 font-size-18"></i> Proses</a>'
                        . '<a href="' . route($path . 'detail', ['shift_id' => $object->shift_id]) . '" class="btn btn-secondary waves-effect waves-light">'
                        . ' <i class="bx bx-edit align-middle me-2 font-size-18"></i> Detail</a>';

                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('pages.kinerja-mesin.index', compact('user'));
    }

    public function detailmesin(Request $request)
    {
        $user = $this->userAuth();
        if ($request->ajax()) {
            $targetMesin = TargetMesin::with('targetShift.targetHari.targetWeek.barang')->with('mesin')->where('shift_id', $request->shift_id)->get();

            return DataTables::of($targetMesin)
                ->make(true);
        }

        return view('pages.kinerja-mesin.detail', compact('user'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function createmesin(Request $request)
    {
        $user = $this->userAuth();
        $shifts = Shift::where('shift_id', $request->shift_id)->get();
        $mesins = Mesin::where('status', 0)->get();

        return view('pages.kinerja-mesin.create', compact('user', 'shifts', 'mesins'));
    }

    public function storemesin(Request $request)
    {
        $mesin_id = $request->shift_id . '/' . str_pad(TargetMesin::where('shift_id', $request->shift_id)->count() + 1, 3, '0', STR_PAD_LEFT);

        TargetMesin::create([
            'msn_trgt_id' => $mesin_id,
            'shift_id' => $request->shift_id,
            'mesin_id' => $request->mesin_id,
            'qty' => $request->qty,
        ]);

        return redirect()->route('kinerja-mesin')->with('success', 'Data target mesin berhasil ditambahkan.');
    }
}
