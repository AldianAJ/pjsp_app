<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class StokMasuk extends Model
{
    use HasFactory;

    protected $table = 'tr_terima_supplier';
    protected $primaryKey = 'no_trm';
    public $incrementing = false;
    protected $fillable = [
        'no_trm',
        'no_sj',
        'supplier_id',
        'tgl'
    ];


    public static function generateNoTrm()
    {
        $lastId = self::max('no_trm');
        $lastId = $lastId ? (int) substr($lastId, -4) : 0;

        $newId = str_pad($lastId + 1, 3, "0", STR_PAD_LEFT);
        $date = date('d/m');

        return "RCV/GU/{$date}/{$newId}";
    }
}

