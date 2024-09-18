<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $table = 'm_brg';
    protected $primaryKey = 'brg_id';
    public $incrementing = false;
    protected $guarded = [];


    public function tr_reqskm_detail()
    {
        return $this->hasMany(TrReqSKMDetail::class);
    }

    public function tr_trmsup()
    {
        return $this->hasMany(TrTrmSup::class);
    }
}
