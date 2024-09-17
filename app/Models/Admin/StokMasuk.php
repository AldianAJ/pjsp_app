<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class StokMasuk extends Model
{
    protected $table = 'tr_trmsup';
    protected $guarded = [];
    protected $primaryKey = 'no_trm';
    public $incrementing = false;


    public function detail_stok_masuk()
    {
        return $this->hasMany(DetailStokMasuk::class, 'no_trm', 'no_trm');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
}
