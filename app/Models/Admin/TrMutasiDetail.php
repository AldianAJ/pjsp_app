<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class TrMutasiDetail extends Model
{
    protected $table = 'tr_mutasi_detail';
    protected $guarded = [];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'brg_id');
    }
}
