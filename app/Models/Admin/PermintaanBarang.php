<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PermintaanBarang extends Model
{
    use HasFactory;

    protected $table = 'tr_minta_brg';
    protected $primaryKey = 'no_doc_minta';
    public $incrementing = false;

    protected $fillable = [
        'no_doc_minta', 'tgl_minta', 'status'
    ];

    public static function generatePermintaanBarangDoc($user_id)
    {
        $now = Carbon::now();
        $day = $now->format('d');
        $month = $now->format('m');

        $latest_minta_brg_id = DB::table('tr_minta_brg')
            ->whereDate('tgl_minta', $now->toDateString())
            ->where('user_id', $user_id)
            ->max('minta_brg_id');

        $increment = 1;

        if ($latest_minta_brg_id) {
            $lastIncrement = (int) substr($latest_minta_brg_id, -4);
            $increment = $lastIncrement + 1;
        }

        $incrementId = str_pad($increment, 4, '0', STR_PAD_LEFT);
        $newPermintaanBarangId = "FPB/SKM/{$day}/{$month}/{$incrementId}";

        return $newPermintaanBarangId;
    }
}
