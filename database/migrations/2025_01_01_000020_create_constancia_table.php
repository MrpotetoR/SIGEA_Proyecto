<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('constancia', function (Blueprint $table) {
            $table->id('id_constancia');
            $table->unsignedBigInteger('id_alumno');
            $table->foreignId('generada_por')->constrained('users')->onUpdate('cascade')->onDelete('restrict');
            $table->enum('tipo', ['estudio', 'calificaciones', 'comportamiento', 'servicio_social', 'cultural']);
            $table->string('archivo_url', 500)->nullable();
            $table->date('fecha_emision');
            $table->timestamps();

            $table->foreign('id_alumno')
                  ->references('id_alumno')->on('alumno')
                  ->onUpdate('cascade')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('constancia');
    }
};
