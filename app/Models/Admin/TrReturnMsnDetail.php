<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class TrReturnMsnDetail extends Model
{
    protected $table = 'tr_returnmsn_detail';
    protected $guarded = [];


    public function tr_returnmsn()
    {
        return $this->belongsTo(TrReturnMsn::class, 'no_returnmsn');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'brg_id');
    }

}
