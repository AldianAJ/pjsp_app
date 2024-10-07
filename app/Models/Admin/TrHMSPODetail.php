<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class TrHMSPODetail extends Model
{
    protected $table = 'tr_hms_po_detail';
    protected $guarded = [];

    public function tr_hms_po()
    {
        return $this->belongsTo(TrHMSPO::class, 'no_po');
    }
}
