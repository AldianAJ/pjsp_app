<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StokMasuk extends Model
{
    use HasFactory;

    protected $table = 'tr_trmsup';
    protected $guarded = [];


    public function detail_stok_masuk()
    {
        return $this->belongsTo(DetailStokMasuk::class, 'no_trm', 'no_trm');
    }
}
