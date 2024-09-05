<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPengiriman extends Model
{
    use HasFactory;

    protected $table = 'tr_krmskm_detail';
    protected $guarded = [];

    public function pengiriman()
    {
        return $this->belongsTo(Pengiriman::class, 'no_krmskm', 'no_krmskm');
    }
}
