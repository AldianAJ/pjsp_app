<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class TrReqSKM extends Model
{
    protected $table = 'tr_reqskm';
    protected $primaryKey = 'no_reqskm';
    public $incrementing = false;
    protected $guarded = [];

    public function tr_reqskm_detail()
    {
        return $this->hasMany(TrReqSKMDetail::class);
    }
}
