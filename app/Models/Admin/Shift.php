<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected $table = 'tr_target_shift';
    protected $primaryKey = 'shift_id';
    public $incrementing = false;

    protected $fillable = [
        'shift_id',
        'harian_id',
        'shift',
        'qty'
    ];

    public function targetHari()
    {
        return $this->belongsTo(Harian::class, 'harian_id');
    }
}
