<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabla unificada de eventos de ingreso de la Caja General.
 *
 * Recibe registros automáticos desde 3 fuentes:
 *  - Colegiaturas: AlumnosController::aprobarBaucher → tipo=colegiatura
 *  - Productos: PedidosController::aprobar → tipo=producto
 *  - Trámites: CobroTramiteController::store → tipo=tramite
 *  - Manual: registro a mano por admin → tipo=otro
 *
 * Para reversas (cancelaciones, devoluciones), el registro NO se borra:
 * se marca con cancelado_en + cancelado_por + motivo_cancelacion para
 * mantener trazabilidad completa. Los reportes filtran WHERE cancelado_en
 * IS NULL salvo que se pida "incluir cancelados".
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ingreso_caja_general', function (Blueprint $t) {
            $t->id('id_ingreso');
            $t->string('folio', 25)->unique()->comment('ICG-2026-00001');

            // Clasificación
            $t->enum('tipo', ['colegiatura', 'producto', 'tramite', 'otro'])
              ->comment('Categoría principal del ingreso');

            // Referencia polimórfica ligera (referencia_tipo + referencia_id)
            $t->string('referencia_tipo', 50)->nullable()
              ->comment('pago_cuatrimestre | pedido | cobro_tramite | manual');
            $t->unsignedBigInteger('referencia_id')->nullable()
              ->comment('ID en la tabla origen (sin FK estricta porque es polimórfico)');

            $t->string('concepto', 255)
              ->comment('Descripción auto-generada (ej. "Colegiatura 3er cuatrimestre — Juan Pérez")');

            $t->decimal('monto', 10, 2);

            // Vinculación a alumno (nullable porque "manual" / "otro" pueden no tener alumno)
            $t->unsignedBigInteger('alumno_id')->nullable();
            $t->foreign('alumno_id')
              ->references('id_alumno')->on('alumno')
              ->onUpdate('cascade')->onDelete('set null');

            // Quien registró el ingreso (gestor que aprobó o admin)
            $t->foreignId('user_id')
              ->constrained('users', 'id')
              ->restrictOnDelete()
              ->comment('Usuario que generó el evento de ingreso');

            // Detalle del cobro
            $t->enum('metodo_pago', ['transferencia', 'efectivo', 'tarjeta', 'otro'])
              ->default('transferencia');
            $t->timestamp('fecha_cobro')->useCurrent()
              ->comment('Fecha efectiva del ingreso (puede ser distinta de created_at)');

            // Reversa / cancelación (no se borra el registro)
            $t->timestamp('cancelado_en')->nullable();
            $t->foreignId('cancelado_por')
              ->nullable()
              ->constrained('users', 'id')
              ->nullOnDelete();
            $t->string('motivo_cancelacion', 255)->nullable();

            $t->timestamps();

            $t->index(['tipo', 'fecha_cobro']);
            $t->index(['alumno_id', 'fecha_cobro']);
            $t->index('fecha_cobro');
            $t->index(['referencia_tipo', 'referencia_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ingreso_caja_general');
    }
};
