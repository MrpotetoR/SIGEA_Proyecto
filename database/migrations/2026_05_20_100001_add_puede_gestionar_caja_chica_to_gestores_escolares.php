<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Agrega el flag puede_gestionar_caja_chica a la tabla gestores_escolares.
 *
 * Este permiso especial habilita a un Gestor Escolar para visualizar y
 * administrar la Caja Chica (Fondo de emergencia). Es una acción
 * administrativa sensible que requiere reauth de admin y deja huella en
 * caja_chica_log.
 *
 * Restricción de negocio (validada en código, no en DB):
 *   Máximo 3 gestores activos pueden tener puede_gestionar_caja_chica = true
 *   al mismo tiempo. La validación vive en PersonalController::store/update.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('gestores_escolares', function (Blueprint $table) {
            $table->boolean('puede_gestionar_caja_chica')
                  ->default(false)
                  ->after('puede_asignar_carreras')
                  ->comment('Permiso especial para administrar Caja Chica (máx 3 gestores)');
        });
    }

    public function down(): void
    {
        Schema::table('gestores_escolares', function (Blueprint $table) {
            $table->dropColumn('puede_gestionar_caja_chica');
        });
    }
};
