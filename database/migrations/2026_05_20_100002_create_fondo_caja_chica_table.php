<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabla del fondo de Caja Chica (único registro global).
 *
 * - monto_base    → Tope objetivo (ej. $3,000). Si el saldo baja, el sistema
 *                   notifica al admin 3 días antes del fin de mes para
 *                   reponer hasta este monto.
 * - saldo_actual  → Saldo vivo. Se descuenta al autorizar vales, se incrementa
 *                   al reponer el fondo o al cancelar un vale autorizado.
 * - tope_vale_individual → Monto máximo permitido por vale. NULL = sin tope.
 * - umbral_verde / umbral_amarillo → Configuran el semáforo del dashboard.
 *                   Verde: saldo > umbral_verde
 *                   Amarillo: saldo entre umbral_amarillo y umbral_verde
 *                   Rojo: saldo <= umbral_amarillo
 *
 * Solo el admin puede modificar monto_base, topes y umbrales (acción sensible:
 * reauth + motivo + log en caja_chica_log).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fondo_caja_chica', function (Blueprint $t) {
            $t->id('id_fondo');

            $t->decimal('monto_base', 10, 2)
              ->default(0)
              ->comment('Tope objetivo del fondo (ej. 3000.00)');

            $t->decimal('saldo_actual', 10, 2)
              ->default(0)
              ->comment('Saldo vivo. Baja con vales autorizados, sube con reposiciones');

            $t->decimal('tope_vale_individual', 10, 2)
              ->nullable()
              ->comment('Monto máx por vale. NULL = sin tope');

            $t->decimal('umbral_verde', 10, 2)
              ->default(2000)
              ->comment('Saldo > este valor = semáforo verde');

            $t->decimal('umbral_amarillo', 10, 2)
              ->default(1000)
              ->comment('Saldo > este valor y <= umbral_verde = amarillo. <= este valor = rojo');

            $t->foreignId('configurado_por')
              ->nullable()
              ->constrained('users', 'id')
              ->nullOnDelete()
              ->comment('Admin que configuró el fondo por última vez');

            $t->timestamp('configurado_en')->nullable();

            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fondo_caja_chica');
    }
};
