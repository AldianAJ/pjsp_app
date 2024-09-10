<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gudang extends Model
{
    use HasFactory;

    protected $table = 'm_gudang';
    protected $guarded = [];

    public function jenis_mesin()
    {
        return $this->belongsTo(Mesin::class, 'mesin_id');
    }
}

