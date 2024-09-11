<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class PengirimanSKM extends Model
{
    protected $table = 'tr_krmmsn';
    protected $guarded = [];

    public function detail_pengiriman()
    {
        return $this->hasMany(DetailPengirimanSKM::class);
    }
}

