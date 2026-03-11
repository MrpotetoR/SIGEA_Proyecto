<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documento_institucional', function (Blueprint $table) {
            $table->id('id_documento');
            $table->foreignId('user_id')->constrained('users')->onUpdate('cascade')->onDelete('restrict');
            $table->string('titulo', 200);
            $table->string('tipo', 80);
            $table->string('archivo_url', 500);
            $table->date('fecha_publicacion');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documento_institucional');
    }
};
