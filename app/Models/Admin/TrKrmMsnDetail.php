<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class TrKrmMsnDetail extends Model
{
    protected $table = 'tr_krmmsn_detail';
    protected $guarded = [];


    public function tr_krmmsn()
    {
        return $this->belongsTo(TrKrmMsn::class, 'no_krmmsn');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'brg_id');
    }

}
