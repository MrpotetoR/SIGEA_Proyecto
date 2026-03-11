<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calificaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumno_id')->constrained('alumnos');
            $table->foreignId('materia_id')->constrained('materias');
            $table->foreignId('ciclo_id')->constrained('ciclos_escolares');
            $table->foreignId('docente_id')->nullable()->constrained('docentes')->nullOnDelete();
            $table->tinyInteger('parcial'); // 1, 2, 3
            $table->decimal('calificacion', 4, 2)->nullable();
            $table->timestamps();

            $table->unique(['alumno_id', 'materia_id', 'ciclo_id', 'parcial'], 'calif_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calificaciones');
    }
};
