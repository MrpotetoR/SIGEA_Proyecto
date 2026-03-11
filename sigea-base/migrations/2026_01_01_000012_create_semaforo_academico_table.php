<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('semaforo_academico', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumno_id')->constrained('alumnos');
            $table->foreignId('ciclo_id')->constrained('ciclos_escolares');
            $table->enum('nivel', ['verde', 'amarillo', 'rojo'])->default('verde');
            $table->decimal('promedio_calificaciones', 4, 2)->nullable();
            $table->decimal('promedio_asistencia', 5, 2)->nullable(); // porcentaje
            $table->timestamps();

            $table->unique(['alumno_id', 'ciclo_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('semaforo_academico');
    }
};
