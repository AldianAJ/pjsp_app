<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class PengirimanGU extends Model
{
    protected $table = 'tr_krmskm';
    protected $guarded = [];

    public function detail_pengiriman()
    {
        return $this->hasMany(DetailPengirimanGU::class);
    }
}

