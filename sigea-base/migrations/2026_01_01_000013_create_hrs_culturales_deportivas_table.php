<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hrs_culturales_deportivas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumno_id')->constrained('alumnos');
            $table->foreignId('validado_por')->nullable()->constrained('docentes')->nullOnDelete();
            $table->enum('tipo', ['cultural', 'deportiva']);
            $table->decimal('horas', 5, 2)->default(0);
            $table->date('fecha_actividad');
            $table->string('descripcion')->nullable();
            $table->boolean('validado')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hrs_culturales_deportivas');
    }
};
