<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Mingguan extends Model
{
    use HasFactory;

    protected $table = 'tr_target_week';
    protected $primaryKey = 'week_id';
    public $incrementing = false;
    protected $guarded = [];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'brg_id');
    }

    public function targetHari()
    {
        return $this->hasMany(Harian::class, 'week_id');
    }
}
