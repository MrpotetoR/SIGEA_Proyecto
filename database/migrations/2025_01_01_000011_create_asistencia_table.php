<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asistencia', function (Blueprint $table) {
            $table->id('id_asistencia');
            $table->unsignedBigInteger('id_alumno');
            $table->unsignedBigInteger('id_horario');
            $table->date('fecha');
            $table->enum('estatus', ['presente', 'ausente', 'justificada'])->default('presente');

            $table->unique(['id_alumno', 'id_horario', 'fecha'], 'uq_asistencia');

            $table->foreign('id_alumno')
                  ->references('id_alumno')->on('alumno')
                  ->onUpdate('cascade')->onDelete('restrict');

            $table->foreign('id_horario')
                  ->references('id_horario')->on('horario')
                  ->onUpdate('cascade')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asistencia');
    }
};
