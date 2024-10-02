<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Cust extends Model
{
    protected $table = 'm_cust';
    protected $primaryKey = 'cust_id';
    public $incrementing = false;
    protected $guarded = [];

}
