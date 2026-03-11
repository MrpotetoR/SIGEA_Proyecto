<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('constancias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumno_id')->constrained('alumnos');
            $table->enum('tipo', ['kardex', 'boleta', 'constancia_estudios', 'constancia_inscripcion', 'otro']);
            $table->string('archivo_url')->nullable();
            $table->date('fecha_emision');
            $table->foreignId('generada_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('historial_bajas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumno_id')->constrained('alumnos');
            $table->foreignId('autorizada_por')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('tipo_baja', ['temporal', 'definitiva']);
            $table->date('fecha_baja');
            $table->date('fecha_reingreso')->nullable();
            $table->text('motivo');
            $table->timestamps();
        });

        Schema::create('chatbot_sesiones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->datetime('fecha_hora');
            $table->text('pregunta');
            $table->text('respuesta');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chatbot_sesiones');
        Schema::dropIfExists('historial_bajas');
        Schema::dropIfExists('constancias');
    }
};
