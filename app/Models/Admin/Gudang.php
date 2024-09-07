<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gudang extends Model
{
    use HasFactory;

    protected $table = 'm_gudang';
    protected $guarded = [];

    public static function generateGudangId()
    {
        $lastId = self::max('gudang_id');
        $lastId = $lastId ? (int) str_replace("GU", "", $lastId) : 0;
        $newId = str_pad($lastId + 1, 3, "0", STR_PAD_LEFT);

        return "GU{$newId}";
    }

    public function jenis_mesin()
    {
        return $this->belongsTo(Mesin::class, 'mesin_id');
    }
}

