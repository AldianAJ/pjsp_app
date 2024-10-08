<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $table = 'm_supplier';
    protected $primaryKey = 'supplier_id';
    public $incrementing = false;
    protected $guarded = [];

    public function tr_trmsup()
    {
        return $this->hasMany(TrTrmSup::class);
    }
}
