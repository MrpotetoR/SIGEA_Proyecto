<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('servicio_social', function (Blueprint $table) {
            $table->id('id_servicio');
            $table->unsignedBigInteger('id_alumno');
            $table->decimal('horas_acumuladas', 6, 2)->default(0.00);
            $table->decimal('horas_requeridas', 6, 2)->default(480.00);
            $table->enum('estatus', ['en_curso', 'completado'])->default('en_curso');

            $table->foreign('id_alumno')
                  ->references('id_alumno')->on('alumno')
                  ->onUpdate('cascade')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('servicio_social');
    }
};
