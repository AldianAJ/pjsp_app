<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SKM extends Model
{
    use HasFactory;

    protected $table = 'm_skm';
    protected $primaryKey = 'skm_id';
    public $incrementing = false;

    protected $fillable = [
        'skm_id', 'user_id', 'address', 'status'
    ];

    public static function generateSkmId()
    {
        $skm_id = DB::table('m_skm')->max('skm_id');

        $skm_id = str_replace("SKM", "", $skm_id);
        $skm_id = (int) $skm_id + 1;

        $addZero = str_pad($skm_id, 6, "0", STR_PAD_LEFT);

        $newSkmId = "SKM{$addZero}";

        return $newSkmId;
    }
}
