<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calificacion', function (Blueprint $table) {
            $table->id('id_calificacion');
            $table->unsignedBigInteger('id_alumno');
            $table->unsignedBigInteger('id_materia');
            $table->unsignedBigInteger('id_ciclo');
            $table->integer('parcial')->comment('1, 2 o 3');
            $table->decimal('calificacion', 5, 2);

            $table->unique(['id_alumno', 'id_materia', 'id_ciclo', 'parcial'], 'uq_calificacion');

            $table->foreign('id_alumno')
                  ->references('id_alumno')->on('alumno')
                  ->onUpdate('cascade')->onDelete('restrict');

            $table->foreign('id_materia')
                  ->references('id_materia')->on('materia')
                  ->onUpdate('cascade')->onDelete('restrict');

            $table->foreign('id_ciclo')
                  ->references('id_ciclo')->on('ciclo_escolar')
                  ->onUpdate('cascade')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calificacion');
    }
};
