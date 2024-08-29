<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mesin extends Model
{
    use HasFactory;

    protected $table = 'm_mesin';
    protected $primaryKey = 'mesin_id';
    public $incrementing = false;

    protected $fillable = [
        'mesin_id',
        'jenis_id',
        'nama',
    ];

    public static function generateMesinId()
    {
        $lastId = self::max('mesin_id');
        $lastId = $lastId ? (int) str_replace("MKREG", "", $lastId) : 0;
        $newId = str_pad($lastId + 1, 3, "0", STR_PAD_LEFT);

        return "MKREG{$newId}";
    }
}

