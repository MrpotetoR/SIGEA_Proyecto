<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductoImagen extends Model
{
    protected $table = 'producto_imagen';
    protected $primaryKey = 'id_imagen';

    protected $fillable = ['id_producto', 'archivo_path', 'alt', 'orden'];

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }
}
