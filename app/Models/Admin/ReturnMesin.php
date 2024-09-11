<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class ReturnMesin extends Model
{
    protected $table = 'tr_returnmsn';
    protected $guarded = [];

    public function detail_return()
    {
        return $this->hasMany(DetailReturnMesin::class);
    }
}

