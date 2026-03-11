<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('servicio_social', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumno_id')->constrained('alumnos');
            $table->decimal('horas_acumuladas', 6, 2)->default(0);
            $table->decimal('horas_requeridas', 6, 2)->default(480);
            $table->enum('estatus', ['en_proceso', 'completado', 'no_iniciado'])->default('no_iniciado');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('servicio_social');
    }
};
