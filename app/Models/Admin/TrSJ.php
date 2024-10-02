<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class TrSJ extends Model
{
    protected $table = 'tr_sj';
    protected $primaryKey = 'no_sj';
    public $incrementing = false;
    protected $guarded = [];

}
