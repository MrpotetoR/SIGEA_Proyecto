<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Global scope que limita las consultas a las carreras asignadas
 * al usuario autenticado de Servicios Escolares.
 *
 * - Admin: ve todo (sin filtro).
 * - Servicios Escolares: ve solo registros cuya carrera esté en su lista de asignadas.
 * - Otros roles / no autenticado: no aplica filtro (cada panel maneja su propia autorización).
 */
class CarrerasAsignadasScope implements Scope
{
    /** Nombre de la columna FK hacia carrera dentro del modelo. Por default 'id_carrera'. */
    protected string $columna;

    public function __construct(string $columna = 'id_carrera')
    {
        $this->columna = $columna;
    }

    public function apply(Builder $builder, Model $model): void
    {
        if (!auth()->check()) {
            return;
        }

        $user = auth()->user();

        // Admin: acceso pleno.
        if ($user->hasRole('admin')) {
            return;
        }

        // Solo aplica a Gestor Escolar.
        if (!$user->hasRole('gestor_escolar')) {
            return;
        }

        // En contexto Bachillerato, las carreras NO aplican
        // (la estructura curricular se maneja via bachillerato_plan).
        // Saltamos el filtro para no ocultar registros validos.
        if (\App\Support\ContextoEducativo::actual() === \App\Support\ContextoEducativo::BACHILLERATO) {
            return;
        }

        $ids = $user->carrerasAsignadasIds();
        $tabla = $model->getTable();

        // Si no tiene carreras asignadas, no ve nada (whereRaw 0=1).
        if (empty($ids)) {
            $builder->whereRaw('1 = 0');
            return;
        }

        $builder->whereIn("{$tabla}.{$this->columna}", $ids);
    }
}
