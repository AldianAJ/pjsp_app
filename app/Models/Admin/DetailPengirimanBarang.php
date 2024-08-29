<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPengirimanBarang extends Model
{
    use HasFactory;

    protected $table = 'tr_detail_kirim_brg';
    protected $guarded = [];
}
