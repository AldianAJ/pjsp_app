<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class TrKrmSKMDetail extends Model
{
    protected $table = 'tr_krmskm_detail';
    protected $guarded = [];


    public function tr_krmskm()
    {
        return $this->belongsTo(TrKrmSKM::class, 'no_krmskm');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'brg_id');
    }

}
