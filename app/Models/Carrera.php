<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carrera extends Model
{
    protected $table = 'carrera';
    protected $primaryKey = 'id_carrera';
    public $timestamps = false;

    protected $fillable = ['id_director', 'nombre_carrera', 'clave_carrera'];

    protected static function booted(): void
    {
        static::creating(function (Carrera $carrera) {
            if (empty($carrera->clave_carrera) && !empty($carrera->nombre_carrera)) {
                $carrera->clave_carrera = self::generarClave($carrera->nombre_carrera);
            }
        });
    }

    public static function generarClave(string $nombre): string
    {
        $stopWords = ['de', 'del', 'en', 'la', 'las', 'los', 'el', 'y', 'e', 'a'];
        $words = explode(' ', $nombre);
        $initials = '';

        foreach ($words as $word) {
            if (!in_array(mb_strtolower($word), $stopWords) && mb_strlen($word) > 1) {
                $initials .= mb_strtoupper(mb_substr($word, 0, 1));
            }
        }

        if (mb_strlen($initials) < 2) {
            $initials = mb_strtoupper(mb_substr(preg_replace('/\s+/', '', $nombre), 0, 3));
        }

        return $initials . '-' . date('Y');
    }

    public function director()
    {
        return $this->belongsTo(Docente::class, 'id_director', 'id_docente');
    }

    public function alumnos()
    {
        return $this->hasMany(Alumno::class, 'id_carrera');
    }

    public function materias()
    {
        return $this->hasMany(Materia::class, 'id_carrera');
    }

    public function grupos()
    {
        return $this->hasMany(Grupo::class, 'id_carrera');
    }
}
