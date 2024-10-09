<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class TrSpart extends Model
{
    protected $table = 'tr_spart';
    protected $primaryKey = 'no_spart';
    public $incrementing = false;
    protected $guarded = [];
}
