<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class BarangSpek extends Model
{
    protected $table = 'm_brg_spek';
    protected $primaryKey = 'spek_id';
    public $incrementing = false;
    protected $guarded = [];

}
