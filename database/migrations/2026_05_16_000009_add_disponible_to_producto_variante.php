<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Agrega la columna `disponible` a producto_variante.
 *
 * Override manual del estado: el gestor puede marcar una variante como
 * "No disponible" aunque tenga stock (ej. lote dañado pero pendiente de
 * salida, reservado para un caso específico, etc.).
 *
 * Lógica del estado mostrado al usuario:
 *   - disponible = false → "No disponible"  (override manual)
 *   - stock <= 0          → "Agotado"
 *   - stock <= stock_minimo → "Stock bajo"
 *   - else                 → "Disponible"
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('producto_variante', function (Blueprint $t) {
            $t->boolean('disponible')
                ->default(true)
                ->after('stock_minimo')
                ->comment('Override manual: false oculta la variante del catalogo publico.');
        });
    }

    public function down(): void
    {
        Schema::table('producto_variante', function (Blueprint $t) {
            $t->dropColumn('disponible');
        });
    }
};
