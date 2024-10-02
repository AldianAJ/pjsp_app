<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Admin\Shift;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\Admin\TargetMesin;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Admin\Closing;
use App\Models\Admin\DetailClosing;
use App\Models\Admin\Mesin;
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
        $tgl = Carbon::parse($request->tgl); // ganti dengan tanggal yang sesuai

        $mesins = Mesin::where('status', 0)->get();

        if ($request->ajax()) {

            $targetMesin = DB::table('tr_target_mesin as a')
                ->join('m_mesin as b', 'a.mesin_id', '=', 'b.mesin_id')
                ->join('tr_target_shift as c', 'a.shift_id', '=', 'c.shift_id')
                ->join('tr_target_harian as d', 'c.harian_id', '=', 'd.harian_id')
                ->join('tr_target_week as e', 'd.week_id', '=', 'e.week_id')
                ->join('m_brg as f', 'e.brg_id', '=', 'f.brg_id')
                ->where('d.tgl', $tgl)
                // ->where('a.status', 0)
                ->select('a.msn_trgt_id', 'a.qty', 'a.mesin_id', 'c.shift', 'b.nama', 'b.jenis_id', 'f.nm_brg', 'e.brg_id')
                ->orderBy('a.msn_trgt_id', 'desc');
            if ($request->has('msn') && $request->msn != '') {
                $targetMesin->where('a.mesin_id', 'like', $request->msn . '%');
            }
            $targetMesin->get();
            return DataTables::of($targetMesin)
                ->addColumn('action', function ($object) use ($path) {
                    $cek = Closing::where('msn_trgt_id', $object->msn_trgt_id)->exists();
                    $html = '<button class="btn btn-info btn-process waves-effect waves-light me-1" data-msn-trgt-id="' . $object->msn_trgt_id . '">'
                        . '<i class="bx bx-transfer-alt align-middle me-2 font-size-18"></i> Proses</button>';
                    if ($cek) {
                        $html = '<button class="btn btn-secondary btn-detail waves-effect waves-light me-1" data-msn-trgt-id="' . $object->msn_trgt_id . '">'
                            . '<i class="bx bx-detail align-middle me-2 font-size-18"></i> Detail</button>';
                        $html .= '<button class="btn btn-success btn-edit waves-effect waves-light me-1" data-msn-trgt-id="' . $object->msn_trgt_id . '">'
                            . '<i class="bx bx-edit align-middle me-2 font-size-18"></i> Edit</button>';
                    }

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
        $msn_trgt_id = $request->trgt_id;
        $produk = $request->produk;

        $closing_id = 'CL' . str_pad(Closing::count() + 1, 3, '0', STR_PAD_LEFT);
        $closing = Closing::create([
            'closing_id' => $closing_id,
            'msn_trgt_id' => $msn_trgt_id,
            'jenis' => '1',
        ]);
        $id = $closing->closing_id;
        foreach ($request->sisaHasil as $item) {
            $brg_id = DB::table('tmp_produk_material as a')
                ->join('m_brg_spek as b', 'b.spek_id', '=', 'a.brg_id')
                ->join('m_brg as c', 'c.brg_id', '=', 'b.brg_id')
                ->where('a.brg_prod_id', $produk)
                ->where('a.tahap', 1)
                ->select('a.brg_id')
                ->first()->brg_id;

            DetailClosing::create([
                'closing_id' => $id,
                'brg_id' => $brg_id,
                'qty' => $item['value'],
                'kode' => 1,
                'satuan' => $item['name'],
                'cek' => 1,
            ]);
        }
        foreach ($request->reject as $item) {
            $brg_id = DB::table('tmp_produk_material as a')
                ->join('m_brg_spek as b', 'b.spek_id', '=', 'a.brg_id')
                ->join('m_brg as c', 'c.brg_id', '=', 'b.brg_id')
                ->where('a.brg_prod_id', $produk)
                ->where('a.tahap', 2)
                ->where('c.nm_brg', 'like', '%' . $item['name'] . '%')
                ->orWhere('b.spek', 'like', '%' . $item['name'] . '%')
                ->value('a.brg_id');

            DetailClosing::create([
                'closing_id' => $id,
                'brg_id' => $brg_id,
                'qty' => $item['value'],
                'kode' => 2,
                'cek' => 1,
            ]);
        }
        foreach ($request->bahan as $item) {
            $brg_id = DB::table('tmp_produk_material as a')
                ->join('m_brg_spek as b', 'b.spek_id', '=', 'a.brg_id')
                ->join('m_brg as c', 'c.brg_id', '=', 'b.brg_id')
                ->where('a.brg_prod_id', $produk)
                ->where('a.tahap', 3)
                ->where('c.nm_brg', 'like', '%' . $item['name'] . '%')
                ->value('a.brg_id');

            DetailClosing::create([
                'closing_id' => $id,
                'brg_id' => $brg_id,
                'qty' => $item['value'],
                'kode' => 3,
                'cek' => 1,
            ]);
        }
        TargetMesin::where('msn_trgt_id', $msn_trgt_id)->update([
            'status' => 1,
        ]);
        return response()->json(['success' => true, 'message' => 'Berhasil ditambahkan.'], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeHlp(Request $request)
    {
        $request->sisaHasil;
        $request->reject;
        $request->bahan;
        $msn_trgt_id = $request->trgt_id;
        $produk = $request->produk;

        $closing_id = 'CL' . str_pad(Closing::count() + 1, 3, '0', STR_PAD_LEFT);
        $closing = Closing::create([
            'closing_id' => $closing_id,
            'msn_trgt_id' => $msn_trgt_id,
            'jenis' => '2', //hlp
        ]);
        $id = $closing->closing_id;
        foreach ($request->sisaHasil as $item) {
            $item['name'] = str_replace('_', ' ', $item['name']);

            $brg_id = DB::table('tmp_produk_material as a')
                ->join('m_brg_spek as b', 'b.spek_id', '=', 'a.brg_id')
                ->join('m_brg as c', 'c.brg_id', '=', 'b.brg_id')
                ->where('a.brg_prod_id', $produk)
                ->where('a.tahap', 1)
                ->select('a.brg_id')
                ->first()->brg_id;

            DetailClosing::create([
                'closing_id' => $id,
                'brg_id' => $brg_id,
                'qty' => $item['value'],
                'kode' => 1,
                'satuan' => $item['name'],
                'cek' => 1,
            ]);
        }
        foreach ($request->reject as $item) {
            $item['name'] = str_replace('_', ' ', $item['name']);

            $brg_id = DB::table('tmp_produk_material as a')
                ->join('m_brg_spek as b', 'b.spek_id', '=', 'a.brg_id')
                ->join('m_brg as c', 'c.brg_id', '=', 'b.brg_id')
                ->where('a.brg_prod_id', $produk)
                ->where('a.tahap', 2)
                ->where('c.nm_brg', 'like', '%' . $item['name'] . '%')
                ->value('a.brg_id');

            DetailClosing::create([
                'closing_id' => $id,
                'brg_id' => $brg_id,
                'qty' => $item['value'],
                'kode' => 2,
                'satuan' => $item['name'],
                'cek' => 1,
            ]);
        }
        foreach ($request->bahan as $item) {
            $item['name'] = str_replace('_', ' ', $item['name']);

            $brg_id = DB::table('tmp_produk_material as a')
                ->join('m_brg_spek as b', 'b.spek_id', '=', 'a.brg_id')
                ->join('m_brg as c', 'c.brg_id', '=', 'b.brg_id')
                ->where('a.brg_prod_id', $produk)
                ->where('a.tahap', 3)
                ->where('c.nm_brg', 'like', '%' . $item['name'] . '%')
                ->value('a.brg_id');

            DetailClosing::create([
                'closing_id' => $id,
                'brg_id' => $brg_id,
                'qty' => $item['value'],
                'kode' => 3,
                'satuan' => $item['name'],
                'cek' => 1,
            ]);
        }
        TargetMesin::where('msn_trgt_id', $msn_trgt_id)->update([
            'status' => 1,
        ]);
        return response()->json(['success' => true, 'message' => 'Berhasil ditambahkan.'], 200);
    }

    /**
     * Display the specified resource.
     */
    public function detail(Request $request)
    {
        $user = $this->userAuth();
        $id = $request->closing_id;
        $jenis = $request->jenis;
        $data = DB::table('tr_closing_detail as a')
            ->join('tr_closing as b', 'a.closing_id', '=', 'b.closing_id')
            ->join('m_brg_spek as c', 'a.brg_id', '=', 'c.spek_id')
            ->join('m_brg as d', 'd.brg_id', '=', 'c.brg_id')
            ->where('b.msn_trgt_id', $id)
            ->where('b.jenis', $jenis)
            ->select('d.nm_brg', 'c.spek', 'a.*')
            ->selectRaw("SUBSTRING_INDEX(d.nm_brg, ' ', 1) as nm_brg2")
            ->get();

        $formData = [
            'TRAY' => $data->where('satuan', 'TRAY')->first()->qty ?? 0,
            'BTG' => $data->where('satuan', 'BTG')->first()->qty ?? 0,
            'btg_reject' => $data->where('satuan', 'btg_reject')->first()->qty ?? 0,
        ];
        $formReject = [
            'debu' => $data->where('kode', 2)->where('spek', 'Debu')->first()->qty ?? 0,
            'sapon' => $data->where('kode', 2)->where('spek', 'Sapon')->first()->qty ?? 0,
            'cp' => $data->where('kode', 2)->where('nm_brg2', 'CP')->first()->qty ?? 0,
            'filter' => $data->where('kode', 2)->where('nm_brg2', 'Filter')->first()->qty ?? 0,
            'ctp' => $data->where('kode', 2)->where('nm_brg2', 'CTP')->first()->qty ?? 0,
        ];
        $formBahan = [
            'tsg' => $data->where('kode', 3)->where('nm_brg2', 'TSG')->first()->qty ?? 0,
            'cp' => $data->where('kode', 3)->where('nm_brg2', 'CP')->first()->qty ?? 0,
            'filter' => $data->where('kode', 3)->where('nm_brg2', 'Filter')->first()->qty ?? 0,
            'ctp' => $data->where('kode', 3)->where('nm_brg2', 'CTP')->first()->qty ?? 0,
        ];

        return view('pages.closing.detail-maker', compact('user', 'formData', 'formReject', 'formBahan'))->render();
    }
    /**
     * Display the specified resource.
     */
    public function detailHLP(Request $request)
    {
        $user = $this->userAuth();
        $id = $request->closing_id;
        $jenis = $request->jenis;
        $data = DB::table('tr_closing_detail as a')
            ->join('tr_closing as b', 'a.closing_id', '=', 'b.closing_id')
            ->join('m_brg_spek as c', 'a.brg_id', '=', 'c.spek_id')
            ->join('m_brg as d', 'd.brg_id', '=', 'c.brg_id')
            ->where('b.msn_trgt_id', $id)
            ->where('b.jenis', $jenis)
            ->select('d.nm_brg', 'c.spek', 'a.*')
            ->selectRaw("SUBSTRING_INDEX(d.nm_brg, ' ', 1) as nm_brg2")
            ->get();

        $formData = [
            'karton' => $data->where('satuan', 'karton')->first()->qty ?? 0,
            'ball' => $data->where('satuan', 'ball')->first()->qty ?? 0,
            'slop' => $data->where('satuan', 'slop')->first()->qty ?? 0,
            'opp_pack' => $data->where('satuan', 'opp pack')->first()->qty ?? 0,
            'npc' => $data->where('satuan', 'npc')->first()->qty ?? 0,
            'pack_reject' => $data->where('satuan', 'pack reject')->first()->qty ?? 0,
        ];
        $formReject = [
            'foil' => $data->where('kode', 2)->where('satuan', 'foil')->first()->qty ?? 0,
            'inner' => $data->where('kode', 2)->where('satuan', 'inner')->first()->qty ?? 0,
            'etiket' => $data->where('kode', 2)->where('satuan', 'etiket')->first()->qty ?? 0,
            'pc' => $data->where('kode', 2)->where('satuan',  'pc')->first()->qty ?? 0,
            'opp_pack' => $data->where('kode', 2)->where('satuan', 'opp pack')->first()->qty ?? 0,
            'teartape' => $data->where('kode', 2)->where('satuan', 'teartape')->first()->qty ?? 0,
            'opp_slop' => $data->where('kode', 2)->where('satuan', 'opp slop')->first()->qty ?? 0,
            'barcode_slop' => $data->where('kode', 2)->where('satuan', 'barcode slop')->first()->qty ?? 0,
            'kertas_ball' => $data->where('kode', 2)->where('satuan', 'kertas ball')->first()->qty ?? 0,
            'cap_ball' => $data->where('kode', 2)->where('satuan', 'cap ball')->first()->qty ?? 0,
            'karton' => $data->where('kode', 2)->where('satuan', 'karton')->first()->qty ?? 0,
        ];
        $formBahan = [
            'foil' => $data->where('kode', 3)->where('satuan', 'foil')->first()->qty ?? 0,
            'inner' => $data->where('kode', 3)->where('satuan', 'inner')->first()->qty ?? 0,
            'etiket' => $data->where('kode', 3)->where('satuan', 'etiket')->first()->qty ?? 0,
            'pc' => $data->where('kode', 3)->where('satuan',  'pc')->first()->qty ?? 0,
            'opp_pack' => $data->where('kode', 3)->where('satuan', 'opp pack')->first()->qty ?? 0,
            'teartape' => $data->where('kode', 3)->where('satuan', 'teartape')->first()->qty ?? 0,
            'opp_slop' => $data->where('kode', 3)->where('satuan', 'opp slop')->first()->qty ?? 0,
            'barcode_slop' => $data->where('kode', 3)->where('satuan', 'barcode slop')->first()->qty ?? 0,
            'kertas_ball' => $data->where('kode', 3)->where('satuan', 'kertas ball')->first()->qty ?? 0,
            'cap_ball' => $data->where('kode', 3)->where('satuan', 'cap ball')->first()->qty ?? 0,
            'karton' => $data->where('kode', 3)->where('satuan', 'karton')->first()->qty ?? 0,
            'batangan' => $data->where('kode', 3)->where('satuan', 'batangan')->first()->qty ?? 0,
        ];

        return view('pages.closing.detail-hlp', compact('user', 'formData', 'formReject', 'formBahan'))->render();
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
        $user = $this->userAuth();
        $id = $request->closing_id;
        $jenis = $request->jenis;
        $data = DB::table('tr_closing_detail as a')
            ->join('tr_closing as b', 'a.closing_id', '=', 'b.closing_id')
            ->join('m_brg_spek as c', 'a.brg_id', '=', 'c.spek_id')
            ->join('m_brg as d', 'd.brg_id', '=', 'c.brg_id')
            ->where('b.msn_trgt_id', $id)
            ->where('b.jenis', $jenis)
            ->select('d.nm_brg', 'c.spek', 'a.*')
            ->selectRaw("SUBSTRING_INDEX(d.nm_brg, ' ', 1) as nm_brg2")
            ->get();

        $formData = [
            'TRAY' => $data->where('satuan', 'TRAY')->first()->qty ?? 0,
            'BTG' => $data->where('satuan', 'BTG')->first()->qty ?? 0,
            'btg_reject' => $data->where('satuan', 'btg_reject')->first()->qty ?? 0,
        ];
        $formReject = [
            'debu' => $data->where('kode', 2)->where('spek', 'Debu')->first()->qty ?? 0,
            'sapon' => $data->where('kode', 2)->where('spek', 'Sapon')->first()->qty ?? 0,
            'cp' => $data->where('kode', 2)->where('nm_brg2', 'CP')->first()->qty ?? 0,
            'filter' => $data->where('kode', 2)->where('nm_brg2', 'Filter')->first()->qty ?? 0,
            'ctp' => $data->where('kode', 2)->where('nm_brg2', 'CTP')->first()->qty ?? 0,
        ];
        $formBahan = [
            'tsg' => $data->where('kode', 3)->where('nm_brg2', 'TSG')->first()->qty ?? 0,
            'cp' => $data->where('kode', 3)->where('nm_brg2', 'CP')->first()->qty ?? 0,
            'filter' => $data->where('kode', 3)->where('nm_brg2', 'Filter')->first()->qty ?? 0,
            'ctp' => $data->where('kode', 3)->where('nm_brg2', 'CTP')->first()->qty ?? 0,
        ];

        return view('pages.closing.edit-maker', compact('user', 'formData', 'formReject', 'formBahan'))->render();
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function editHlp(Request $request)
    {
        $user = $this->userAuth();
        $id = $request->closing_id;
        $jenis = $request->jenis;
        $data = DB::table('tr_closing_detail as a')
            ->join('tr_closing as b', 'a.closing_id', '=', 'b.closing_id')
            ->join('m_brg_spek as c', 'a.brg_id', '=', 'c.spek_id')
            ->join('m_brg as d', 'd.brg_id', '=', 'c.brg_id')
            ->where('b.msn_trgt_id', $id)
            ->where('b.jenis', $jenis)
            ->select('d.nm_brg', 'c.spek', 'a.*')
            ->selectRaw("SUBSTRING_INDEX(d.nm_brg, ' ', 1) as nm_brg2")
            ->get();

        $formData = [
            'karton' => $data->where('satuan', 'karton')->first()->qty ?? 0,
            'ball' => $data->where('satuan', 'ball')->first()->qty ?? 0,
            'slop' => $data->where('satuan', 'slop')->first()->qty ?? 0,
            'opp_pack' => $data->where('satuan', 'opp pack')->first()->qty ?? 0,
            'npc' => $data->where('satuan', 'npc')->first()->qty ?? 0,
            'pack_reject' => $data->where('satuan', 'pack reject')->first()->qty ?? 0,
        ];
        $formReject = [
            'foil' => $data->where('kode', 2)->where('satuan', 'foil')->first()->qty ?? 0,
            'inner' => $data->where('kode', 2)->where('satuan', 'inner')->first()->qty ?? 0,
            'etiket' => $data->where('kode', 2)->where('satuan', 'etiket')->first()->qty ?? 0,
            'pc' => $data->where('kode', 2)->where('satuan',  'pc')->first()->qty ?? 0,
            'opp_pack' => $data->where('kode', 2)->where('satuan', 'opp pack')->first()->qty ?? 0,
            'teartape' => $data->where('kode', 2)->where('satuan', 'teartape')->first()->qty ?? 0,
            'opp_slop' => $data->where('kode', 2)->where('satuan', 'opp slop')->first()->qty ?? 0,
            'barcode_slop' => $data->where('kode', 2)->where('satuan', 'barcode slop')->first()->qty ?? 0,
            'kertas_ball' => $data->where('kode', 2)->where('satuan', 'kertas ball')->first()->qty ?? 0,
            'cap_ball' => $data->where('kode', 2)->where('satuan', 'cap ball')->first()->qty ?? 0,
            'karton' => $data->where('kode', 2)->where('satuan', 'karton')->first()->qty ?? 0,
        ];
        $formBahan = [
            'foil' => $data->where('kode', 3)->where('satuan', 'foil')->first()->qty ?? 0,
            'inner' => $data->where('kode', 3)->where('satuan', 'inner')->first()->qty ?? 0,
            'etiket' => $data->where('kode', 3)->where('satuan', 'etiket')->first()->qty ?? 0,
            'pc' => $data->where('kode', 3)->where('satuan',  'pc')->first()->qty ?? 0,
            'opp_pack' => $data->where('kode', 3)->where('satuan', 'opp pack')->first()->qty ?? 0,
            'teartape' => $data->where('kode', 3)->where('satuan', 'teartape')->first()->qty ?? 0,
            'opp_slop' => $data->where('kode', 3)->where('satuan', 'opp slop')->first()->qty ?? 0,
            'barcode_slop' => $data->where('kode', 3)->where('satuan', 'barcode slop')->first()->qty ?? 0,
            'kertas_ball' => $data->where('kode', 3)->where('satuan', 'kertas ball')->first()->qty ?? 0,
            'cap_ball' => $data->where('kode', 3)->where('satuan', 'cap ball')->first()->qty ?? 0,
            'karton' => $data->where('kode', 3)->where('satuan', 'karton')->first()->qty ?? 0,
            'batangan' => $data->where('kode', 3)->where('satuan', 'batangan')->first()->qty ?? 0,
        ];
        // dd($data);
        return view('pages.closing.edit-hlp', compact('user', 'formData', 'formReject', 'formBahan'))->render();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $request->sisaHasil;
        $request->reject;
        $request->bahan;
        $msn_trgt_id = $request->trgt_id;
        $produk = $request->produk;

        $closing_id = Closing::where('msn_trgt_id', $msn_trgt_id)->where('jenis', '1')->value('closing_id');

        foreach ($request->sisaHasil as $item) {
            $brg_id = DB::table('tmp_produk_material as a')
                ->join('m_brg_spek as b', 'b.spek_id', '=', 'a.brg_id')
                ->join('m_brg as c', 'c.brg_id', '=', 'b.brg_id')
                ->where('a.brg_prod_id', $produk)
                ->where('a.tahap', 1)
                ->select('a.brg_id')
                ->first()->brg_id;

            DetailClosing::where('closing_id', $closing_id)->where('kode', 1)->where('brg_id', $brg_id)->where('satuan', $item['name'])
                ->update([
                    'qty' => $item['value'],
                    'cek' => 1,
                ]);
        }
        foreach ($request->reject as $item) {
            $brg_id = DB::table('tmp_produk_material as a')
                ->join('m_brg_spek as b', 'b.spek_id', '=', 'a.brg_id')
                ->join('m_brg as c', 'c.brg_id', '=', 'b.brg_id')
                ->where('a.brg_prod_id', $produk)
                ->where('a.tahap', 2)
                ->where('c.nm_brg', 'like', '%' . $item['name'] . '%')
                ->orWhere('b.spek', 'like', '%' . $item['name'] . '%')
                ->value('a.brg_id');

            DetailClosing::where('closing_id', $closing_id)->where('kode', 2)->where('brg_id', $brg_id)
                ->update([
                    'qty' => $item['value'],
                    'cek' => 1,
                ]);
        }
        foreach ($request->bahan as $item) {
            $brg_id = DB::table('tmp_produk_material as a')
                ->join('m_brg_spek as b', 'b.spek_id', '=', 'a.brg_id')
                ->join('m_brg as c', 'c.brg_id', '=', 'b.brg_id')
                ->where('a.brg_prod_id', $produk)
                ->where('a.tahap', 3)
                ->where('c.nm_brg', 'like', '%' . $item['name'] . '%')
                ->value('a.brg_id');

            DetailClosing::where('closing_id', $closing_id)->where('kode', 3)->where('brg_id', $brg_id)
                ->update([
                    'qty' => $item['value'],
                    'cek' => 1,
                ]);
        }
        TargetMesin::where('msn_trgt_id', $msn_trgt_id)->update([
            'status' => 1,
        ]);
        return response()->json(['success' => true, 'message' => 'Berhasil diperbarui.'], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateHlp(Request $request)
    {
        $request->sisaHasil;
        $request->reject;
        $request->bahan;
        $msn_trgt_id = $request->trgt_id;
        $produk = $request->produk;

        $closing_id = Closing::where('msn_trgt_id', $msn_trgt_id)->where('jenis', '2')->first()->closing_id;

        foreach ($request->sisaHasil as $item) {
            $item['name'] = str_replace('_', ' ', $item['name']);

            $brg_id = DB::table('tmp_produk_material as a')
                ->join('m_brg_spek as b', 'b.spek_id', '=', 'a.brg_id')
                ->join('m_brg as c', 'c.brg_id', '=', 'b.brg_id')
                ->where('a.brg_prod_id', $produk)
                ->where('a.tahap', 1)
                ->select('a.brg_id')
                ->first()->brg_id;

            DetailClosing::where('closing_id', $closing_id)->where('kode', 1)->where('brg_id', $brg_id)->where('satuan', $item['name'])
                ->update([
                    'qty' => $item['value'],
                    'cek' => 1,
                ]);
        }
        foreach ($request->reject as $item) {
            $item['name'] = str_replace('_', ' ', $item['name']);

            $brg_id = DB::table('tmp_produk_material as a')
                ->join('m_brg_spek as b', 'b.spek_id', '=', 'a.brg_id')
                ->join('m_brg as c', 'c.brg_id', '=', 'b.brg_id')
                ->where('a.brg_prod_id', $produk)
                ->where('a.tahap', 2)
                ->where('c.nm_brg', 'like', '%' . $item['name'] . '%')
                ->value('a.brg_id');


            DetailClosing::where('closing_id', $closing_id)->where('kode', 2)->where('brg_id', $brg_id)->where('satuan', $item['name'])
                ->update([
                    'qty' => $item['value'],
                    'cek' => 1,
                ]);
        }
        foreach ($request->bahan as $item) {
            $item['name'] = str_replace('_', ' ', $item['name']);

            $brg_id = DB::table('tmp_produk_material as a')
                ->join('m_brg_spek as b', 'b.spek_id', '=', 'a.brg_id')
                ->join('m_brg as c', 'c.brg_id', '=', 'b.brg_id')
                ->where('a.brg_prod_id', $produk)
                ->where('a.tahap', 3)
                ->where('c.nm_brg', 'like', '%' . $item['name'] . '%')
                ->value('a.brg_id');


            DetailClosing::where('closing_id', $closing_id)->where('kode', 3)->where('brg_id', $brg_id)->where('satuan', $item['name'])
                ->update([
                    'qty' => $item['value'],
                    'cek' => 1,
                ]);
        }
        TargetMesin::where('msn_trgt_id', $msn_trgt_id)->update([
            'status' => 1,
        ]);
        return response()->json(['success' => true, 'message' => 'Berhasil diperbarui.'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
