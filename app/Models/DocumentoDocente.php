<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentoDocente extends Model
{
    protected $table = 'documento_docente';
    protected $primaryKey = 'id_documento';
    public $timestamps = false;

    protected $fillable = ['id_docente', 'tipo', 'archivo_path', 'subido_en'];

    public const TIPOS = [
        'comprobante_domicilio'        => 'Comprobante de domicilio',
        'ine'                          => 'INE',
        'carta_motivos'                => 'Carta de motivos',
        'curp'                         => 'CURP',
        'acta_nacimiento'              => 'Acta de nacimiento',
        'constancia_situacion_fiscal'  => 'Constancia de situación fiscal',
        'cedula_profesional'           => 'Cédula profesional',
    ];

    public function docente()
    {
        return $this->belongsTo(Docente::class, 'id_docente');
    }
}
