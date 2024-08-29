<?
namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailStokMasuk extends Model
{
    use HasFactory;

    protected $table = 'tr_detail_terima_supplier';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $fillable = [
        'no_trm',
        'brg_id',
        'qty',
        'satuan_besar',
        'ket',
    ];

}
