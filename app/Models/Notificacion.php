<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    protected $table = 'notificaciones';

    protected $fillable = [
        'user_id', 'tipo', 'titulo', 'mensaje', 'icono', 'color', 'url', 'leida_en',
    ];

    protected function casts(): array
    {
        return [
            'leida_en' => 'datetime',
        ];
    }

    // ── Relaciones ──
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── Scopes ──
    public function scopeNoLeidas($query)
    {
        return $query->whereNull('leida_en');
    }

    public function scopeRecientes($query, int $dias = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($dias));
    }

    // ── Helpers ──
    public function marcarLeida(): void
    {
        $this->update(['leida_en' => now()]);
    }

    public function estaLeida(): bool
    {
        return $this->leida_en !== null;
    }

    /**
     * Genera el icono SVG según el tipo.
     */
    public function getIconoSvgAttribute(): string
    {
        return match ($this->icono) {
            'newspaper' => 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z',
            'academic-cap' => 'M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z',
            'clipboard-check' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
            default => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9',
        };
    }

    public function getColorClassAttribute(): string
    {
        return match ($this->color) {
            'green' => 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400',
            'amber' => 'bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400',
            'red' => 'bg-red-100 dark:bg-red-900/30 text-red-500 dark:text-red-400',
            default => 'bg-blue-100 dark:bg-blue-900/30 text-[#0606F0] dark:text-blue-400',
        };
    }
}
