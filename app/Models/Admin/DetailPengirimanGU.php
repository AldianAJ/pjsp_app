<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class DetailPengirimanGU extends Model
{
    protected $table = 'tr_krmskm_detail';
    protected $guarded = [];


    public function pengiriman()
    {
        return $this->belongsTo(PengirimanGU::class, 'no_krmskm');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'brg_id');
    }

}
