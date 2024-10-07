<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mesin extends Model
{
    use HasFactory;
    protected $table = 'm_mesin';
    protected $primaryKey = 'mesin_id';
    public $incrementing = false;
    protected $guarded = [];

    public function jenis()
    {
        return $this->belongsTo(JenisMesin::class, 'jenis_id', 'jenis_id');
    }
}
