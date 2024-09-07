<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'm_supplier';
    protected $guarded = [];

    public static function generateSupplierId()
    {
        $lastId = self::max('supplier_id');
        $lastId = $lastId ? (int) str_replace("S", "", $lastId) : 0;
        $newId = str_pad($lastId + 1, 4, "0", STR_PAD_LEFT);

        return "S{$newId}";
    }
}
