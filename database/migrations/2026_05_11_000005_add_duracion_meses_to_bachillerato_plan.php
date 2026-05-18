<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Permite almacenar la duracion del plan explicitamente (en meses), porque
 * num_periodos x meses-por-periodo no siempre cuadra:
 *
 *   - Escolarizado:    6 semestres x 6 meses = 36 meses (3 anios) ✓
 *   - No Escolarizado: 4 cuatrimestres x 4 meses = 16, pero la institucion
 *     declara 18 meses por convencion. Se guarda override aqui.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bachillerato_plan', function (Blueprint $t) {
            $t->unsignedTinyInteger('duracion_meses')
                ->nullable()
                ->after('tipo_periodo')
                ->comment('Override de duracion total. Si NULL, se calcula como num_periodos * (6|4).');
        });

        // Backfill: BGE → 36 meses, BNE → 18 meses.
        DB::table('bachillerato_plan')->where('clave_plan', 'BGE-2026')->update(['duracion_meses' => 36]);
        DB::table('bachillerato_plan')->where('clave_plan', 'BNE-2026')->update(['duracion_meses' => 18]);
    }

    public function down(): void
    {
        Schema::table('bachillerato_plan', function (Blueprint $t) {
            $t->dropColumn('duracion_meses');
        });
    }
};
