<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Corrección de regla de negocio: las carreras semestrales en UDEA duran
 * exactamente 6 semestres (3 años calendario), no 7 como se tenía previamente.
 *
 * Aplica a las licenciaturas del área de Ciencias de la Salud:
 *   - Enfermería, Nutrición, Psicología, Estomatología, etc.
 *
 * Esta migración actualiza las carreras semestrales existentes que estaban
 * registradas con 7 periodos para alinearlas con la regla correcta (6).
 *
 * Las carreras cuatrimestrales no se ven afectadas (siguen con 10).
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('carrera')
            ->where('tipo_periodo', 'semestre')
            ->where('duracion_periodos', 7)
            ->update(['duracion_periodos' => 6]);
    }

    public function down(): void
    {
        // Reverso: regresar a 7 por si fuera necesario hacer rollback.
        DB::table('carrera')
            ->where('tipo_periodo', 'semestre')
            ->where('duracion_periodos', 6)
            ->update(['duracion_periodos' => 7]);
    }
};
