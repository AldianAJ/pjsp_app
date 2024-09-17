<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class StokBarang extends Model
{
    protected $table = 'tr_stok';
    protected $guarded = [];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'brg_id');
    }
}
