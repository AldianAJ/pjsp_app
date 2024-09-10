<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $table = 'm_brg';
    protected $primaryKey = 'brg_id';
    public $incrementing = false;
    protected $guarded = [];


    public function detailPermintaan()
    {
        return $this->hasMany(DetailPermintaanSKM::class);
    }
}
