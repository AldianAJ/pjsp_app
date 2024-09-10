<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TargetMesin extends Model
{
    use HasFactory;

    protected $table = 'tr_target_mesin';
    protected $primaryKey = 'msn_trgt_id';
    public $incrementing = false;
    protected $guarded = [];

    public function mesin()
    {
        return $this->belongsTo(Mesin::class, 'mesin_id');
    }

    public function targetShift()
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }
}
