<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alumnos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('carrera_id')->constrained('carreras');
            $table->string('matricula')->unique();
            $table->string('nombre');
            $table->string('apellidos');
            $table->tinyInteger('cuatrimestre_actual')->default(1);
            $table->enum('status', ['activo', 'baja_temporal', 'baja_definitiva', 'egresado'])->default('activo');
            $table->foreignId('tutor_id')->nullable()->constrained('tutores')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alumnos');
    }
};
