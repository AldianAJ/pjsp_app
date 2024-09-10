<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class DetailPengirimanSKM extends Model
{
    protected $table = 'tr_krmmsn_detail';
    protected $guarded = [];


    public function pengiriman()
    {
        return $this->belongsTo(PengirimanSKM::class, 'no_krmmsn');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'brg_id');
    }

}
