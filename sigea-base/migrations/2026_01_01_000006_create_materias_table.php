<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('carrera_id')->constrained('carreras');
            $table->string('nombre_materia');
            $table->tinyInteger('cuatrimestre'); // en qué cuatrimestre se cursa
            $table->integer('horas_semana')->default(0);
            $table->integer('creditos')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materias');
    }
};
