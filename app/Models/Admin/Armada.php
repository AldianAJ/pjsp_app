<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Armada extends Model
{
    protected $table = 'm_armada';
    protected $primaryKey = 'no_pol';
    public $incrementing = false;
    protected $guarded = [];

}
