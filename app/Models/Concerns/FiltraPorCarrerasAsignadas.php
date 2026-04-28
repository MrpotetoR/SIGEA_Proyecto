<?php

namespace App\Models\Concerns;

use App\Models\Scopes\CarrerasAsignadasScope;

/**
 * Aplica el global scope CarrerasAsignadasScope para filtrar
 * por las carreras asignadas al usuario autenticado de Servicios Escolares.
 *
 * Define la propiedad estática `$columnaCarrera` (default 'id_carrera') si la
 * FK del modelo hacia carrera tiene un nombre diferente.
 */
trait FiltraPorCarrerasAsignadas
{
    public static function bootFiltraPorCarrerasAsignadas(): void
    {
        $columna = isset(static::$columnaCarrera) ? static::$columnaCarrera : 'id_carrera';
        static::addGlobalScope(new CarrerasAsignadasScope($columna));
    }

    /** Bypass del scope cuando se necesita acceso pleno (ej. en panel admin). */
    public function scopeSinFiltroDeCarreras($query)
    {
        return $query->withoutGlobalScope(CarrerasAsignadasScope::class);
    }
}
