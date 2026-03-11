<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('encuesta_pregunta', function (Blueprint $table) {
            $table->id('id_pregunta');
            $table->string('texto_pregunta', 500);
            $table->integer('orden')->default(0);
            $table->boolean('activa')->default(true);
        });

        Schema::create('encuesta_respuesta', function (Blueprint $table) {
            $table->id('id_respuesta');
            $table->unsignedBigInteger('id_evaluacion');
            $table->unsignedBigInteger('id_pregunta');
            $table->integer('valor'); // 1-5
            $table->text('comentarios')->nullable();

            $table->foreign('id_evaluacion')
                  ->references('id_evaluacion')->on('evaluacion_docente')
                  ->onUpdate('cascade')->onDelete('cascade');

            $table->foreign('id_pregunta')
                  ->references('id_pregunta')->on('encuesta_pregunta')
                  ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('encuesta_respuesta');
        Schema::dropIfExists('encuesta_pregunta');
    }
};
