<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentoPersonalSE extends Model
{
    protected $table = 'documento_personal_se';
    protected $primaryKey = 'id_documento';
    public $timestamps = false;

    protected $fillable = ['id_personal', 'tipo', 'archivo_path', 'subido_en'];

    protected $casts = [
        'subido_en' => 'datetime',
    ];

    public const TIPOS = [
        'comprobante_domicilio'        => 'Comprobante de domicilio',
        'ine'                          => 'INE',
        'numero_seguridad_social'      => 'Número de seguridad social',
        'carta_motivos'                => 'Carta de motivos',
        'curp'                         => 'CURP',
        'acta_nacimiento'              => 'Acta de nacimiento',
        'constancia_situacion_fiscal'  => 'Constancia de situación fiscal',
        'cedula_profesional'           => 'Cédula profesional',
    ];

    public function personal()
    {
        return $this->belongsTo(PersonalServiciosEscolares::class, 'id_personal');
    }
}
