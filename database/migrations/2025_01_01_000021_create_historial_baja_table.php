<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historial_baja', function (Blueprint $table) {
            $table->id('id_baja');
            $table->unsignedBigInteger('id_alumno');
            $table->foreignId('autorizada_por')->constrained('users')->onUpdate('cascade')->onDelete('restrict');
            $table->enum('tipo_baja', ['temporal', 'definitiva']);
            $table->date('fecha_baja');
            $table->date('fecha_reingreso')->nullable();
            $table->text('motivo');
            $table->timestamps();

            $table->foreign('id_alumno')
                  ->references('id_alumno')->on('alumno')
                  ->onUpdate('cascade')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historial_baja');
    }
};
