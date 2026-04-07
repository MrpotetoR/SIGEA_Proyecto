<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PadreTutor extends Model
{
    protected $table = 'padre_tutor';
    protected $primaryKey = 'id_padre_tutor';

    protected $fillable = [
        'id_alumno', 'nombre', 'apellidos', 'email',
        'telefono', 'telefono_emergencia', 'ine_path',
    ];

    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'id_alumno');
    }
}
