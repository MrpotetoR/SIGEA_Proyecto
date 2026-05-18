<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Estructura curricular de Bachillerato.
 *
 * A diferencia de Universidad (que usa "carreras"), Bachillerato tiene un
 * Plan unificado dividido en semestres. Las materias se asocian al plan
 * por semestre, no a una carrera.
 *
 * Tabla `bachillerato_plan`:
 *   - Representa un plan de estudios de bachillerato (ej: "BGU 2026").
 *   - num_semestres define cuantos periodos tiene (4 o 6 tipicamente).
 *
 * Las materias de bachillerato apuntan al plan via columna `id_plan_bachillerato`
 * (FK opcional, solo poblada cuando nivel_educativo='bachillerato').
 *
 * Los grupos de bachillerato tambien apuntan al plan via la misma columna.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bachillerato_plan', function (Blueprint $t) {
            $t->id('id_plan_bachillerato');
            $t->string('clave_plan', 20)->unique()->comment('Ej: BGU-2026');
            $t->string('nombre_plan', 150)->comment('Ej: Bachillerato General 2026');
            $t->unsignedTinyInteger('num_semestres')->default(6)->comment('4 o 6 tipicamente');
            $t->boolean('vigente')->default(true);
            $t->text('descripcion')->nullable();
            $t->timestamps();
        });

        // Agregar FK opcional a materia y grupo (para bachillerato).
        Schema::table('materia', function (Blueprint $t) {
            if (!Schema::hasColumn('materia', 'id_plan_bachillerato')) {
                $t->unsignedBigInteger('id_plan_bachillerato')
                    ->nullable()
                    ->after('id_carrera')
                    ->comment('FK al plan de bachillerato (solo si nivel=bachillerato)');
                $t->foreign('id_plan_bachillerato')
                    ->references('id_plan_bachillerato')->on('bachillerato_plan')
                    ->onUpdate('cascade')->onDelete('set null');
            }
        });

        Schema::table('grupo', function (Blueprint $t) {
            if (!Schema::hasColumn('grupo', 'id_plan_bachillerato')) {
                $t->unsignedBigInteger('id_plan_bachillerato')
                    ->nullable()
                    ->after('id_carrera')
                    ->comment('FK al plan de bachillerato (solo si nivel=bachillerato)');
                $t->foreign('id_plan_bachillerato')
                    ->references('id_plan_bachillerato')->on('bachillerato_plan')
                    ->onUpdate('cascade')->onDelete('set null');
            }
        });

        // En alumno tambien, para saber a que plan pertenece.
        Schema::table('alumno', function (Blueprint $t) {
            if (!Schema::hasColumn('alumno', 'id_plan_bachillerato')) {
                $t->unsignedBigInteger('id_plan_bachillerato')
                    ->nullable()
                    ->after('id_carrera');
                $t->foreign('id_plan_bachillerato')
                    ->references('id_plan_bachillerato')->on('bachillerato_plan')
                    ->onUpdate('cascade')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('alumno', function (Blueprint $t) {
            if (Schema::hasColumn('alumno', 'id_plan_bachillerato')) {
                $t->dropForeign(['id_plan_bachillerato']);
                $t->dropColumn('id_plan_bachillerato');
            }
        });
        Schema::table('grupo', function (Blueprint $t) {
            if (Schema::hasColumn('grupo', 'id_plan_bachillerato')) {
                $t->dropForeign(['id_plan_bachillerato']);
                $t->dropColumn('id_plan_bachillerato');
            }
        });
        Schema::table('materia', function (Blueprint $t) {
            if (Schema::hasColumn('materia', 'id_plan_bachillerato')) {
                $t->dropForeign(['id_plan_bachillerato']);
                $t->dropColumn('id_plan_bachillerato');
            }
        });

        Schema::dropIfExists('bachillerato_plan');
    }
};
