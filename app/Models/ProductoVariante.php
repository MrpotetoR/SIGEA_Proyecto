<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Variante de un producto — donde realmente vive el stock.
 *
 * Una credencial sin tallas tendra una sola variante con `talla = null`.
 * Una playera con tallas tendra una variante por cada talla.
 */
class ProductoVariante extends Model
{
    protected $table = 'producto_variante';
    protected $primaryKey = 'id_variante';

    protected $fillable = [
        'id_producto', 'codigo_variante', 'talla', 'stock', 'stock_minimo', 'disponible',
    ];

    protected $casts = [
        'stock'        => 'integer',
        'stock_minimo' => 'integer',
        'disponible'   => 'boolean',
    ];

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }

    public function movimientos(): HasMany
    {
        return $this->hasMany(MovimientoInventario::class, 'id_variante');
    }

    /** Label legible para mostrar al alumno. */
    public function getLabelAttribute(): string
    {
        return $this->talla ? "Talla {$this->talla}" : 'Disponible';
    }

    /** True si el stock está por debajo del mínimo configurado. */
    public function getStockBajoAttribute(): bool
    {
        return $this->stock <= $this->stock_minimo;
    }

    /** Estado consolidado para mostrar el badge. */
    public function getEstadoLabelAttribute(): string
    {
        if (!$this->disponible) return 'No disponible';
        if ($this->stock <= 0) return 'Agotado';
        if ($this->stock_bajo) return 'Stock bajo';
        return 'Disponible';
    }

    public function getEstadoColorAttribute(): string
    {
        if (!$this->disponible) return 'gray';
        if ($this->stock <= 0) return 'red';
        if ($this->stock_bajo) return 'amber';
        return 'green';
    }
}
