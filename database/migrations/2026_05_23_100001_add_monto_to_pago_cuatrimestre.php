<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Agrega el campo `monto` a `pago_cuatrimestre` para que cada baucher tenga
 * un monto explícito.
 *
 * Nullable por compatibilidad con registros existentes. Si está NULL al
 * momento de aprobar, el sistema usa la tarifa default desde
 * `configuracion_institucional` (clave `colegiatura.monto_default`).
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('pago_cuatrimestre', function (Blueprint $table) {
            $table->decimal('monto', 10, 2)
                  ->nullable()
                  ->after('cuatrimestre')
                  ->comment('Monto del pago de colegiatura (capturado o desde tarifa default)');
        });
    }

    public function down(): void
    {
        Schema::table('pago_cuatrimestre', function (Blueprint $table) {
            $table->dropColumn('monto');
        });
    }
};
