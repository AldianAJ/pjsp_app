<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class TrHMSPO extends Model
{
    protected $table = 'tr_hms_po';
    protected $primaryKey = 'no_po';
    public $incrementing = false;
    protected $guarded = [];

    public function tr_hms_po_detail()
    {
        return $this->hasMany(TrHMSPODetail::class, 'no_po');
    }
}
