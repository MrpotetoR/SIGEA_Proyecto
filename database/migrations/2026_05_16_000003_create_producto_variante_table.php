<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Variantes de producto — el stock se administra por variante, no por producto.
 *
 * Para un producto sin tallas (ej. credencial) habrá una variante "única".
 * Para un producto con tallas (ej. playera), habrá una variante por talla:
 *   - PLAY-AZUL-XS, PLAY-AZUL-S, PLAY-AZUL-M, ...
 *
 * El `codigo_variante` se autogenera concatenando codigo del producto + talla.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('producto_variante', function (Blueprint $t) {
            $t->id('id_variante');
            $t->foreignId('id_producto')
                ->constrained('producto', 'id_producto')
                ->cascadeOnDelete();
            $t->string('codigo_variante', 40)->unique();
            $t->string('talla', 10)->nullable()->comment('NULL = variante unica sin talla');
            $t->unsignedInteger('stock')->default(0);
            $t->unsignedInteger('stock_minimo')->default(3)->comment('Alerta de reabastecimiento');
            $t->timestamps();

            $t->index(['id_producto', 'talla']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('producto_variante');
    }
};
