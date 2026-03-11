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
            $table->foreignId('user_id')->constrained('users')->onUpdate('cascade')->onDelete('restrict');
            $table->string('nombre', 80);
            $table->string('apellidos', 100);
            $table->string('especialidad', 100)->nullable();
            $table->integer('horas_contrato')->default(0);
            $table->boolean('es_tutor')->default(false);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('docente');
    }
};
