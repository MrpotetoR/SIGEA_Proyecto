<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Elimina la columna id_director (y su FK) de la tabla carrera.
 *
 * El concepto de "director de carrera" se retiró del sistema: toda la
 * administración la realiza el personal de Gestor Escolar. El rol
 * director_carrera ya había sido fusionado en gestor_escolar previamente
 * (ver 2026_05_06_000003_create_gestor_escolar_role.php).
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('carrera', function (Blueprint $table) {
            // Eliminar FK antes de soltar la columna.
            try {
                $table->dropForeign(['id_director']);
            } catch (\Throwable $e) {
                // FK ya no existe en este entorno.
            }
            if (Schema::hasColumn('carrera', 'id_director')) {
                $table->dropColumn('id_director');
            }
        });
    }

    public function down(): void
    {
        Schema::table('carrera', function (Blueprint $table) {
            $table->unsignedBigInteger('id_director')->nullable()->after('id_carrera');
            $table->foreign('id_director')
                  ->references('id_docente')->on('docente')
                  ->onUpdate('cascade')->onDelete('set null');
        });
    }
};
