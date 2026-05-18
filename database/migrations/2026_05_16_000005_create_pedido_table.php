<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Pedido de tienda institucional.
 *
 * Flujo de estados:
 *   pendiente_pago → vaucher_enviado → aprobado → listo_recoger → entregado
 *                                                                      ↑
 *                                                 cancelado (cualquier momento)
 *
 * - `folio`: identificador legible para el alumno (PED-2026-0001).
 * - `nivel_educativo`: snapshot del nivel del comprador (para reportes).
 * - `vaucher_path`: ruta al comprobante de pago subido.
 * - `motivo_rechazo`: si el gestor rechaza el váucher, queda registro.
 * - `fecha_listo_recoger`: timestamp del cambio a "listo para recoger"
 *   (dispara el correo automático).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedido', function (Blueprint $t) {
            $t->id('id_pedido');
            $t->string('folio', 20)->unique()->comment('PED-2026-0001');
            $t->foreignId('user_id')
                ->constrained('users', 'id')
                ->cascadeOnDelete()
                ->comment('Alumno o docente que pidió');
            $t->enum('nivel_educativo', ['universidad', 'bachillerato'])->default('universidad');
            $t->decimal('total', 10, 2)->default(0);
            $t->enum('estado', [
                'pendiente_pago',
                'vaucher_enviado',
                'aprobado',
                'listo_recoger',
                'entregado',
                'cancelado',
            ])->default('pendiente_pago');

            // Váucher y validación
            $t->string('vaucher_path', 500)->nullable();
            $t->timestamp('vaucher_subido_en')->nullable();
            $t->text('motivo_rechazo')->nullable();
            $t->foreignId('revisado_por')->nullable()->constrained('users', 'id')->nullOnDelete();
            $t->timestamp('revisado_en')->nullable();

            // Entrega
            $t->timestamp('fecha_listo_recoger')->nullable();
            $t->foreignId('entregado_por')->nullable()->constrained('users', 'id')->nullOnDelete();
            $t->timestamp('entregado_en')->nullable();

            $t->timestamps();

            $t->index('estado');
            $t->index('nivel_educativo');
            $t->index(['user_id', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedido');
    }
};
