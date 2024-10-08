<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\TrTrmSup;
use App\Models\Admin\TrTrmSupDetail;
use App\Models\Admin\Gudang;
use App\Models\Admin\Supplier;
use App\Models\Admin\Barang;
use App\Models\Admin\TrStok;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TrTrmSupController extends Controller
{
    public function userAuth()
    {
        $user = Auth::guard('user')->user();
        return $user;
    }

    public function index(Request $request)
    {
        $user = $this->userAuth();
        $path = 'penerimaan-supplier.';

        $suppliers = Supplier::where('status', 0)->get();

        if ($request->ajax()) {
            $supplier_id = $request->get('supplier_id');
            $selectedMonth = $request->get('selected_month');
            $selectedYear = $request->get('selected_year');

            $tr_trmsups = TrTrmSup::with('supplier')
                ->where('status', 0)
                ->when($supplier_id, function ($query, $supplier_id) {
                    return $query->where('supplier_id', $supplier_id);
                })
                ->when($selectedMonth && $selectedYear, function ($query) use ($selectedMonth, $selectedYear) {
                    return $query->whereMonth('tgl_trm', $selectedMonth)
                        ->whereYear('tgl_trm', $selectedYear);
                })
                ->orderBy('no_trm', 'desc')
                ->get();

            return DataTables::of($tr_trmsups)
                ->addColumn('action', function ($object) use ($path) {
                    $no = str_replace('/', '-', $object->no_trm);
                    $editButton = '<a href="' . route($path . "edit", ["no_trm" => $no]) . '" class="btn btn-success waves-effect waves-light mx-1">'
                        . '<i class="bx bx-edit align-middle me-2 font-size-18"></i> Edit</a>';
                    $detailButton = '<button class="btn btn-secondary waves-effect waves-light btn-detail me-2" data-bs-toggle="modal" data-bs-target="#detailModal">'
                        . '<i class="bx bx-detail font-size-18 align-middle me-2"></i>Detail</button>';

                    return $editButton . $detailButton;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('pages.penerimaan-supplier.index', compact('user', 'suppliers'));
    }



    public function create(Request $request)
    {
        $user = $this->userAuth();
        $no_trm = 'RCV/GU' . '/' . date('y/m/' . str_pad(TrTrmSup::count() + 1, 3, '0', STR_PAD_LEFT));
        $gudang_id = Gudang::where('gudang_id', 'GU001')->value('gudang_id');
        $suppliers = Supplier::where('status', 0)->get();

        if ($request->ajax()) {
            $type = $request->input('type');

            if ($type == 'barangs') {
                $barangs = Barang::where('status', 0)
                    ->orderBy('brg_id', 'asc')
                    ->get();

                return DataTables::of($barangs)->make(true);

            } elseif ($type == 'speks') {
                $speks = DB::table('m_brg_spek as a')
                    ->join('m_brg as b', 'a.brg_id', '=', 'b.brg_id')
                    ->select('b.brg_id', 'b.nm_brg', 'a.satuan1', 'a.satuan2', 'a.konversi1', 'a.spek_id', 'a.spek')
                    ->where('a.brg_id', $request->brg_id)
                    ->where('a.status', 0)
                    ->get();

                return DataTables::of($speks)->make(true);
            }
        }
        return view('pages.penerimaan-supplier.create', compact('user','no_trm', 'gudang_id', 'suppliers'));
    }

    public function store(Request $request)
    {
        $no_trm = $request->no_trm;
        $gudang_id = $request->gudang_id;

        TrTrmSup::create([
            'no_trm' => $no_trm,
            'tgl_trm' => $request->tgl_trm,
            'tgl_jth_tmp' => $request->tgl_jth_tmp,
            'no_sj' => $request->no_sj,
            'supplier_id' => $request->supplier_id,
            'gudang_id' => $gudang_id,
        ]);

        foreach ($request->items as $item) {
            TrTrmSupDetail::create([
                'no_trm' => $no_trm,
                'brg_id' => $item['brg_id'],
                'spek_id' => $item['spek_id'],
                'qty_beli' => $item['qty_beli'],
                'satuan_beli' => $item['satuan_beli'],
                'qty_std' => $item['qty_std'],
                'satuan_std' => $item['satuan_std'],
                'ket' => $item['ket'],
            ]);

            $id = str_pad(TrStok::count() + 1, 3, '0', STR_PAD_LEFT);
            $stok_id = "{$gudang_id}/{$item['brg_id']}/{$id}";
            $suppliers = Supplier::where('supplier_id', $request->supplier_id)->value('nama');
            $ket = "Penerimaan barang dari " . $suppliers;

            $masuk = $item['qty_beli'];

            TrStok::create([
                'stok_id' => $stok_id,
                'tgl' => $request->tgl_trm,
                'brg_id' => $item['brg_id'],
                'gudang_id' => $gudang_id,
                'doc_id' => $no_trm,
                'ket' => $ket,
                'awal' => 0,
                'masuk' => $masuk,
                'keluar' => 0,
                'akhir' => 0,
                'cek' => 1,
            ]);
        }
        return redirect()->route('penerimaan-supplier')->with('success', 'Data stok masuk berhasil ditambahkan.');
    }


    public function edit(Request $request, string $no_trm)
    {
        $user = $this->userAuth();
        $no_trms = str_replace('-', '/', $no_trm);

        $data_supplier = DB::table('tr_trmsup as a')
            ->join('m_supplier as b', 'a.supplier_id', '=', 'b.supplier_id')
            ->where('a.no_trm', $no_trms)
            ->value('a.supplier_id');

        $suppliers = Supplier::where('status', 0)->get();

        $tgl_trm = TrTrmSup::where('no_trm', $no_trms)
            ->where('status', 0)
            ->value('tgl_trm');

        $tgl_jth_tmp = TrTrmSup::where('no_trm', $no_trms)
            ->where('status', 0)
            ->value('tgl_jth_tmp');

        $no_sj = TrTrmSup::where('no_trm', $no_trms)
            ->where('status', 0)
            ->value('no_sj');

        if ($request->ajax()) {
            $type = $request->input('type');

            if ($type == 'details') {
                $details = DB::table('tr_trmsup_detail as a')
                    ->join('m_brg as b', 'a.brg_id', '=', 'b.brg_id')
                    ->join('m_brg_spek as c', 'a.spek_id', '=', 'c.spek_id')
                    ->select('b.brg_id', 'b.nm_brg as nama', 'a.qty_beli', 'a.satuan_beli', 'a.qty_std', 'a.satuan_std', 'c.konversi1')
                    ->where('no_trm', $no_trms)
                    ->where('a.status', 0)
                    ->get();

                return DataTables::of($details)->make(true);

            } elseif ($type == 'barangs') {
                $barangs = Barang::where('status', 0)
                    ->orderBy('brg_id', 'asc')
                    ->get();

                return DataTables::of($barangs)->make(true);

            }
        }
        return view('pages.penerimaan-supplier.edit', compact('user', 'no_sj', 'data_supplier', 'suppliers', 'tgl_trm', 'tgl_jth_tmp',  'no_trm', 'no_trms'));
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $no_trm)
    {
        $no_trms = str_replace('-', '/', $no_trm);
        $responseMessage = '';

        if (!empty($request->items)) {
            foreach ($request->items as $item) {
                $data_tr_trmsup_detail = TrTrmSupDetail::where('no_trm', $no_trms)
                    ->where('brg_id', $item['brg_id'])
                    ->first();

                if ($data_tr_trmsup_detail) {
                    $nama = Barang::where('brg_id', $item['brg_id'])->value('nm_brg');
                    $data_tr_trmsup_detail->update([
                        'qty_beli' => $item['qty_beli'],
                        'qty_std' => $item['qty_std']
                    ]);
                    $responseMessage = 'Data ' . $nama . ' berhasil diubah. Menjadi Qty : ' . $item['qty_beli'];
                }
            }
        } else {
            $data_tr_trmsup = TrTrmSup::where('no_trm', $no_trms)->first();
            $data_tr_trmsup->update([
                'tgl_trm' => $request->tgl_trm,
                'tgl_jth_tmp' => $request->tgl_jth_tmp,
                'no_sj' => $request->no_sj,
                'supplier_id' => $request->supplier_id,
            ]);
            $responseMessage = 'Data transaksi berhasil diubah.';
        }

        return response()->json(['success' => true, 'message' => $responseMessage], 200);
    }


    public function detail(Request $request)
    {
        $details = DB::table('tr_trmsup as a')
            ->join('tr_trmsup_detail as b', 'a.no_trm', '=', 'b.no_trm')
            ->join('m_brg as c', 'b.brg_id', '=', 'c.brg_id')
            ->join('m_brg_spek as d', 'b.spek_id', '=', 'd.spek_id')
            ->where('a.no_trm', $request->no_trm)
            ->where('a.status', 0)
            ->where('b.status', 0)
            ->select('c.nm_brg','d.spek', 'b.qty_beli', 'b.satuan_beli')
            ->get();

        return DataTables::of($details)->make(true);
    }
}
