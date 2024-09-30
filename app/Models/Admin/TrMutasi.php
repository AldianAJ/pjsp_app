<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class TrMutasi extends Model
{
    protected $table = 'tr_mutasi';
    protected $primaryKey = 'mutasi_id';
    public $incrementing = false;
    protected $guarded = [];

    public function tr_mutasi_detail()
    {
        return $this->hasMany(TrMutasiDetail::class);
    }
}
