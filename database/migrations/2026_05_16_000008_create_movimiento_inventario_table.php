<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Bitácora de movimientos de inventario.
 *
 * Tipos:
 *   - entrada:  el gestor recibió mercancía (cantidad positiva)
 *   - salida:   merma, pérdida o devolución a proveedor (cantidad negativa)
 *   - pedido:   reserva por pedido del alumno (cantidad negativa)
 *   - reverso:  cancelación de pedido o ajuste (cantidad positiva)
 *   - ajuste:   correcciones manuales (positivo o negativo)
 *
 * Cada fila guarda el `stock_resultante` post-movimiento como snapshot
 * para auditoría — así se puede reconstruir la curva de inventario sin
 * recalcular sumas históricas.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimiento_inventario', function (Blueprint $t) {
            $t->id('id_movimiento');
            $t->foreignId('id_variante')
                ->constrained('producto_variante', 'id_variante')
                ->cascadeOnDelete();
            $t->enum('tipo', ['entrada', 'salida', 'pedido', 'reverso', 'ajuste']);
            $t->integer('cantidad')->comment('Signed: + suma stock, - resta stock');
            $t->unsignedInteger('stock_resultante');
            $t->foreignId('id_pedido')->nullable()
                ->constrained('pedido', 'id_pedido')
                ->nullOnDelete();
            $t->foreignId('user_id')->nullable()
                ->constrained('users', 'id')
                ->nullOnDelete();
            $t->string('motivo', 200)->nullable();
            $t->timestamp('created_at')->useCurrent();

            $t->index(['id_variante', 'created_at']);
            $t->index('tipo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimiento_inventario');
    }
};
