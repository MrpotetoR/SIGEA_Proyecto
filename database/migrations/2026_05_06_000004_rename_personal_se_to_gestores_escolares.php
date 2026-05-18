<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * Renombra la tabla personal_servicios_escolares a gestores_escolares
 * para reflejar el nuevo rol unificado "Gestor Escolar".
 *
 * Las FKs que apuntan a esta tabla (en personal_carrera y documento_personal_se)
 * se preservan automáticamente por MySQL al hacer RENAME TABLE.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('personal_servicios_escolares')) {
            Schema::rename('personal_servicios_escolares', 'gestores_escolares');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('gestores_escolares')) {
            Schema::rename('gestores_escolares', 'personal_servicios_escolares');
        }
    }
};
