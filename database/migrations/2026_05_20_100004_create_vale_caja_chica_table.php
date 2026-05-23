<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Vales (salidas) de la Caja Chica.
 *
 * Flujo de estados:
 *   solicitada   → autorizada → comprobada
 *                ↘ rechazada (terminal)
 *
 *   autorizada/comprobada pueden pasar a "cancelada" (terminal). Al cancelar
 *   un vale autorizado, el saldo del fondo se devuelve automáticamente.
 *
 * Reglas:
 *   - El folio se autogenera con formato VCC-YYYY-NNNN (ver ValeCajaChica@booted).
 *   - solicitado_por es el gestor que crea la solicitud.
 *   - autorizado_por es el admin o gestor con puede_gestionar_caja_chica que
 *     aprueba (acción sensible: reauth + motivo + log).
 *   - El monto se descuenta del saldo del fondo SOLO al pasar a "autorizada".
 *   - factura_path es inmutable una vez establecido (no se puede modificar ni
 *     resubir; ver subir_factura en CajaChicaController).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vale_caja_chica', function (Blueprint $t) {
            $t->id('id_vale');
            $t->string('folio', 20)->unique()->comment('VCC-2026-0001');

            $t->foreignId('id_fondo')
              ->constrained('fondo_caja_chica', 'id_fondo')
              ->cascadeOnUpdate()
              ->restrictOnDelete();

            $t->string('solicitante_nombre', 150)
              ->comment('Texto libre con autocompletado contra caja_solicitante');

            $t->string('concepto', 255);
            $t->decimal('monto', 10, 2);

            $t->enum('estatus', [
                'solicitada',
                'autorizada',
                'rechazada',
                'comprobada',
                'cancelada',
            ])->default('solicitada');

            // Quién la solicitó
            $t->foreignId('solicitado_por')
              ->constrained('users', 'id')
              ->restrictOnDelete();

            // Autorización (admin o gestor con permiso)
            $t->foreignId('autorizado_por')
              ->nullable()
              ->constrained('users', 'id')
              ->nullOnDelete();
            $t->timestamp('autorizado_en')->nullable();
            $t->text('motivo_rechazo')->nullable();

            // Factura digital (inmutable tras carga)
            $t->string('factura_path', 500)->nullable();
            $t->timestamp('factura_subida_en')->nullable();
            $t->foreignId('factura_subida_por')
              ->nullable()
              ->constrained('users', 'id')
              ->nullOnDelete();

            // Comprobación (cierre)
            $t->foreignId('cerrado_por')
              ->nullable()
              ->constrained('users', 'id')
              ->nullOnDelete();
            $t->timestamp('cerrado_en')->nullable();

            // Cancelación
            $t->foreignId('cancelado_por')
              ->nullable()
              ->constrained('users', 'id')
              ->nullOnDelete();
            $t->timestamp('cancelado_en')->nullable();

            $t->timestamps();

            $t->index('estatus');
            $t->index('solicitado_por');
            $t->index(['estatus', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vale_caja_chica');
    }
};
