<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('docente', function (Blueprint $table) {
            $table->id('id_docente');
            $table->unsignedBigInteger('id_usuario');
            $table->string('nombre', 80);
            $table->string('apellidos', 100);
            $table->string('especialidad', 100)->nullable();
            $table->integer('horas_contrato')->default(0);
            $table->boolean('es_tutor')->default(false);

            $table->foreign('id_usuario')
                  ->references('id_usuario')->on('usuario')
                  ->onUpdate('cascade')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('docente');
    }
};
