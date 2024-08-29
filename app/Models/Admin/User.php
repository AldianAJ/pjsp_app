<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasFactory;

    protected $table = 'm_user';

    protected $primaryKey = 'user_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'nama',
        'username',
        'password',
        'role',
        'status',
    ];

    protected $hidden = [
        'password'
    ];

    public function getAuthPassword()
    {
        return $this->password;
    }

    public static function generateUserId()
    {
        $user_id = DB::table('m_user')->max('user_id');

        $user_id = str_replace("U", "", $user_id);
        $user_id = (int) $user_id + 1;

        $addZero = str_pad($user_id, 4, "0", STR_PAD_LEFT);

        $newUserId = "U{$addZero}";

        return $newUserId;
    }
}
