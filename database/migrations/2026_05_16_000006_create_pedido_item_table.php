<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Líneas de detalle del pedido.
 *
 * Snapshot de precio y nombre del producto al momento de la compra:
 * si el gestor sube el precio después, las compras pasadas mantienen
 * el precio original.
 *
 * `id_variante` puede quedar NULL si el variante se elimina después,
 * pero `nombre_snapshot` y `talla_snapshot` permiten reconstruir el
 * comprobante histórico.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedido_item', function (Blueprint $t) {
            $t->id('id_item');
            $t->foreignId('id_pedido')
                ->constrained('pedido', 'id_pedido')
                ->cascadeOnDelete();
            $t->foreignId('id_variante')->nullable()
                ->constrained('producto_variante', 'id_variante')
                ->nullOnDelete();

            // Snapshots para preservar el historial aunque cambien los catálogos.
            $t->string('nombre_snapshot', 150);
            $t->string('codigo_snapshot', 40);
            $t->string('talla_snapshot', 10)->nullable();

            $t->unsignedInteger('cantidad');
            $t->decimal('precio_unitario', 10, 2);
            $t->decimal('subtotal', 10, 2);

            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedido_item');
    }
};
