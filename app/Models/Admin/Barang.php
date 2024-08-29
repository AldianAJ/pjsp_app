<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'm_brg';
    protected $primaryKey = 'brg_id';
    public $incrementing = false;

    protected $fillable = [
        'brg_id','supplier_id', 'nm_brg', 'satuan_beli', 'konversi1', 'satuan_besar', 'konversi2', 'satuan_kecil', 'konversi3'
    ];

    /**
     * Generate a new Barang ID in an Eloquent way.
     *
     * @return string
     */
    public static function generateBarangId()
    {
        $maxId = self::max('brg_id');

        $numericPart = intval(str_replace('B', '', $maxId)) + 1;

        return 'B' . str_pad($numericPart, 4, '0', STR_PAD_LEFT);
    }
}
