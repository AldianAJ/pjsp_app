<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class PermintaanSKM extends Model
{
    protected $table = 'tr_reqskm';
    protected $primaryKey = 'no_reqskm';
    public $incrementing = false;
    protected $guarded = [];

    public function detail_permintaan()
    {
        return $this->hasMany(DetailPermintaanSKM::class);
    }
}
