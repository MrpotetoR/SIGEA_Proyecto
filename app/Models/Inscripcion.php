<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inscripcion extends Model
{
    protected $table = 'inscripcion';
    protected $primaryKey = 'id_inscripcion';
    public $timestamps = false;

    protected $fillable = ['id_alumno', 'id_grupo', 'fecha_inscripcion'];

    /**
     * Scope global: filtra inscripciones cuya carrera (vía grupo) esté asignada
     * al usuario autenticado de Servicios Escolares.
     */
    protected static function booted(): void
    {
        static::addGlobalScope('carreras_asignadas', function ($query) {
            if (!auth()->check()) return;
            $user = auth()->user();
            if ($user->hasRole('admin')) return;
            if (!$user->hasRole('gestor_escolar')) return;

            $ids = $user->carrerasAsignadasIds();
            if (empty($ids)) {
                $query->whereRaw('1 = 0');
                return;
            }
            $query->whereExists(function ($sub) use ($ids) {
                $sub->select(\DB::raw(1))
                    ->from('grupo')
                    ->whereColumn('grupo.id_grupo', 'inscripcion.id_grupo')
                    ->whereIn('grupo.id_carrera', $ids);
            });
        });
    }

    public function scopeSinFiltroDeCarreras($query)
    {
        return $query->withoutGlobalScope('carreras_asignadas');
    }

    protected function casts(): array
    {
        return ['fecha_inscripcion' => 'date'];
    }

    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'id_alumno');
    }

    public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'id_grupo');
    }
}
