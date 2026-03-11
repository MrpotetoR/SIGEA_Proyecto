<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asistencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumno_id')->constrained('alumnos');
            $table->foreignId('horario_id')->constrained('horarios');
            $table->date('fecha');
            $table->enum('estatus', ['presente', 'falta', 'justificada'])->default('presente');
            $table->timestamps();

            $table->unique(['alumno_id', 'horario_id', 'fecha']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asistencias');
    }
};
