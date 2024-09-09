<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPermintaanSKM extends Model
{
    use HasFactory;

    protected $table = 'tr_reqskm_detail';
    protected $guarded = [];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'brg_id');
    }
}
