<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grupos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('carrera_id')->constrained('carreras');
            $table->foreignId('ciclo_id')->constrained('ciclos_escolares');
            $table->tinyInteger('cuatrimestre');
            $table->string('clave_grupo'); // "A", "B", "1A-TIID"
            $table->foreignId('tutor_docente_id')->nullable()->constrained('docentes')->nullOnDelete();
            $table->timestamps();

            $table->unique(['carrera_id', 'ciclo_id', 'clave_grupo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grupos');
    }
};
