<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pertanyaan extends Model
{
    protected $table = 'hr_darussalam_pertanyaan';
    protected $primaryKey = 'id_pertanyaan';
    public $timestamps = false;
    
    protected $fillable = [
        'id_kriteria',
        'teks_pertanyaan'
    ];
    
    public function kriteria()
    {
        return $this->belongsTo(Kriteria::class, 'id_kriteria');
    }
    
    public function penilaian()
    {
        return $this->hasMany(Penilaian::class, 'id_pertanyaan');
    }
}



?>