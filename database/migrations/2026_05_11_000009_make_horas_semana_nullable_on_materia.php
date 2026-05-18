<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Permite que `horas_semana` sea NULL en la tabla `materia`.
 *
 * Motivo: en muchos casos al dar de alta una materia se desconoce todavia
 * la carga horaria exacta. Dejar el campo opcional evita capturar "0"
 * como dato ficticio.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('materia', function (Blueprint $t) {
            $t->integer('horas_semana')->nullable()->default(null)->change();
        });
    }

    public function down(): void
    {
        Schema::table('materia', function (Blueprint $t) {
            $t->integer('horas_semana')->nullable(false)->default(0)->change();
        });
    }
};
