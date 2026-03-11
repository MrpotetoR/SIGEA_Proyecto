<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluacion_docente', function (Blueprint $table) {
            $table->id('id_evaluacion');
            $table->unsignedBigInteger('id_docente');
            $table->unsignedBigInteger('id_alumno');
            $table->unsignedBigInteger('id_ciclo');
            $table->decimal('calificacion_promedio', 4, 2);
            $table->text('comentarios')->nullable();

            $table->unique(['id_docente', 'id_alumno', 'id_ciclo'], 'uq_evaluacion');

            $table->foreign('id_docente')
                  ->references('id_docente')->on('docente')
                  ->onUpdate('cascade')->onDelete('restrict');

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
        Schema::dropIfExists('evaluacion_docente');
    }
};
