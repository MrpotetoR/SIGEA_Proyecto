<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('semaforo_academico', function (Blueprint $table) {
            $table->id('id_semaforo');
            $table->unsignedBigInteger('id_alumno');
            $table->unsignedBigInteger('id_ciclo');
            $table->enum('nivel', ['verde', 'amarillo', 'rojo'])->default('verde');
            $table->decimal('promedio_calificaciones', 5, 2)->default(0.00);
            $table->decimal('porcentaje_asistencia', 5, 2)->default(100.00);

            $table->unique(['id_alumno', 'id_ciclo'], 'uq_semaforo');

            $table->foreign('id_alumno')
                  ->references('id_alumno')->on('alumno')
                  ->onUpdate('cascade')->onDelete('restrict');

            $table->foreign('id_ciclo')
                  ->references('id_ciclo')->on('ciclo_escolar')
                  ->onUpdate('cascade')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('semaforo_academico');
    }
};
