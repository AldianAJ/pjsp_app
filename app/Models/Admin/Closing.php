<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Closing extends Model
{
    use HasFactory;

    protected $table = 'tr_closing';

    protected $guarded = [];

    public function detail_closing()
    {
        return $this->hasMany(DetailClosing::class);
    }
}
