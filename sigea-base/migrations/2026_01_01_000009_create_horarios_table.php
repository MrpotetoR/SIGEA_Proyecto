<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('horarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('docente_id')->constrained('docentes');
            $table->foreignId('grupo_id')->constrained('grupos');
            $table->foreignId('materia_id')->constrained('materias');
            $table->enum('dia_semana', ['lunes', 'martes', 'miercoles', 'jueves', 'viernes']);
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('horarios');
    }
};
