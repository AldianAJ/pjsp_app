<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengiriman extends Model
{
    use HasFactory;

    protected $table = 'tr_krmskm';
    protected $primaryKey = 'no_krmskm';
    public $incrementing = false;

    protected $fillable = [
        'no_krmskm', 'tgl'
    ];

    
}

