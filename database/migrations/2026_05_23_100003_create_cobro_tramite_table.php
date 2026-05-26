<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Cobros manuales de trámites administrativos (kárdex, constancias, etc.).
 *
 * Diferencia con `ingreso_caja_general`:
 *  - cobro_tramite es la tabla **fuente** del cobro (con datos específicos
 *    del trámite: tipo, alumno, evidencia, método de pago)
 *  - Al guardar un cobro_tramite, automáticamente se crea un registro en
 *    `ingreso_caja_general` con tipo=tramite, referencia_tipo=cobro_tramite,
 *    referencia_id=id_cobro
 *
 * Las tarifas se configuran en `configuracion_institucional`:
 *  - tramite.kardex.precio
 *  - tramite.constancia_estudios.precio
 *  - tramite.constancia_terminacion.precio
 *  - tramite.constancia_no_adeudo.precio
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cobro_tramite', function (Blueprint $t) {
            $t->id('id_cobro');
            $t->string('folio', 25)->unique()->comment('CTR-2026-00001');

            $t->enum('tipo_tramite', [
                'kardex',
                'constancia_estudios',
                'constancia_terminacion',
                'constancia_no_adeudo',
                'otro',
            ]);
            $t->string('concepto_personalizado', 255)->nullable()
              ->comment('Cuando tipo_tramite=otro');

            $t->decimal('monto', 10, 2);

            // Alumno solicitante
            $t->unsignedBigInteger('alumno_id');
            $t->foreign('alumno_id')
              ->references('id_alumno')->on('alumno')
              ->cascadeOnUpdate()
              ->restrictOnDelete();

            // Quien procesó el cobro
            $t->foreignId('cobrado_por')
              ->constrained('users', 'id')
              ->restrictOnDelete();

            $t->enum('metodo_pago', ['transferencia', 'efectivo', 'tarjeta', 'otro'])
              ->default('efectivo');

            $t->string('referencia_externa', 100)->nullable()
              ->comment('Núm. de recibo, comprobante, etc.');

            $t->string('evidencia_path', 500)->nullable()
              ->comment('Foto del recibo o comprobante (opcional)');

            $t->enum('estatus', ['cobrado', 'cancelado'])->default('cobrado');
            $t->timestamp('cobrado_en')->useCurrent();
            $t->timestamp('cancelado_en')->nullable();
            $t->foreignId('cancelado_por')->nullable()
              ->constrained('users', 'id')->nullOnDelete();
            $t->string('motivo_cancelacion', 255)->nullable();

            $t->timestamps();

            $t->index(['tipo_tramite', 'estatus']);
            $t->index(['alumno_id', 'estatus']);
            $t->index('cobrado_en');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cobro_tramite');
    }
};
