<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class PengirimanBarang extends Model
{
    use HasFactory;

    protected $table = 'tr_kirim_brg';
    protected $primaryKey = 'no_doc_kirim';
    public $incrementing = false;

    protected $fillable = [
        'no_doc_kirim', 'no_doc_minta', 'tgl_kirim', 'tgl_terima', 'status'
    ];

    public static function generatePengirimanBarangDoc($user_id)
    {
        $now = Carbon::now();
        $day = $now->format('d');
        $month = $now->format('m');

        $maxId = DB::table('krm_brgs as pg')
            ->join('tr_minta_brg as pm', 'pg.minta_brg_id', '=', 'pm.minta_brg_id')
            ->whereDay('pg.tgl_krm', $day)
            ->whereMonth('pg.tgl_krm', $month)
            ->where('pm.id', $user_id)
            ->max(DB::raw('SUBSTRING(pg.krm_brg_id, -4)'));

        $nextId = str_pad((int) $maxId + 1, 4, '0', STR_PAD_LEFT);

        $newPengirimanBarangId = "SJ/GU/{$day}/{$month}/{$nextId}";

        return $newPengirimanBarangId;
    }
}

