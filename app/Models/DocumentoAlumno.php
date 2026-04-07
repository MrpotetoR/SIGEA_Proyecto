<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentoAlumno extends Model
{
    protected $table = 'documento_alumno';
    protected $primaryKey = 'id_documento';
    public $timestamps = false;

    protected $fillable = ['id_alumno', 'tipo', 'archivo_path', 'subido_en'];

    public const TIPOS = [
        'acta_nacimiento'            => 'Acta de nacimiento',
        'curp'                       => 'CURP',
        'comprobante_domicilio'      => 'Comprobante de domicilio',
        'constancia_media_superior'  => 'Constancia media superior',
        'constancia_basica'          => 'Constancia básica',
        'numero_seguridad_social'    => 'Número de seguridad social',
    ];

    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'id_alumno');
    }
}
