<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alumno', function (Blueprint $table) {
            $table->id('id_alumno');
            $table->foreignId('user_id')->constrained('users')->onUpdate('cascade')->onDelete('restrict');
            $table->unsignedBigInteger('id_carrera');
            $table->unsignedBigInteger('id_tutor')->nullable()->comment('Docente asignado como tutor');
            $table->string('matricula', 20)->unique()->comment('Generada automáticamente');
            $table->string('nombre', 80);
            $table->string('apellidos', 100);
            $table->integer('cuatrimestre_actual')->default(1);
            $table->enum('estatus', ['activo', 'baja_temporal', 'baja_definitiva'])->default('activo');

            $table->foreign('id_carrera')
                  ->references('id_carrera')->on('carrera')
                  ->onUpdate('cascade')->onDelete('restrict');

            $table->foreign('id_tutor')
                  ->references('id_docente')->on('docente')
                  ->onUpdate('cascade')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alumno');
    }
};
