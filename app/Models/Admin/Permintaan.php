<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permintaan extends Model
{
    use HasFactory;

    protected $table = 'tr_reqskm';
    protected $primaryKey = 'no_reqskm';
    public $incrementing = false;

    protected $fillable = [
        'no_reqskm', 'tgl', 'gudang_id', 'status'
    ];

}
