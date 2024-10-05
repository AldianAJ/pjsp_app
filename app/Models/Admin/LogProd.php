<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogProd extends Model
{
    use HasFactory;

    protected $table = 'tr_log_prod';
    protected $primaryKey = 'logprod_id';
    public $incrementing = false;
    protected $guarded = [];
}
