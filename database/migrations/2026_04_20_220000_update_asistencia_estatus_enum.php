<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Estandariza los estados de asistencia a 3 valores: presente, ausente, retardo.
 * Convierte los registros existentes de 'justificada' a 'retardo' (el valor más cercano
 * semánticamente: llegada/presencia parcial con justificación).
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1. Ampliar enum para aceptar temporalmente ambos valores.
        DB::statement("ALTER TABLE asistencia MODIFY estatus ENUM('presente','ausente','justificada','retardo') NOT NULL DEFAULT 'presente'");

        // 2. Migrar datos existentes.
        DB::table('asistencia')->where('estatus', 'justificada')->update(['estatus' => 'retardo']);

        // 3. Reducir enum al set final.
        DB::statement("ALTER TABLE asistencia MODIFY estatus ENUM('presente','ausente','retardo') NOT NULL DEFAULT 'presente'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE asistencia MODIFY estatus ENUM('presente','ausente','justificada','retardo') NOT NULL DEFAULT 'presente'");
        DB::table('asistencia')->where('estatus', 'retardo')->update(['estatus' => 'justificada']);
        DB::statement("ALTER TABLE asistencia MODIFY estatus ENUM('presente','ausente','justificada') NOT NULL DEFAULT 'presente'");
    }
};
