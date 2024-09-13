<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $table = 'm_brg';
    protected $primaryKey = 'brg_id';
    public $incrementing = false;
    protected $guarded = [];


    public function detail_permintaan()
    {
        return $this->hasMany(DetailPermintaanSKM::class);
    }

    public function detail_stok_masuk()
    {
        return $this->hasMany(DetailStokMasuk::class);
    }
}
