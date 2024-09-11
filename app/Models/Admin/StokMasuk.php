<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class StokMasuk extends Model
{
    protected $table = 'tr_trmsup';
    protected $guarded = [];


    public function detail_stok_masuk()
    {
        return $this->hasMany(DetailStokMasuk::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
}
