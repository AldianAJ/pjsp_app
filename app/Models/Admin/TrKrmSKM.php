<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class TrKrmSKM extends Model
{
    protected $table = 'tr_krmskm';
    protected $primaryKey = 'no_krmskm';
    public $incrementing = false;
    protected $guarded = [];

    public function tr_krmskm_detail()
    {
        return $this->hasMany(TrKrmSKMDetail::class);
    }
}

