<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MovimientoInventario extends Model
{
    protected $table = 'movimiento_inventario';
    protected $primaryKey = 'id_movimiento';
    public $timestamps = false;

    protected $fillable = [
        'id_variante', 'tipo', 'cantidad', 'stock_resultante',
        'id_pedido', 'user_id', 'motivo', 'created_at',
    ];

    protected $casts = [
        'cantidad'         => 'integer',
        'stock_resultante' => 'integer',
        'created_at'       => 'datetime',
    ];

    public const TIPOS = [
        'entrada' => ['label' => 'Entrada',  'color' => 'green'],
        'salida'  => ['label' => 'Salida',   'color' => 'red'],
        'pedido'  => ['label' => 'Pedido',   'color' => 'blue'],
        'reverso' => ['label' => 'Reverso',  'color' => 'amber'],
        'ajuste'  => ['label' => 'Ajuste',   'color' => 'gray'],
    ];

    public function variante(): BelongsTo
    {
        return $this->belongsTo(ProductoVariante::class, 'id_variante');
    }

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class, 'id_pedido');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
