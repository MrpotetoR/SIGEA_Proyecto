<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('encuesta_preguntas', function (Blueprint $table) {
            $table->id();
            $table->text('texto_pregunta');
            $table->integer('orden')->default(0);
            $table->boolean('activa')->default(true);
            $table->timestamps();
        });

        Schema::create('evaluaciones_docente', function (Blueprint $table) {
            $table->id();
            $table->foreignId('docente_id')->constrained('docentes');
            $table->foreignId('alumno_id')->constrained('alumnos');
            $table->foreignId('materia_id')->constrained('materias');
            $table->foreignId('ciclo_id')->constrained('ciclos_escolares');
            $table->decimal('calificacion_promedio', 4, 2)->nullable();
            $table->text('comentarios')->nullable();
            $table->timestamps();

            $table->unique(['docente_id', 'alumno_id', 'materia_id', 'ciclo_id'], 'eval_doc_unique');
        });

        Schema::create('encuesta_respuestas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluacion_id')->constrained('evaluaciones_docente')->cascadeOnDelete();
            $table->foreignId('pregunta_id')->constrained('encuesta_preguntas');
            $table->tinyInteger('valor'); // 1-5
            $table->text('comentario')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('encuesta_respuestas');
        Schema::dropIfExists('evaluaciones_docente');
        Schema::dropIfExists('encuesta_preguntas');
    }
};
