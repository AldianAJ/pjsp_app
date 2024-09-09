<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailReturnMesin extends Model
{
    use HasFactory;

    protected $table = 'tr_returnmsn_detail';
    protected $guarded = [];


    public function return()
    {
        return $this->belongsTo(ReturnMesin::class, 'no_returnmsn');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'brg_id');
    }

}
