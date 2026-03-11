<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materia', function (Blueprint $table) {
            $table->id('id_materia');
            $table->unsignedBigInteger('id_carrera');
            $table->string('nombre_materia', 120);
            $table->integer('cuatrimestre');
            $table->integer('horas_semana')->default(0);

            $table->foreign('id_carrera')
                  ->references('id_carrera')->on('carrera')
                  ->onUpdate('cascade')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materia');
    }
};
