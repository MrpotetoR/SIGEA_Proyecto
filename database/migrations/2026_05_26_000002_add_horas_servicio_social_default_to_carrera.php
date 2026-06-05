<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Agrega el campo `horas_servicio_social_default` a la tabla `carrera`.
 *
 * Permite definir las horas requeridas de servicio social a nivel de carrera,
 * en lugar de hardcodearlas. El gestor puede sobreescribir caso por caso al
 * registrar el SS de un alumno (ej. Nutrición hospitalario vs básico).
 *
 * Defaults aplicados según normativa UDEA + SEP/Salud:
 *   - Enfermería        : 960 h (1 año, jornadas de 4h/día)
 *   - Estomatología     : 960 h (mínimo, puede llegar a 1200)
 *   - Otras carreras    : 480 h (mínimo legal SEP)
 *
 * Carreras del área de Ciencias de la Salud que requieren 960+ se detectan
 * por palabras clave en el nombre. El gestor puede ajustar manualmente
 * después de la migración si hay casos especiales.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carrera', function (Blueprint $table) {
            $table->unsignedSmallInteger('horas_servicio_social_default')
                  ->default(480)
                  ->after('duracion_periodos')
                  ->comment('Horas requeridas de servicio social por defecto para la carrera');
        });

        // Aplicar defaults inteligentes a carreras existentes según nombre.
        // 960 h para carreras clínicas estrictas (Enfermería, Estomatología/Odontología).
        DB::table('carrera')
            ->where(function ($q) {
                $q->where('nombre_carrera', 'like', '%nfermer%')   // Enfermería, Enfermeria
                  ->orWhere('nombre_carrera', 'like', '%stomatolog%') // Estomatología
                  ->orWhere('nombre_carrera', 'like', '%dontolog%');  // Odontología
            })
            ->update(['horas_servicio_social_default' => 960]);

        // El resto se queda en 480 (default de la columna).
    }

    public function down(): void
    {
        Schema::table('carrera', function (Blueprint $table) {
            $table->dropColumn('horas_servicio_social_default');
        });
    }
};
