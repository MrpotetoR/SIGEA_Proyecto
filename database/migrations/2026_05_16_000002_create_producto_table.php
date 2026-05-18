<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Catálogo de productos institucionales (uniformes, credenciales, accesorios).
 *
 * - `codigo`: SKU corto y legible para inventario y comprobantes.
 * - `nivel_educativo`: separa el catálogo de Universidad y Bachillerato.
 * - `tiene_tallas`: indica si el producto se vende por variantes de talla
 *   (XS/S/M/L/XL). Si es false, tendrá una única variante "única".
 * - `precio`: precio base. El precio se snapshea en el pedido al comprar
 *   para que cambios futuros no afecten compras pasadas.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('producto', function (Blueprint $t) {
            $t->id('id_producto');
            $t->string('codigo', 30)->unique()->comment('SKU base: PLAY-AZUL, CRED-DSM');
            $t->string('nombre', 150);
            $t->text('descripcion')->nullable();
            $t->enum('categoria', ['uniforme', 'credencial', 'accesorio', 'otro'])->default('otro');
            $t->decimal('precio', 10, 2);
            $t->enum('nivel_educativo', ['universidad', 'bachillerato'])->default('universidad');
            $t->boolean('tiene_tallas')->default(false);
            $t->string('imagen_principal', 500)->nullable()->comment('Path en storage/app/public');
            $t->boolean('activo')->default(true)->comment('Si false, no aparece en catalogo publico');
            $t->timestamps();

            $t->index('nivel_educativo');
            $t->index('categoria');
            $t->index('activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('producto');
    }
};
