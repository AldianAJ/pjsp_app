<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class TrKrmMsn extends Model
{
    protected $table = 'tr_krmmsn';
    protected $guarded = [];

    public function tr_krmmsn_detail()
    {
        return $this->hasMany(TrKrmMsnDetail::class);
    }
}

