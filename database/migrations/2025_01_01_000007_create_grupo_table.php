<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grupo', function (Blueprint $table) {
            $table->id('id_grupo');
            $table->unsignedBigInteger('id_carrera');
            $table->unsignedBigInteger('id_ciclo');
            $table->unsignedBigInteger('id_tutor')->nullable();
            $table->integer('cuatrimestre');
            $table->string('clave_grupo', 20)->unique()->comment('Ej: 5A_DSM');

            $table->foreign('id_carrera')
                  ->references('id_carrera')->on('carrera')
                  ->onUpdate('cascade')->onDelete('restrict');

            $table->foreign('id_ciclo')
                  ->references('id_ciclo')->on('ciclo_escolar')
                  ->onUpdate('cascade')->onDelete('restrict');

            $table->foreign('id_tutor')
                  ->references('id_docente')->on('docente')
                  ->onUpdate('cascade')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grupo');
    }
};
