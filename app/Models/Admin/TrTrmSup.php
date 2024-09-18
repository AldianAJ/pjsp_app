<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class TrTrmSup extends Model
{
    protected $table = 'tr_trmsup';
    protected $guarded = [];
    protected $primaryKey = 'no_trm';
    public $incrementing = false;


    public function tr_trmsup_detail()
    {
        return $this->hasMany(TrTrmSupDetail::class, 'no_trm', 'no_trm');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
}
