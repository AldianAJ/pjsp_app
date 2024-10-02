<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class TrHMSPO extends Model
{
    protected $table = 'tr_hms_po';
    protected $primaryKey = 'no_po';
    public $incrementing = false;
    protected $guarded = [];

}
