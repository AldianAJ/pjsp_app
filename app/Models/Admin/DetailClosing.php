<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailClosing extends Model
{
    use HasFactory;

    protected $table = 'tr_detail_closing';

    protected $guarded = [];

    public function closing()
    {
        return $this->belongsTo(Closing::class);
    }
}
