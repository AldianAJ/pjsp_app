<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class TrTrmSupDetail extends Model
{
    protected $table = 'tr_trmsup_detail';
    protected $guarded = [];

    public function tr_trmsup()
    {
        return $this->belongsTo(TrTrmSup::class, 'no_trm', 'no_trm');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'brg_id');
    }
}
