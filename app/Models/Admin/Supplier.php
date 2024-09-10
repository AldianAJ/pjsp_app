<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $table = 'm_supplier';
    protected $guarded = [];
    protected $primaryKey = 'supplier_id';
    public $incrementing = false;

    public function stok_masuk()
    {
        return $this->hasMany(StokMasuk::class);
    }
}
