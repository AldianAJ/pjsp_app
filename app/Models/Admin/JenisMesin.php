<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisMesin extends Model
{
    use HasFactory;

    protected $table = 'm_jenis_mesin';
    protected $primaryKey = 'jenis_id';
    public $incrementing = false;

    protected $fillable = [
        'jenis_id',
        'nama',
    ];

}

