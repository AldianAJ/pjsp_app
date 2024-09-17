<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\StokMasuk;
use App\Models\Admin\DetailStokMasuk;
use App\Models\Admin\Gudang;
use App\Models\Admin\Supplier;
use App\Models\Admin\Barang;
use App\Models\Admin\StokBarang;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;

class StokMasukController extends Controller
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

        $suppliers = Supplier::all();

        if ($request->ajax()) {
            $supplierId = $request->get('supplier_id');
            $selectedMonth = $request->get('selected_month');
            $selectedYear = $request->get('selected_year');

            $stokMasuks = StokMasuk::with('supplier')
                ->where('status', 0)
                ->when($supplierId, function ($query, $supplierId) {
                    return $query->where('supplier_id', $supplierId);
                })
                ->when($selectedMonth && $selectedYear, function ($query) use ($selectedMonth, $selectedYear) {
                    return $query->whereMonth('tgl', $selectedMonth)
                        ->whereYear('tgl', $selectedYear);
                })
                ->orderBy('no_trm', 'desc')
                ->get();

            return DataTables::of($stokMasuks)
                ->addColumn('action', function ($object) use ($path) {
                    $no = str_replace('/', '-', $object->no_trm);
                    $html = '<a href="' . route($path . "edit", ["no_trm" => $no]) . '" class="btn btn-success waves-effect waves-light mx-1">'
                        . ' <i class="bx bx-edit align-middle me-2 font-size-18"></i> Edit</a>';
                    $html = '<button class="btn btn-secondary waves-effect waves-light btn-detail me-2" data-bs-toggle="modal" data-bs-target="#detailModal">'
                        . '<i class="bx bx-detail font-size-18 align-middle me-2"></i>Detail</button>';
                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('pages.stok-masuk.index', compact('user', 'suppliers'));
    }


    public function create(Request $request)
    {
        $user = $this->userAuth();

        $gudang_id = Gudang::where('jenis', 2)->value('gudang_id');
        $suppliers = Supplier::where('status', 0)->get();

        if ($request->ajax()) {
            $barangs = Barang::where('status', 0)->get();
            return DataTables::of($barangs)->make(true);
        }
        return view('pages.stok-masuk.create', compact('user', 'gudang_id', 'suppliers'));
    }

    public function store(Request $request)
    {

        $no_trm = 'RCV/GU' . '/' . date('y/m/' . str_pad(StokMasuk::count() + 1, 3, '0', STR_PAD_LEFT));

        $gudang_id = $request->gudang_id;

        StokMasuk::create([
            'no_trm' => $no_trm,
            'no_sj' => $request->no_sj,
            'supplier_id' => $request->supplier_id,
            'tgl' => $request->tgl,
            'gudang_id' => $gudang_id,
        ]);

        foreach ($request->items as $item) {
            DetailStokMasuk::create([
                'no_trm' => $no_trm,
                'brg_id' => $item['brg_id'],
                'qty' => $item['qty'],
                'satuan_beli' => $item['satuan_beli'],
                'ket' => $item['ket'],
            ]);

            $lastStok = StokBarang::where('gudang_id', $gudang_id)
                ->where('brg_id', $item['brg_id'])
                ->orderBy('stok_id', 'desc')
                ->first();

            $akhir = ($awal = ($lastStok ? $lastStok->akhir : 0)) + ($masuk = $item['qty']);

            $id = str_pad(StokBarang::count() + 1, 3, '0', STR_PAD_LEFT);
            $stok_id = "{$gudang_id}/{$item['brg_id']}/{$id}";

            StokBarang::create([
                'stok_id' => $stok_id,
                'tgl' => $request->tgl,
                'brg_id' => $item['brg_id'],
                'gudang_id' => $gudang_id,
                'doc_id' => $no_trm,
                'awal' => $awal,
                'masuk' => $masuk,
                'keluar' => 0,
                'akhir' => $akhir,
            ]);
        }


        return redirect()->route('stok-masuk')->with('success', 'Data stok masuk berhasil ditambahkan.');
    }


    public function edit(Request $request, string $no_trm)
    {
        $user = $this->userAuth();
        $no_trm_supp = str_replace('-', '/', $no_trm);

        $datas = StokMasuk::where('no_trm', $no_trm_supp)
            ->where('status', 0)
            ->first();

        if ($request->ajax()) {
            $type = $request->input('type');

            if ($type == 'data_stok_masuks') {
                $data_stok_masuks = StokMasuk::with('supplier')
                    ->where('no_trm', $no_trm_supp)
                    ->where('status', 0)
                    ->get();

                return DataTables::of($data_stok_masuks)->make(true);
            } elseif ($type == 'data_detail_stok_masuks') {
                $data_detail_stok_masuks = DetailStokMasuk::with('barang')
                    ->where('no_trm', $no_trm_supp)
                    ->where('status', 0)
                    ->get();
                return DataTables::of($data_detail_stok_masuks)->make(true);
            }
        }
        return view('pages.stok-masuk.edit', compact('user', 'datas', 'no_trm', 'no_trm_supp'));
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $no_trm)
    {
        $no_trm_supp = str_replace('-', '/', $no_trm);
        $responseMessage = '';

        foreach ($request->items as $item) {
            $detailStokMasuk = DetailStokMasuk::where('no_trm', $no_trm_supp)
                ->where('brg_id', $item['brg_id'])
                ->first();

            if ($detailStokMasuk) {
                $nama = Barang::where('brg_id', $item['brg_id'])->value('nm_brg');
                $detailStokMasuk->update(['qty' => $item['qty']]);
                $responseMessage = 'Data ' . $nama . ' berhasil diubah. Menjadi Qty : ' . $item['qty'];
            }
        }

        return response()->json(['success' => true, 'message' => $responseMessage], 200);
    }

    public function showDetail(Request $request)
    {
        $details = StokMasuk::with('detail_stok_masuk.barang')
            ->where('no_trm', $request->no_trm)->get();

        return DataTables::of($details)->make(true);
    }
}
