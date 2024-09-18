<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class TrReturnMsn extends Model
{
    protected $table = 'tr_returnmsn';
    protected $guarded = [];

    public function tr_returnmsn_detail()
    {
        return $this->hasMany(TrReturnMsnDetail::class);
    }
}

