<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('noticias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('autor_id')->constrained('users');
            $table->string('titulo');
            $table->text('contenido');
            $table->datetime('fecha_publicacion');
            $table->boolean('activa')->default(true);
            $table->timestamps();
        });

        Schema::create('documentos_institucionales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('autor_id')->constrained('users');
            $table->string('titulo');
            $table->string('tipo'); // reglamento, formato, circular
            $table->string('archivo_url');
            $table->datetime('fecha_publicacion');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentos_institucionales');
        Schema::dropIfExists('noticias');
    }
};
