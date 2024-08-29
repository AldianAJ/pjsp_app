<?
namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailStokMasuk extends Model
{
    use HasFactory;

    protected $table = 'tr_detail_terima_supplier';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $fillable = [
        'id',
        'no_trm',
        'brg_id',
        'qty',
        'satuan_besar',
        'ket',
    ];

    public static function generateNoTrm()
{
    // Ambil ID terakhir dari database
    $lastId = self::max('id');

    // Jika ID terakhir ada, tambahkan 1; jika tidak, mulai dari 1
    $newId = $lastId ? $lastId + 1 : 1;

    // Kembalikan ID baru
    return $newId;
}

}
