<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class DetailPermintaanSKM extends Model
{
    protected $table = 'tr_reqskm_detail';
    protected $guarded = [];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'brg_id');
    }
}
