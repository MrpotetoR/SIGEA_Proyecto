<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Agrega el campo RVOE (Reconocimiento de Validez Oficial de Estudios)
 * a la tabla carrera.
 *
 * Es la clave de autorización oficial ante la SEP y es independiente
 * de la clave interna del sistema. Es opcional y permite caracteres
 * alfanuméricos con guion y diagonal.
 *
 * Ejemplo: "ESLI-2024/05-PE-09"
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carrera', function (Blueprint $t) {
            $t->string('rvoe', 50)
                ->nullable()
                ->after('clave_carrera')
                ->comment('Reconocimiento de Validez Oficial de Estudios (SEP). Opcional.');
        });
    }

    public function down(): void
    {
        Schema::table('carrera', function (Blueprint $t) {
            $t->dropColumn('rvoe');
        });
    }
};
