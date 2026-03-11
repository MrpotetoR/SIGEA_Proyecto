<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hrs_culturales_deportivas', function (Blueprint $table) {
            $table->id('id_registro');
            $table->unsignedBigInteger('id_alumno');
            $table->enum('tipo', ['cultural', 'deportiva']);
            $table->decimal('horas_acumuladas', 6, 2)->default(0.00);
            $table->string('descripcion', 255)->nullable();

            $table->foreign('id_alumno')
                  ->references('id_alumno')->on('alumno')
                  ->onUpdate('cascade')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hrs_culturales_deportivas');
    }
};
