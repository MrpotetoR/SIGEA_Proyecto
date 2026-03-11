<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistorialBaja extends Model
{
    protected $table = 'historial_baja';
    protected $primaryKey = 'id_baja';

    protected $fillable = [
        'id_alumno', 'autorizada_por', 'tipo_baja',
        'fecha_baja', 'fecha_reingreso', 'motivo',
    ];

    protected function casts(): array
    {
        return [
            'fecha_baja' => 'date',
            'fecha_reingreso' => 'date',
        ];
    }

    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'id_alumno');
    }

    public function autorizadaPor()
    {
        return $this->belongsTo(User::class, 'autorizada_por');
    }
}
