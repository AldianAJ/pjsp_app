<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailStokMasuk extends Model
{
    use HasFactory;

    protected $table = 'tr_trmsup_detail';
    protected $primaryKey = 'no_trm';
    protected $fillable = [
        'id',
        'no_trm',
        'brg_id',
        'qty',
        'satuan_besar',
        'ket'
    ];

    public function stok_masuk()
    {
        return $this->belongsTo(StokMasuk::class, 'no_trm', 'no_trm');
    }
}
