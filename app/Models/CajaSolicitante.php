<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Catálogo de solicitantes para autocompletado del módulo Caja Chica.
 *
 * Se popula automáticamente desde CajaChicaController al crear un vale con
 * un nombre que aún no existe. Cada vez que se usa el mismo nombre se
 * incrementa veces_usado y se actualiza ultimo_uso_en.
 *
 * El endpoint AJAX /gestor/caja-chica/solicitantes/buscar?q=... consulta
 * con LIKE %q% y devuelve top 5 ordenados por veces_usado DESC,
 * ultimo_uso_en DESC.
 */
class CajaSolicitante extends Model
{
    protected $table = 'caja_solicitante';

    protected $fillable = [
        'nombre',
        'veces_usado',
        'ultimo_uso_en',
    ];

    protected $casts = [
        'veces_usado'   => 'integer',
        'ultimo_uso_en' => 'datetime',
    ];

    /**
     * Registra el uso de un nombre como solicitante (alta o incremento).
     * Llamar desde CajaChicaController@store.
     */
    public static function registrarUso(string $nombre): self
    {
        $nombre = trim($nombre);

        $solicitante = self::firstOrNew(['nombre' => $nombre]);

        if ($solicitante->exists) {
            $solicitante->increment('veces_usado');
            $solicitante->update(['ultimo_uso_en' => now()]);
        } else {
            $solicitante->veces_usado   = 1;
            $solicitante->ultimo_uso_en = now();
            $solicitante->save();
        }

        return $solicitante;
    }

    /**
     * Búsqueda para autocompletado.
     * Devuelve top N por relevancia (más usados primero, luego más recientes).
     */
    public static function buscar(string $q, int $limit = 5)
    {
        return self::where('nombre', 'like', "%{$q}%")
            ->orderByDesc('veces_usado')
            ->orderByDesc('ultimo_uso_en')
            ->limit($limit)
            ->get(['id', 'nombre', 'veces_usado']);
    }
}
