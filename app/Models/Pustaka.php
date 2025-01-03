<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pustaka extends Model
{
    use HasFactory;

    protected $table = 'pknow_mspustaka';
    protected $primaryKey = 'pus_id';
    public $incrementing = false;
    protected $fillable = [
        'kke_id', 'pus_judul', 'pus_file', 'pus_keterangan',
        'pus_kata_kunci', 'pus_gambar', 'pus_status',
        'pus_created_by', 'pus_created_date', 'pus_modif_by', 'pus_modif_date'
    ];
}

?>