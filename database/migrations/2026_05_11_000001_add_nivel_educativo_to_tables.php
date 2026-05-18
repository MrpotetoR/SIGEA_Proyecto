<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Agrega la columna `nivel_educativo` a las tablas principales para soportar
 * la coexistencia de dos areas operativas dentro del mismo sistema:
 *   - 'universidad'    : carreras, planes de estudio, cuatrimestres
 *   - 'bachillerato'   : grupos por grado, semestres, sin "carrera"
 *
 * Todos los registros existentes quedan marcados como 'universidad' (default),
 * lo que preserva el comportamiento actual.
 *
 * Tambien hace nullable la FK `id_carrera` en alumno, grupo y materia
 * porque bachillerato no tiene carreras.
 */
return new class extends Migration
{
    /** Tablas y su PK (para colocar la columna nueva justo despues). */
    private const TABLAS = [
        'alumno'  => 'id_alumno',
        'docente' => 'id_docente',
        'grupo'   => 'id_grupo',
        'materia' => 'id_materia',
    ];

    public function up(): void
    {
        // 1. Agregar columna a las tablas principales.
        foreach (self::TABLAS as $tabla => $pk) {
            if (!Schema::hasTable($tabla)) continue;
            if (Schema::hasColumn($tabla, 'nivel_educativo')) continue;

            Schema::table($tabla, function (Blueprint $t) use ($pk) {
                $t->enum('nivel_educativo', ['universidad', 'bachillerato'])
                    ->default('universidad')
                    ->after($pk)
                    ->comment('Area operativa del registro');
                $t->index('nivel_educativo');
            });
        }

        // 2. Hacer id_carrera nullable en tablas donde bachillerato no aplica.
        //    Bachillerato no tiene "carrera" — los alumnos pertenecen a un grupo
        //    directamente y las materias son del plan de bachillerato.
        if (Schema::hasTable('alumno') && Schema::hasColumn('alumno', 'id_carrera')) {
            // MySQL requiere DBAL para change(), asi que usamos SQL directo.
            DB::statement('ALTER TABLE alumno MODIFY id_carrera BIGINT UNSIGNED NULL');
        }
        if (Schema::hasTable('grupo') && Schema::hasColumn('grupo', 'id_carrera')) {
            DB::statement('ALTER TABLE grupo MODIFY id_carrera BIGINT UNSIGNED NULL');
        }
        if (Schema::hasTable('materia') && Schema::hasColumn('materia', 'id_carrera')) {
            DB::statement('ALTER TABLE materia MODIFY id_carrera BIGINT UNSIGNED NULL');
        }
    }

    public function down(): void
    {
        // Antes de quitar la columna, asegurar que no quedan registros bachillerato
        // (la regresion no se hace cargo de migrar esos datos).
        foreach (self::TABLAS as $tabla => $pk) {
            if (!Schema::hasTable($tabla)) continue;
            if (!Schema::hasColumn($tabla, 'nivel_educativo')) continue;

            Schema::table($tabla, function (Blueprint $t) use ($tabla) {
                $t->dropIndex([$tabla . '_nivel_educativo_index']);
                $t->dropColumn('nivel_educativo');
            });
        }

        // Restaurar id_carrera NOT NULL.
        // (Si quedaran registros con NULL, esto fallara — es la senal de que
        // existian datos de bachillerato.)
        if (Schema::hasTable('alumno') && Schema::hasColumn('alumno', 'id_carrera')) {
            DB::statement('ALTER TABLE alumno MODIFY id_carrera BIGINT UNSIGNED NOT NULL');
        }
        if (Schema::hasTable('grupo') && Schema::hasColumn('grupo', 'id_carrera')) {
            DB::statement('ALTER TABLE grupo MODIFY id_carrera BIGINT UNSIGNED NOT NULL');
        }
        if (Schema::hasTable('materia') && Schema::hasColumn('materia', 'id_carrera')) {
            DB::statement('ALTER TABLE materia MODIFY id_carrera BIGINT UNSIGNED NOT NULL');
        }
    }
};
