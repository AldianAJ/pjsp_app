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

    protected $guarded = [];

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

    public function detailPermintaan()
    {
        return $this->hasMany(DetailPermintaan::class);
    }
}
