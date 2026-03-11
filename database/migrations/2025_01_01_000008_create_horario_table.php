<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('horario', function (Blueprint $table) {
            $table->id('id_horario');
            $table->unsignedBigInteger('id_docente');
            $table->unsignedBigInteger('id_grupo');
            $table->unsignedBigInteger('id_materia');
            $table->enum('dia_semana', ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado']);
            $table->time('hora_inicio');
            $table->time('hora_fin');

            $table->foreign('id_docente')
                  ->references('id_docente')->on('docente')
                  ->onUpdate('cascade')->onDelete('restrict');

            $table->foreign('id_grupo')
                  ->references('id_grupo')->on('grupo')
                  ->onUpdate('cascade')->onDelete('restrict');

            $table->foreign('id_materia')
                  ->references('id_materia')->on('materia')
                  ->onUpdate('cascade')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('horario');
    }
};
