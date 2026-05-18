<?php

namespace App\Models;

use App\Models\Scopes\NivelEducativoScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Catalogo de productos institucionales.
 *
 * Cada producto tiene 1+ variantes (con o sin talla) que es donde
 * realmente vive el stock. Para acceder al stock total del producto:
 *   $producto->stockTotal()
 */
class Producto extends Model
{
    protected $table = 'producto';
    protected $primaryKey = 'id_producto';

    protected $fillable = [
        'codigo', 'nombre', 'descripcion', 'categoria', 'precio',
        'nivel_educativo', 'tiene_tallas', 'imagen_principal', 'activo',
    ];

    protected $casts = [
        'precio'       => 'decimal:2',
        'tiene_tallas' => 'boolean',
        'activo'       => 'boolean',
    ];

    public const CATEGORIAS = [
        'uniforme'    => 'Uniforme',
        'credencial'  => 'Credencial escolar',
        'accesorio'   => 'Accesorio',
        'otro'        => 'Otro',
    ];

    /** Tallas estandar para productos de uniforme. */
    public const TALLAS_DEFAULT = ['XS', 'S', 'M', 'L', 'XL'];

    protected static function booted(): void
    {
        // Igual que el resto del sistema: filtra automaticamente por
        // contexto activo (universidad / bachillerato).
        static::addGlobalScope(new NivelEducativoScope());
    }

    /** Bypass para admin / reportes globales. */
    public function scopeSinFiltroNivel($query)
    {
        return $query->withoutGlobalScope(NivelEducativoScope::class);
    }

    public function variantes(): HasMany
    {
        return $this->hasMany(ProductoVariante::class, 'id_producto');
    }

    public function imagenes(): HasMany
    {
        return $this->hasMany(ProductoImagen::class, 'id_producto')->orderBy('orden');
    }

    /** Suma de stock de todas las variantes. */
    public function stockTotal(): int
    {
        return (int) $this->variantes()->sum('stock');
    }

    /** True si al menos una variante tiene stock. */
    public function hayDisponibilidad(): bool
    {
        return $this->variantes()->where('stock', '>', 0)->exists();
    }

    /** Solo productos visibles en el catalogo publico. */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /** Filtra por categoria. */
    public function scopeCategoria($query, string $categoria)
    {
        return $query->where('categoria', $categoria);
    }
}
