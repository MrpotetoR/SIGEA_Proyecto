<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('docente_carrera', function (Blueprint $table) {
            $table->unsignedBigInteger('id_docente');
            $table->unsignedBigInteger('id_carrera');

            $table->primary(['id_docente', 'id_carrera']);

            $table->foreign('id_docente')
                  ->references('id_docente')->on('docente')
                  ->onDelete('cascade');

            $table->foreign('id_carrera')
                  ->references('id_carrera')->on('carrera')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('docente_carrera');
    }
};
