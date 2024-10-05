<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\LogProd;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class LogProdController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $tgl = Carbon::parse($request->tgl);

            $logprod = DB::table('tr_log_prod as a')
                ->join('tr_target_mesin as b', 'a.msn_trgt_id', '=', 'b.msn_trgt_id')
                ->join('tr_target_shift as c', 'b.shift_id', '=', 'c.shift_id')
                ->join('tr_target_harian as d', 'c.harian_id', '=', 'd.harian_id')
                ->join('tr_target_week as e', 'd.week_id', '=', 'e.week_id')
                ->join('m_mesin as f', 'b.mesin_id', '=', 'f.mesin_id')
                ->join('m_brg as g', 'e.brg_id', '=', 'g.brg_id')
                ->select('a.logprod_id', 'a.msn_trgt_id', 'b.mesin_id', 'd.tgl', 'a.pic')
                ->selectRaw('CONCAT_WS(" - ",a.waktu_mulai,a.waktu_selesai) AS waktu, f.nama as mesin, g.nm_brg as produksi');

            if ($request->has('tgl') && $request->tgl != '') {
                $logprod->where('d.tgl', $tgl);
            }

            $logprod->get();
            return DataTables::of($logprod)->make(true);
        }
        return view('pages.log-prod.index');
    }

    public function create(Request $request)
    {
        if ($request->ajax()) {
            $type = $request->input('type');
            if ($type == 'shifts') {
                $date = $request->input('date');
                $harian = DB::table('tr_target_harian')->where('tgl', $date)->first();

                $shifts = DB::table('tr_target_shift')
                    ->select('shift_id', 'shift')
                    ->where('harian_id', $harian->harian_id)->get();

                return DataTables::of($shifts)->make(true);
            } elseif ($type == 'machines') {
                $shiftId = $request->input('shift_id');
                $machines = DB::table('tr_target_mesin as a')
                    ->join('tr_target_shift as b', 'a.shift_id', '=', 'b.shift_id')
                    ->join('m_mesin as c', 'a.mesin_id', '=', 'c.mesin_id')
                    ->select('a.msn_trgt_id', 'c.nama')
                    ->where('a.shift_id', $shiftId)->get();

                return DataTables::of($machines)->make(true);
            }
        }

        return view('pages.log-prod.create');
    }

    public function store(Request $request)
    {
        $data = $request->all();

        $logprod_id = 'LBP' . str_pad(LogProd::count() + 1, 3, '0', STR_PAD_LEFT);

        foreach ($request->items as $items) {
            DB::table('tr_log_prod')->insert([
                'logprod_id' => $logprod_id,
                'msn_trgt_id' => $data['msn_trgt_id'],
                'tgl' => date('Y-m-d', strtotime($data['tgl'])),
                'trouble' => $items['trouble'],
                'penanganan' => $items['penanganan'],
                'waktu_mulai' => $items['waktu_mulai'],
                'waktu_selesai' => $items['waktu_selesai'],
                'lost_time' => $items['lost_time'],
                'ket' => $items['ket'],
                'pic' => $items['pic'],
            ]);
        }

        return redirect()->route('log-produksi');
    }

    public function edit(Request $request)
    {
        $id = $request->id;
        $data = DB::table('tr_log_prod')->where('logprod_id', $id)->first();

        return view('pages.log-prod.edit', compact('data'));
    }

    public function update(Request $request)
    {
        $id = $request->logprod_id;
        $data = $request->all();
        DB::table('tr_log_prod')->where('logprod_id', $id)
            ->update([
                'trouble' => $data['trouble'],
                'penanganan' => $data['penanganan'],
                'waktu_mulai' => $data['waktu_mulai'],
                'waktu_selesai' => $data['waktu_selesai'],
                'lost_time' => $data['lost_time'],
                'ket' => $data['ket'],
                'pic' => $data['pic'],
            ]);

        return redirect()->route('log-produksi');
    }

    public function detail(Request $request)
    {
        $id = $request->id;
        $data = DB::table('tr_log_prod')->where('logprod_id', $id)->first();

        return view('pages.log-prod.detail', compact('data'));
    }
}
