<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Harian extends Model
{
    use HasFactory;

    protected $table = 'tr_target_harian';
    protected $primaryKey = 'harian_id';
    public $incrementing = false;

    protected $fillable = [
        'harian_id',
        'week_id',
        'qty',
        'tgl'
    ];

    public function targetWeek()
    {
        return $this->belongsTo(Mingguan::class, 'week_id');
    }

    public function targetShift()
    {
        return $this->hasMany(Shift::class, 'harian_id');
    }
}
