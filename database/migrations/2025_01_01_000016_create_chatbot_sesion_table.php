<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chatbot_sesion', function (Blueprint $table) {
            $table->id('id_sesion');
            $table->unsignedBigInteger('id_usuario');
            $table->dateTime('fecha_hora')->useCurrent();
            $table->text('pregunta');
            $table->text('respuesta');

            $table->foreign('id_usuario')
                  ->references('id_usuario')->on('usuario')
                  ->onUpdate('cascade')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chatbot_sesion');
    }
};
