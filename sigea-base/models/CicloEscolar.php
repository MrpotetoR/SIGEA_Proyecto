<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CicloEscolar extends Model
{
    use HasFactory;

    protected $table = 'ciclos_escolares';

    protected $fillable = ['nombre', 'fecha_inicio', 'fecha_fin', 'activo'];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin'    => 'date',
        'activo'       => 'boolean',
    ];

    public function grupos(): HasMany
    {
        return $this->hasMany(Grupo::class, 'ciclo_id');
    }

    // ─── Scopes ────────────────────────────────

    public function scopeActivo($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Helper estático para obtener el ciclo activo.
     */
    public static function actual(): ?self
    {
        return static::activo()->first();
    }
}
