<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Agrega `tipo_periodo` a los planes de bachillerato.
 *
 * Bachillerato General Escolarizado  -> semestres (3 anios = 6 semestres)
 * Bachillerato No Escolarizado       -> cuatrimestres (18 meses = ~4 cuatrimestres)
 *
 * No renombramos `num_semestres` para evitar romper codigo existente:
 * la columna seguira llamandose asi pero conceptualmente representa
 * el numero de periodos (semestres o cuatrimestres segun el plan).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bachillerato_plan', function (Blueprint $t) {
            $t->enum('tipo_periodo', ['semestre', 'cuatrimestre'])
                ->default('semestre')
                ->after('num_semestres');
        });

        // Backfill: planes existentes son semestrales (BGU clasico).
        DB::table('bachillerato_plan')->update(['tipo_periodo' => 'semestre']);
    }

    public function down(): void
    {
        Schema::table('bachillerato_plan', function (Blueprint $t) {
            $t->dropColumn('tipo_periodo');
        });
    }
};
