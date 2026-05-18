<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Agrega el flag puede_asignar_carreras a la tabla gestores_escolares.
 *
 * Este permiso especial habilita a un Gestor Escolar para asignar
 * carreras a otros gestores (acción administrativa sensible que
 * requiere reauth y registro de auditoría).
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('gestores_escolares', function (Blueprint $table) {
            $table->boolean('puede_asignar_carreras')
                  ->default(false)
                  ->after('especialidad')
                  ->comment('Permiso especial para asignar carreras a otros gestores');
        });
    }

    public function down(): void
    {
        Schema::table('gestores_escolares', function (Blueprint $table) {
            $table->dropColumn('puede_asignar_carreras');
        });
    }
};
