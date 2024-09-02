<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mingguan extends Model
{
    use HasFactory;

    protected $table = 'tr_target_week';
    protected $primaryKey = 'week_id';

    protected $fillable = [
        'week_id', 'brg_id', 'tahun', 'week', 'qty',
    ];

}
