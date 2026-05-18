<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Linea de detalle del pedido.
 *
 * Almacena un snapshot del nombre, codigo y talla al momento de la compra
 * para que el comprobante historico no se afecte si despues se cambia el
 * catalogo.
 */
class PedidoItem extends Model
{
    protected $table = 'pedido_item';
    protected $primaryKey = 'id_item';

    protected $fillable = [
        'id_pedido', 'id_variante',
        'nombre_snapshot', 'codigo_snapshot', 'talla_snapshot',
        'cantidad', 'precio_unitario', 'subtotal',
    ];

    protected $casts = [
        'cantidad'        => 'integer',
        'precio_unitario' => 'decimal:2',
        'subtotal'        => 'decimal:2',
    ];

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class, 'id_pedido');
    }

    public function variante(): BelongsTo
    {
        return $this->belongsTo(ProductoVariante::class, 'id_variante');
    }

    /** Label legible para mostrar en el comprobante. */
    public function getDescripcionAttribute(): string
    {
        $talla = $this->talla_snapshot ? " (Talla {$this->talla_snapshot})" : '';
        return $this->nombre_snapshot . $talla;
    }
}
