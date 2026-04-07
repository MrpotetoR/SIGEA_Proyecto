<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagoCuatrimestre extends Model
{
    protected $table = 'pago_cuatrimestre';
    protected $primaryKey = 'id_pago';
    public $timestamps = false;

    protected $fillable = ['id_alumno', 'cuatrimestre', 'baucher_path', 'subido_en'];

    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'id_alumno');
    }
}
