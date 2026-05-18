<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Bitácora de cambios de estado del pedido.
 *
 * Cada cambio queda registrado con timestamp, autor y comentario.
 * Sirve tanto para auditoría como para construir un timeline visual
 * en el detalle del pedido.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedido_estado_historial', function (Blueprint $t) {
            $t->id('id_historial');
            $t->foreignId('id_pedido')
                ->constrained('pedido', 'id_pedido')
                ->cascadeOnDelete();
            $t->string('estado_anterior', 30)->nullable();
            $t->string('estado_nuevo', 30);
            $t->foreignId('user_id')->nullable()
                ->constrained('users', 'id')
                ->nullOnDelete();
            $t->text('comentario')->nullable();
            $t->timestamp('created_at')->useCurrent();

            $t->index(['id_pedido', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedido_estado_historial');
    }
};
