<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Elimina por completo el modulo de Actividades Culturales / ACUDE.
 *
 * Decision institucional UDEA: este modulo no formara parte del producto.
 * Se elimina la tabla hrs_culturales_deportivas y las migraciones
 * relacionadas dejan de aplicar. Tambien se preserva la posibilidad de
 * rollback recreando una tabla minima.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('hrs_culturales_deportivas');
    }

    public function down(): void
    {
        // Recreacion minima para permitir rollback. No restaura datos.
        Schema::create('hrs_culturales_deportivas', function (Blueprint $t) {
            $t->id('id_hrs');
            $t->foreignId('id_alumno')->constrained('alumno', 'id_alumno')->cascadeOnDelete();
            $t->string('tipo', 50)->nullable();
            $t->string('actividad', 150);
            $t->unsignedInteger('horas_acumuladas')->default(0);
            $t->date('fecha_registro')->nullable();
            $t->text('observaciones')->nullable();
            $t->timestamps();
        });
    }
};
