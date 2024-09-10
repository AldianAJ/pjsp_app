<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class DetailStokMasuk extends Model
{
    protected $table = 'tr_trmsup_detail';
    protected $guarded = [];

    public function stok_masuk()
    {
        return $this->belongsTo(StokMasuk::class, 'no_trm');
    }
}
